<?php
/**
 * Nucleus - Fix SMTP Configuration and Connectivity Manager
 * Supports Sendmail, Mailpit, and Postfix configurations.
 * Version: 3.2.1 // Updated to support Nucleus structure
 */

header('Content-Type: application/json');

// Load configuration
require_once __DIR__ . '/../config.php';

$action = $_GET['action'] ?? 'check';

/**
 * Determine the Nucleus root directory reliably.
 */
function getNucleusRootDir() {
    // Check for defined global constant (best practice)
    if (defined('NUCLEUS_ROOT')) {
        return NUCLEUS_ROOT;
    }
    // Fallback: Assume project root is two directories up from the current file location.
    return dirname(__DIR__, 2);
}

/**
 * Find PHP php.ini file.
 * - Windows/Laragon: traverses the presumed vendor/php directory structure
 * - Linux: uses php_ini_loaded_file(), then scans /etc/php (apache2/cli variants)
 */
function findPhpIni() {
    $root = getNucleusRootDir();
    if (!empty($root) && is_dir($root)) {
        // Laragon-style structure (Windows)
        $vendorPhpPath = $root . '/vendor/php/';
        if (is_dir($vendorPhpPath)) {
            $phpDirs = glob($vendorPhpPath . 'php-*');
            if (!empty($phpDirs)) {
                rsort($phpDirs);
                $iniPath = $phpDirs[0] . '/php.ini';
                if (file_exists($iniPath)) {
                    return $iniPath;
                }
                $iniPathFallback = $vendorPhpPath . 'php.ini';
                if (file_exists($iniPathFallback)) {
                    return $iniPathFallback;
                }
            }
        }
    }

    // Linux: php.ini actually loaded by PHP (most reliable)
    if (function_exists('php_ini_loaded_file')) {
        $loaded = php_ini_loaded_file();
        if (!empty($loaded) && file_exists($loaded)) {
            return $loaded;
        }
    }

    // Linux: scan /etc/php/<ver>/{apache2,cli}/php.ini
    $apacheIni = glob('/etc/php/*/apache2/php.ini');
    if (!empty($apacheIni)) {
        rsort($apacheIni);
        return $apacheIni[0];
    }
    $cliIni = glob('/etc/php/*/cli/php.ini');
    if (!empty($cliIni)) {
        rsort($cliIni);
        return $cliIni[0];
    }

    return null;
}

/**
 * Get current SMTP configuration from php.ini by regex matching.
 */
function getCurrentSmtpConfig($iniPath) {
    if (!file_exists($iniPath)) {
        return null;
    }
    
    $content = file_get_contents($iniPath);
    $config = [
        'smtp' => null,
        'smtp_port' => null,
        'sendmail_from' => null,
        'sendmail_path' => null
    ];
    
    // Parse SMTP settings using regex groups and non-capturing logic
    if (preg_match('/^smtp\s*=\s*(.+)$/mi', $content, $matches)) {
        $config['smtp'] = trim($matches[1]);
    }
    if (preg_match('/^smtp_port\s*=\s*(.+)$/mi', $content, $matches)) {
        $config['smtp_port'] = trim($matches[1]);
    }

    // Existing sendmail settings
    if (preg_match('/^sendmail_from\s*=\s*(.+)$/mi', $content, $matches)) {
        $config['sendmail_from'] = trim($matches[1]);
    }
    if (preg_match('/^sendmail_path\s*=\s*(.+)$/mi', $content, $matches)) {
        $config['sendmail_path'] = trim($matches[1]);
    }
    
    return $config;
}

/**
 * Detect the current mail transport agent from sendmail_path.
 * Returns 'mailpit', 'postfix', or 'sendmail'.
 */
function detectMta($config) {
    $sendmailPath = $config['sendmail_path'] ?? '';
    if (stripos($sendmailPath, 'mailpit') !== false) {
        return 'mailpit';
    }
    // Postfix provides /usr/sbin/sendmail on Debian/Ubuntu
    if (stripos($sendmailPath, '/usr/sbin/sendmail') !== false || stripos($sendmailPath, 'postfix') !== false) {
        return 'postfix';
    }
    return 'sendmail';
}

/**
 * Resolve the sendmail binary for a given MTA mode.
 */
function resolveSendmailBinary($mode) {
    if ($mode === 'mailpit') {
        // Prefer a system-wide install, fall back to user-scope binary
        foreach (['/usr/local/bin/mailpit', getenv('HOME') . '/.local/bin/mailpit', '/var/www/.local/bin/mailpit'] as $candidate) {
            if (is_file($candidate) && is_executable($candidate)) {
                return $candidate . ' sendmail';
            }
        }
        return null;
    }
    return '/usr/sbin/sendmail -t -i';
}

/**
 * Configure PHP mail() for the selected MTA by modifying php.ini.
 * @param string $iniPath Path to php.ini
 * @param string $mode 'mailpit' | 'postfix' | 'sendmail'
 * @param int $smtpPort Mailpit SMTP port (mailpit mode only)
 * @param string $fromEmail Default sender address
 * @return array Status of the operation (or manual commands when not writable)
 */
function configureMtaSmtp($iniPath, $mode, $smtpPort = 1025, $fromEmail = 'noreply@localhost') {
    if (!file_exists($iniPath)) {
        return ['success' => false, 'error' => 'php.ini file not found'];
    }

    $sendmailPath = resolveSendmailBinary($mode);
    if ($mode === 'mailpit' && $sendmailPath === null) {
        return [
            'success' => false,
            'error' => 'Mailpit binary not found. Install the Mailpit node from the Plugins page first, then configure SMTP.',
        ];
    }

    $content = file_get_contents($iniPath);

    // Define replacements (handles commented-out or active settings)
    $replacements = [
        // sendmail_path: always set explicitly for the selected MTA
        '/^;?\s*sendmail_path\s*=.*$/mi' => 'sendmail_path = ' . $sendmailPath,
        // sendmail_from: default sender
        '/^;?\s*sendmail_from\s*=.*$/mi' => 'sendmail_from = ' . $fromEmail,
    ];

    if ($mode === 'mailpit') {
        // Windows-style SMTP settings are irrelevant on Linux; comment them out
        $replacements['/^;?\s*smtp\s*=\s*.+$/mi'] = ';smtp = localhost';
        $replacements['/^;?\s*smtp_port\s*=\s*.+$/mi'] = ';smtp_port = ' . (int)$smtpPort;
    } else {
        // Postfix/Sendmail: comment out Windows-only smtp settings
        $replacements['/^;?\s*smtp\s*=\s*.+$/mi'] = ';smtp = localhost';
        $replacements['/^;?\s*smtp_port\s*=\s*.+$/mi'] = ';smtp_port = 25';
    }

    $contentToUpdate = $content;
    foreach ($replacements as $pattern => $replacement) {
        $contentToUpdate = preg_replace($pattern, $replacement, $contentToUpdate);
    }

    if (!preg_match('/^sendmail_path\s*=/mi', $contentToUpdate)) {
        // Add [mail function] section when nothing matched
        if (preg_match('/\[mail function\]/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1] + strlen($matches[0][0]);
            $contentToUpdate = substr_replace($contentToUpdate, "\nsendmail_path = " . $sendmailPath . "\nsendmail_from = " . $fromEmail . "\n", $pos, 0);
        } else {
            $contentToUpdate .= "\n\n[mail function]\nsendmail_path = " . $sendmailPath . "\nsendmail_from = " . $fromEmail . "\n";
        }
    }

    // File not writable by the web user → hand back exact commands the user can run with sudo
    if (!is_writable($iniPath)) {
        $backupName = basename($iniPath) . '.backup.' . date('Y-m-d_His');
        $lines = explode("\n", $contentToUpdate);
        $newContent = implode("\n", $lines);

        // Write staged file for the sudo command to install
        $stageDir = sys_get_temp_dir() . '/nucleus_mta_' . time();
        @mkdir($stageDir, 0755, true);
        $staged = $stageDir . '/php.ini.new';
        @file_put_contents($staged, $newContent);

        return [
            'success' => false,
            'needs_manual' => true,
            'message' => 'php.ini is not writable by the web server. Run these commands with sudo to apply the ' . $mode . ' configuration:',
            'commands' => [
                'sudo cp ' . escapeshellarg($iniPath) . ' ' . escapeshellarg($iniPath . '.backup.' . date('Y-m-d_His')),
                'sudo cp ' . escapeshellarg($staged) . ' ' . escapeshellarg($iniPath),
                'sudo systemctl restart apache2 || sudo systemctl restart php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '-fpm',
            ],
            'staged_file' => $staged,
            'config' => [
                'mode' => $mode,
                'sendmail_path' => $sendmailPath,
                'sendmail_from' => $fromEmail,
            ],
        ];
    }

    // Backup original file before any changes
    $backupPath = $iniPath . '.backup.' . date('Y-m-d_His');
    if (!copy($iniPath, $backupPath)) {
        return ['success' => false, 'error' => 'Failed to create backup of php.ini'];
    }

    // Write updated content
    if (file_put_contents($iniPath, $contentToUpdate) === false) {
        @copy($backupPath, $iniPath);
        return ['success' => false, 'error' => 'Failed to write php.ini file'];
    }

    return [
        'success' => true,
        'message' => 'SMTP configuration updated for ' . $mode,
        'backup' => $backupPath,
        'config' => [
            'mode' => $mode,
            'sendmail_path' => $sendmailPath,
            'sendmail_from' => $fromEmail,
        ],
    ];
}

/**
 * Check Mailpit status (web UI API + SMTP port).
 */
function checkMailpitConfig() {
    $running = false;
    $smtpRunning = false;

    $ch = curl_init('http://localhost:8025/api/v1/info');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $running = ($httpCode === 200 && !empty($resp));

    $sock = @fsockopen('127.0.0.1', 1025, $errno, $errstr, 1);
    if ($sock) {
        @fclose($sock);
        $smtpRunning = true;
    }

    return [
        'running' => $running,
        'smtp_running' => $smtpRunning,
        'enabled' => $running,
        'port' => 1025,
        'web_port' => 8025,
    ];
}

/**
 * Check whether Postfix (systemd) is active.
 */
function checkPostfix() {
    $status = trim(@shell_exec('systemctl is-active postfix 2>/dev/null') ?? '');
    return $status === 'active';
}

try {
    switch ($action) {
        case 'check':
            $iniPath = findPhpIni();
            $mailpit = checkMailpitConfig();
            $postfixActive = checkPostfix();

            if (!$iniPath) {
                echo json_encode([
                    'success' => false,
                    'error' => 'php.ini file not found',
                    'mailpit' => $mailpit,
                    'postfix_active' => $postfixActive,
                ]);
                exit;
            }

            $currentConfig = getCurrentSmtpConfig($iniPath);
            $detectedMta = $currentConfig ? detectMta($currentConfig) : 'sendmail';

            // A Linux setup is "configured" when sendmail_path points at a working MTA
            $sendmailPath = $currentConfig['sendmail_path'] ?? '';
            $isConfigured = false;
            if ($mailpit['running'] && $detectedMta === 'mailpit') {
                $isConfigured = true;
            } elseif ($postfixActive && $detectedMta === 'postfix' && stripos($sendmailPath, '/usr/sbin/sendmail') !== false) {
                $isConfigured = true;
            }

            if ($mailpit['running']) {
                $recommendation = $detectedMta === 'mailpit' ? 'ok' : 'configure';
            } elseif ($postfixActive) {
                $recommendation = $detectedMta === 'postfix' ? 'ok' : 'configure';
            } else {
                $recommendation = 'check_mailpit';
            }

            echo json_encode([
                'success' => true,
                'php_ini_path' => $iniPath,
                'php_ini_writable' => is_writable($iniPath),
                'current_config' => $currentConfig,
                'detected_mta' => $detectedMta,
                'is_configured' => $isConfigured,
                'mailpit' => $mailpit,
                'postfix_active' => $postfixActive,
                'recommendation' => $recommendation,
            ]);
            break;

        case 'configure':
            $iniPath = findPhpIni();
            if (!$iniPath) {
                throw new Exception('php.ini file not found');
            }

            $mode = $_POST['mode'] ?? 'mailpit';
            if (!in_array($mode, ['mailpit', 'postfix', 'sendmail'], true)) {
                throw new Exception('Invalid mail transport mode');
            }

            $smtpPort = (int)($_POST['smtp_port'] ?? 1025);
            $fromEmail = $_POST['from_email'] ?? 'noreply@localhost';

            $result = configureMtaSmtp($iniPath, $mode, $smtpPort, $fromEmail);
            echo json_encode($result);
            break;

        case 'restore':
            $iniPath = findPhpIni();
            if (!$iniPath) {
                throw new Exception('php.ini file not found');
            }
            
            $backupPath = $_POST['backup'] ?? '';
            if (empty($backupPath) || !file_exists($backupPath)) {
                throw new Exception('Backup file not found');
            }
            
            if (!is_writable($iniPath)) {
                throw new Exception('php.ini file is not writable');
            }
            
            if (copy($backupPath, $iniPath)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'php.ini restored from backup'
                ]);
            } else {
                throw new Exception('Failed to restore backup');
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

?>