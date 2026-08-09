<?php
/**
 * Nucleus - Refresh Vhosts API
 * Version: 1.0.0
 * Description: Scans the web root for project directories, generates a
 *              <name>.local Apache vhost + hosts entry for each (unless a
 *              service vhost already exists), then reloads Apache so newly
 *              added projects/directories get a pretty-URL automatically.
 */

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (function_exists('check_auth')) {
    check_auth();
}

ob_clean();
header('Content-Type: application/json');

// CSRF check
$token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
if (!verifyCSRFToken($token)) {
    ob_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
    ob_end_flush();
    exit;
}

/**
 * Directories that are Nucleus infrastructure, not projects.
 */
function getInfrastructureDirs(): array {
    return [
        'laragon', 'laragon-dashboard', 'dashboard', 'assets', 'build',
        'includes', 'partials', 'pages', 'api', 'i18n', 'cache', 'data', 'logs',
        'phpmyadmin', 'adminer', 'phppgadmin', 'html', 'nucleus-logo',
        'node_modules', 'vendor', '.git', 'backups', 'temp',
    ];
}

/**
 * Resolve the web root where projects live.
 */
function resolveWebRoot(): string {
    if (class_exists('\Nucleus\Core\System')) {
        $www = \Nucleus\Core\System::getWwwPath();
        if (is_dir($www)) {
            return $www;
        }
    }
    return '/var/www/html';
}

/**
 * List top-level directories in the web root that should be treated as projects.
 */
function discoverProjectDirs(string $webRoot): array {
    $excluded = getInfrastructureDirs();
    $dirs = [];
    foreach (glob($webRoot . '/*', GLOB_ONLYDIR) as $dir) {
        $name = basename($dir);
        if ($name[0] === '.') continue;
        if (in_array($name, $excluded, true)) continue;
        $dirs[] = $name;
    }
    sort($dirs);
    return $dirs;
}

/**
 * Determine the DocumentRoot for a project dir (Laravel -> /public).
 */
function docRootFor(string $webRoot, string $name): string {
    $path = $webRoot . '/' . $name;
    if (file_exists($path . '/artisan') && is_dir($path . '/public')) {
        return $path . '/public';
    }
    return $path;
}

/**
 * Existing enabled vhost names (excluding default infrastructure hosts).
 * Returns names WITHOUT the .local suffix for comparison against project dirs.
 */
function existingVhostNames(): array {
    $names = [];
    foreach (glob('/etc/apache2/sites-enabled/*.conf') as $file) {
        $base = basename($file, '.conf');
        // Skip the auto-generated infrastructure / default files
        if (preg_match('/^(000-|\.)/', $base)) continue;
        $name = str_replace(['_ssl', '-ssl'], '', $base);
        // Strip one single ".local" (or ".test") suffix and "www." prefix
        $name = preg_replace('/\.(local|test)$/i', '', $name);
        $name = preg_replace('/^www\./i', '', $name);
        if ($name !== '') {
            $names[] = $name;
        }
    }
    return array_unique($names);
}

/**
 * Ensure a 127.0.0.1 <name>.local hosts entry exists.
 */
function ensureHostEntry(string $domain): bool {
    $HOSTS_FILE = '/etc/hosts';
    $content = @file_get_contents($HOSTS_FILE);
    if ($content === false) return false;
    if (stripos($content, $domain) !== false) return true;

    $line = "127.0.0.1\t{$domain}\n";
    $tmpFile = sys_get_temp_dir() . '/nucleus-hosts-' . getmypid() . '.tmp';
    if (@file_put_contents($tmpFile, $line) === false) return false;
    @chmod($tmpFile, 0644);
    $command = 'sudo -n tee -a ' . escapeshellarg($HOSTS_FILE) . ' < ' . escapeshellarg($tmpFile) . ' > /dev/null 2>&1';
    @exec($command, $out, $code);
    @unlink($tmpFile);
    return $code === 0;
}

/**
 * Write an explicit HTTP vhost file directly into sites-enabled.
 */
function writeVhostFile(string $name, string $webRoot): bool {
    $domain = $name . '.local';
    $docRoot = docRootFor($webRoot, $name);
    $escapedRoot = addcslashes($docRoot, '"');

    $content = "# Nucleus auto-generated vhost for {$name}\n"
        . "<VirtualHost *:80>\n"
        . "    ServerName {$domain}\n"
        . "    DocumentRoot \"{$escapedRoot}\"\n"
        . "    <Directory \"{$escapedRoot}\">\n"
        . "        Options Indexes FollowSymLinks\n"
        . "        AllowOverride All\n"
        . "        Require all granted\n"
        . "    </Directory>\n"
        . "    ErrorLog \${APACHE_LOG_DIR}/{$name}-error.log\n"
        . "    CustomLog \${APACHE_LOG_DIR}/{$name}-access.log combined\n"
        . "</VirtualHost>\n";

    $file = '/etc/apache2/sites-enabled/' . $domain . '.conf';
    $tmp = sys_get_temp_dir() . '/nucleus-vhost-' . getmypid() . '.conf';
    if (@file_put_contents($tmp, $content) === false) {
        return false;
    }
    @chmod($tmp, 0644);
    $command = 'sudo -n tee ' . escapeshellarg($file) . ' < ' . escapeshellarg($tmp) . ' > /dev/null 2>&1';
    @exec($command, $out, $code);
    @unlink($tmp);
    return $code === 0;
}

/**
 * Test + reload Apache (graceful first, full restart as fallback).
 */
function refreshApache(): array {
    @exec('sudo -n systemctl reload apache2 2>&1', $reloadOut, $reloadCode);
    if ($reloadCode === 0) {
        return ['method' => 'reload', 'success' => true];
    }
    @exec('sudo -n systemctl restart apache2 2>&1', $restartOut, $restartCode);
    if ($restartCode === 0) {
        return ['method' => 'restart', 'success' => true];
    }
    return ['method' => 'restart', 'success' => false, 'error' => trim(implode("\n", $restartOut)) ?: 'Failed to reload or restart Apache'];
}

try {
    $webRoot = resolveWebRoot();
    $projects = discoverProjectDirs($webRoot);
    $existing = existingVhostNames();

    $created = [];
    $already = [];
    $skippedInfra = getInfrastructureDirs();

    foreach ($projects as $name) {
        $domain = $name . '.local';

        // Skip if a service/explicit vhost already serves this host
        if (in_array($name, $existing, true)) {
            $already[] = $name;
            // Still ensure the hosts entry resolves
            ensureHostEntry($domain);
            continue;
        }
        $okVhost = writeVhostFile($name, $webRoot);
        $okHost = ensureHostEntry($domain);
        $created[] = [
            'domain' => $domain,
            'vhost_written' => $okVhost,
            'host_added' => $okHost,
        ];
    }

    $apache = refreshApache();

    $success = $apache['success'] ?? false;
    echo json_encode([
        'success' => $success,
        'message' => $success
            ? 'Apache ' . ($apache['method'] === 'reload' ? 'reloaded' : 'restarted') . ' — ' . count($created) . ' vhost(s) created, ' . count($already) . ' already handled'
            : 'Refresh incomplete: ' . ($apache['error'] ?? 'Unknown error'),
        'web_root' => $webRoot,
        'projects' => $projects,
        'created' => $created,
        'already' => $already,
        'apache' => $apache,
    ]);
} catch (Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();