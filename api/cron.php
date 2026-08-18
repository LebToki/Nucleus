<?php
/**
 * Nucleus - Cron API
 * Version: 1.0.0
 * Description: Read-only view of scheduled cron jobs.
 *   - GET ?action=list -> list cron entries (user crontab + /etc/crontab + /etc/cron.d)
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

function cronParseLines(array $lines, string $source): array {
    $entries = [];
    foreach ($lines as $raw) {
        $line = trim($raw);
        if ($line === '' || isset($line[0]) && $line[0] === '#') continue;
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*\s*=/', $line)) continue; // env lines (MAILTO=, PATH=, SHELL=)
        if (preg_match('/^@(reboot|yearly|annually|monthly|weekly|daily|midnight|hourly)\s+(.+)$/i', $line, $m)) {
            $entries[] = ['schedule' => $m[1], 'run_as' => null, 'command' => trim($m[2]), 'source' => $source];
            continue;
        }
        $parts = preg_split('/\s+/', $line);
        if (count($parts) < 6) continue;
        $fields = array_slice($parts, 0, 5);
        $valid = true;
        foreach ($fields as $f) {
            if (!preg_match('/^[0-9*\/,\-?A-Za-z]+$/', $f)) { $valid = false; break; }
        }
        if (!$valid) continue;
        $rest = array_slice($parts, 5);
        $runAs = null;
        if (count($rest) > 1 && preg_match('/^[a-z_][a-z0-9_-]*$/', $rest[0])) { // system crontab has a run-as user
            $runAs = $rest[0];
            $rest = array_slice($rest, 1);
        }
        if (count($rest) === 0) continue;
        $entries[] = ['schedule' => implode(' ', $fields), 'run_as' => $runAs, 'command' => implode(' ', $rest), 'source' => $source];
    }
    return $entries;
}

function cronCollect(): array {
    $entries = [];
    $user = '';
    if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
        $pw = posix_getpwuid(posix_geteuid());
        $user = (string)($pw['name'] ?? '');
    }

    $out = trim((string)@shell_exec('crontab -l 2>/dev/null'));
    if ($out !== '') {
        $entries = array_merge($entries, cronParseLines(explode("\n", $out), $user !== '' ? 'crontab (' . $user . ')' : 'crontab'));
    }

    if (is_readable('/etc/crontab')) {
        $content = @file_get_contents('/etc/crontab');
        if ($content !== false) {
            $entries = array_merge($entries, cronParseLines(explode("\n", $content), '/etc/crontab'));
        }
    }

    $cronDirs = @glob('/etc/cron.d/*');
    if (is_array($cronDirs)) {
        foreach ($cronDirs as $file) {
            $content = @file_get_contents($file);
            if ($content === false) continue;
            $entries = array_merge($entries, cronParseLines(explode("\n", $content), '/etc/cron.d/' . basename($file)));
        }
    }

    return $entries;
}

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';

    switch ($action) {
        case 'list':
            $entries = cronCollect();
            $sources = array_values(array_unique(array_column($entries, 'source')));
            echo json_encode([
                'success' => true,
                'total'   => count($entries),
                'sources' => $sources,
                'entries' => $entries,
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
