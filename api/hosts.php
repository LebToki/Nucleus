<?php
/**
 * Nucleus - Hosts File API
 * Version: 1.0.0
 * Description: Manage /etc/hosts entries (pretty URLs) on Linux
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

$HOSTS_FILE = '/etc/hosts';
$BACKUP_FILE = '/etc/hosts.bak.nucleus';

/**
 * Read and parse the hosts file
 */
function readHostsFile() {
    global $HOSTS_FILE;
    if (!is_readable($HOSTS_FILE)) {
        return null;
    }
    return file_get_contents($HOSTS_FILE);
}

/**
 * Write the hosts file (requires root; uses sudo -n tee with backup)
 */
function writeHostsFile($content) {
    global $HOSTS_FILE, $BACKUP_FILE;

    // Ensure content ends with newline
    if (substr($content, -1) !== "\n") {
        $content .= "\n";
    }

    // Try to back up first
    $backupCommand = 'sudo -n cp ' . escapeshellarg($HOSTS_FILE) . ' ' . escapeshellarg($BACKUP_FILE) . ' 2>&1';
    @exec($backupCommand, $backupOut, $backupCode);

    // Write via sudo tee
    $tmpFile = sys_get_temp_dir() . '/nucleus-hosts-' . getmypid() . '.tmp';
    if (file_put_contents($tmpFile, $content) === false) {
        return ['success' => false, 'error' => 'Could not write temporary file'];
    }
    @chmod($tmpFile, 0644);

    $command = 'sudo -n tee ' . escapeshellarg($HOSTS_FILE) . ' < ' . escapeshellarg($tmpFile) . ' > /dev/null 2>&1';
    @exec($command, $out, $code);
    @unlink($tmpFile);

    if ($code !== 0) {
        $hint = "www-data needs a passwordless sudo rule. As root, run:\n"
            . "  echo 'www-data ALL=(root) NOPASSWD: /usr/bin/cp /etc/hosts /etc/hosts.bak.nucleus, /usr/bin/tee /etc/hosts' > /etc/sudoers.d/99-nucleus-hosts\n"
            . "  chmod 440 /etc/sudoers.d/99-nucleus-hosts";
        return ['success' => false, 'error' => 'Failed to write /etc/hosts (needs root). ' . $hint];
    }

    return ['success' => true, 'backup' => file_exists($BACKUP_FILE)];
}

/**
 * Parse /etc/hosts into structured entries
 */
function parseHostsFile($content) {
    $entries = [];
    $lines = explode("\n", $content);
    foreach ($lines as $index => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' ) {
            $entries[] = ['index' => $index, 'type' => 'blank'];
            continue;
        }
        if (strpos($trimmed, '#') === 0) {
            $entries[] = ['index' => $index, 'type' => 'comment', 'text' => $trimmed];
            continue;
        }
        // Strip inline comments
        $body = preg_replace('/\s+#.*$/', '', $trimmed);
        $parts = preg_split('/\s+/', $body);
        if (count($parts) >= 2 && filter_var($parts[0], FILTER_VALIDATE_IP)) {
            $entries[] = [
                'index' => $index,
                'type' => 'entry',
                'ip' => $parts[0],
                'hosts' => array_slice($parts, 1),
                'name' => $parts[1],
            ];
        } else {
            $entries[] = ['index' => $index, 'type' => 'entry', 'raw' => $trimmed];
        }
    }
    return $entries;
}

$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
        case 'read':
            $content = readHostsFile();
            if ($content === null) {
                throw new Exception('Cannot read /etc/hosts');
            }
            ob_clean();
            echo json_encode([
                'success' => true,
                'data' => [
                    'content' => $content,
                    'entries' => parseHostsFile($content),
                    'writable' => is_writable($HOSTS_FILE) || isSudoAvailable(),
                ],
            ]);
            break;

        case 'add':
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('CSRF token validation failed');
            }
            $ip = trim($_POST['ip'] ?? '');
            $hosts = trim($_POST['hosts'] ?? '');
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new Exception('Invalid IP address');
            }
            if (empty($hosts)) {
                throw new Exception('At least one hostname required');
            }
            if (!preg_match('/^[a-zA-Z0-9.\- _]+$/', $hosts)) {
                throw new Exception('Invalid hostname(s)');
            }

            $content = readHostsFile();
            if ($content === null) {
                throw new Exception('Cannot read /etc/hosts');
            }
            if (strpos($content, $hosts) !== false) {
                throw new Exception('This host already exists in /etc/hosts');
            }

            $newLine = $ip . '    ' . str_replace(' ', '  ', $hosts) . "\n";
            if (substr($content, -1) !== "\n") {
                $content .= "\n";
            }
            $result = writeHostsFile($content . $newLine);
            if (!$result['success']) {
                throw new Exception($result['error']);
            }
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Host entry added', 'data' => $result]);
            break;

        case 'toggle':
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('CSRF token validation failed');
            }
            $index = intval($_POST['index'] ?? -1);
            $content = readHostsFile();
            $lines = explode("\n", $content);
            if (!isset($lines[$index]) || trim($lines[$index]) === '') {
                throw new Exception('Invalid line index');
            }
            $line = $lines[$index];
            if (strpos(ltrim($line), '#') === 0) {
                $lines[$index] = preg_replace('/^#\s*/', '', $line);
            } else {
                $lines[$index] = '# ' . $line;
            }
            $result = writeHostsFile(implode("\n", $lines));
            if (!$result['success']) {
                throw new Exception($result['error']);
            }
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Host entry toggled']);
            break;

        case 'remove':
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('CSRF token validation failed');
            }
            $index = intval($_POST['index'] ?? -1);
            $content = readHostsFile();
            $lines = explode("\n", $content);
            if (!isset($lines[$index])) {
                throw new Exception('Invalid line index');
            }
            $lines[$index] = '# Nucleus disabled: ' . ltrim($lines[$index], '# ');
            $result = writeHostsFile(implode("\n", $lines));
            if (!$result['success']) {
                throw new Exception($result['error']);
            }
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Host entry disabled']);
            break;

        case 'save':
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('CSRF token validation failed');
            }
            $content = $_POST['content'] ?? null;
            if ($content === null) {
                throw new Exception('No content provided');
            }
            if (strlen($content) > 65536) {
                throw new Exception('Content too large');
            }
            $result = writeHostsFile($content);
            if (!$result['success']) {
                throw new Exception($result['error']);
            }
            ob_clean();
            echo json_encode(['success' => true, 'message' => '/etc/hosts saved successfully']);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (\Throwable $e) {
    \Nucleus\Core\Logger::error('API hosts.php error: ' . $e->getMessage());
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();

function isSudoAvailable() {
    $out = @shell_exec('sudo -n true 2>&1');
    return trim((string)$out) === '';
}
