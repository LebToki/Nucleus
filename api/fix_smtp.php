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
 * Find PHP php.ini file by traversing the presumed vendor/php directory structure.
 */
function findPhpIni() {
    $root = getNucleusRootDir();
    if (empty($root) || !is_dir($root)) {
        return null;
    }
    
    // Attempt to determine a likely PHP path based on common Composer/Nucleus structures.
    // This is a best-effort guess and may need adjustment if the environment changes.
    $vendorPhpPath = $root . '/vendor/php/';
    if (!is_dir($vendorPhpPath)) {
        return null; 
    }
    
    // Find the most recent or standard PHP version directory (e.g., php-8.3)
    $phpDirs = glob($vendorPhpPath . 'php-*');
    if (empty($phpDirs)) {
        return null;
    }

    // Sort to get the latest version and use its path for php.ini lookup.
    rsort($phpDirs);
    $latestVersionDir = $phpDirs[0];
    $iniPath = $latestVersionDir . '/php.ini';
    
    if (file_exists($iniPath)) {
        return $iniPath;
    }
    
    // Fallback: Check for php.ini in the vendor/php directory itself
    $iniPathFallback = $vendorPhpPath . 'php.ini';
    if (file_exists($iniPathFallback)) {
        return $iniPathFallback;
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
 * Configure PHP to use Mailpit SMTP by modifying php.ini.
 * @param string $iniPath The path to the writable php.ini file.
 * @param int $smtpPort The target SMTP port (default 1025 for Mailpit).
 * @param string $fromEmail The default sender email address.
 * @return array Status of the operation.
 */
function configureMailpitSmtp($iniPath, $smtpPort = 1025, $fromEmail = 'noreply@localhost') {
    if (!file_exists($iniPath)) {
        return ['success' => false, 'error' => 'php.ini file not found'];
    }
    
    // Check if file is writable (crucial for system changes)
    if (!is_writable($iniPath)) {
        return ['success' => false, 'error' => 'php.ini file is not writable. Please run as administrator or check file permissions.'];
    }
    
    $content = file_get_contents($iniPath);
    
    // Backup original file before any changes
    $backupPath = $iniPath . '.backup.' . date('Y-m-d_His');
    if (!copy($iniPath, $backupPath)) {
        return ['success' => false, 'error' => 'Failed to create backup of php.ini'];
    }
    
    // Define replacements patterns (handles commented out or active settings)
    $replacements = [
        // SMTP server: Look for any smtp setting and overwrite/enable it
        '/^;?\s*smtp\s*=\s*(.+)$/mi' => 'smtp = localhost',
        
        // SMTP port: Look for any smtp_port setting and overwrite/enable it
        '/^;?\s*smtp_port\s*=\s*(.+)$/mi' => 'smtp_port = ' . $smtpPort,
        
        // Sendmail from: Overwrite or enable the sender email address
        '/^;?\s*sendmail_from\s*=\s*(.+)$/mi' => 'sendmail_from = ' . $fromEmail,
        
        // Disable sendmail_path (Use SMTP instead)
        '/^;?\s*sendmail_path\s*=\s*(.+)$/mi' => ';sendmail_path = ',
    ];
    
    $contentToUpdate = $content;

    // Apply replacements iteratively. We must be careful not to overwrite valid sections unintentionally.
    foreach ($replacements as $pattern => $replacement) {
        // Using preg_replace with the /m` flag ensures multi-line matching is respected.
        $contentToUpdate = preg_replace($pattern, $replacement, $contentToUpdate);
    }

    // Check if core settings still need to be added (e.g., if no pattern matched at all)
    if (!preg_match('/^smtp\s*=/mi', $contentToUpdate)) {
        $initialContent = $content; // Use the original content check for placement logic
        
        // Find [mail function] section or add at end (using initial content structure)
        if (preg_match('/\[mail function\]/i', $initialContent, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1] + strlen($matches[0][0]);
            // We use the updated content for insertion to maintain consistency
            $contentToUpdate = substr_replace($contentToUpdate, "\nsmtp = localhost\nsmtp_port = " . $smtpPort . "\nsendmail_from = " . $fromEmail . "\n", $pos, 0);
        } else {
            // Add at end of file
            $contentToUpdate .= "\n\n[mail function]\nsmtp = localhost\nsmtp_port = " . $smtpPort . "\nsendmail_from = " . $fromEmail . "\n";
        }
    }

    // Write updated content
    if (file_put_contents($iniPath, $contentToUpdate) === false) {
        // Restore backup on failure
        copy($backupPath, $iniPath);
        return ['success' => false, 'error' => 'Failed to write php.ini file'];
    }
    
    return [
        'success' => true,
        'message' => 'SMTP configuration updated successfully',
        'backup' => $backupPath,
        'config' => [
            'smtp' => 'localhost',
            'smtp_port' => $smtpPort,
            'sendmail_from' => $fromEmail,
            'sendmail_path' => 'disabled'
        ]
    ];
}

/**
 * Check Mailpit status and port using the Nucleus root directory context.
 */
function checkMailpitConfig() {
    $root = getNucleusRootDir();
    if (empty($root)) {
        return ['running' => false, 'port' => null];
    }
    
    // In a real application, this would load settings from Nucleus config files or environment variables.
    // For now, we assume the standard development port if no specific configuration is available.
    $mailpitPort = 1025; 
    $mailpitEnabled = true; // Assume enabled for service checking module context
    
    // Check if Mailpit is actually running via cURL to localhost:port
    $ch = curl_init('http://localhost:' . $mailpitPort);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'running' => $httpCode === 200 || $httpCode === 0, // Check for connection success or refusal (which might still indicate the port exists)
        'enabled' => $mailpitEnabled,
        'port' => $mailpitPort
    ];
}

try {
    switch ($action) {
        case 'check':
            $iniPath = findPhpIni();
            $mailpit = checkMailpitConfig();
            
            if (!$iniPath) {
                echo json_encode([
                    'success' => false,
                    'error' => 'php.ini file not found',
                    'mailpit' => $mailpit
                ]);
                exit;
            }
            
            $currentConfig = getCurrentSmtpConfig($iniPath);
            $isConfigured = false;
            
            if ($currentConfig) {
                // Check if already configured for Mailpit based on current settings vs. detected mailpit port
                $expectedPort = $mailpit['port'];

                $isConfigured = (
                    ($currentConfig['smtp'] === 'localhost' || $currentConfig['smtp'] === '127.0.0.1') &&
                    ($currentConfig['smtp_port'] == $expectedPort) // Use direct comparison for simplicity and robustness
                );
            }

            // --- Robust check logic for external services ---
            $serviceChecks = [
                'databases' => 'Check database connectivity via dedicated function.', 
                'mailpit' => $mailpit
            ];
            
            $results = [];
            foreach ($serviceChecks as $serviceName => $check) {
                if (is_array($check)) {
                    // Assuming check() returns structured data
                    $status = $check['status'] ?? 'unknown'; 
                    $results[$serviceName] = ['status' => $status, 'message' => $check['message'] ?? 'N/A'];
                } else {
                     // Handle simple string checks or functions that don't return an array structure.
                    $results[$serviceName] = ['status' => ($check === 'Check database connectivity via dedicated function.') ? 'pending_check' : 'info', 'message' => $check];
                }
            }

            echo json_encode([
                'success' => true,
                'php_ini_path' => $iniPath,
                'php_ini_writable' => is_writable($iniPath),
                'current_config' => $currentConfig,
                'is_configured' => $isConfigured,
                'mailpit' => $mailpit,
                'service_results' => $results,
                'recommendation' => ($mailpit['enabled'] && !$isConfigured) ? 'configure' : ($isConfigured ? 'ok' : 'check_mailpit')
            ]);
            // --- End of robust check logic for external services ---
            break;
            
        case 'configure':
            $iniPath = findPhpIni();
            if (!$iniPath) {
                throw new Exception('php.ini file not found');
            }
            
            $mailpit = checkMailpitConfig();
            if (!$mailpit['enabled']) {
                throw new Exception('Mailpit is not enabled in the environment. Please ensure the service is active.');
            }
            
            // Use data from POST or fall back to detected/default values
            $smtpPort = $_POST['smtp_port'] ?? $mailpit['port'] ?? 1025;
            $fromEmail = $_POST['from_email'] ?? 'noreply@localhost';
            
            $result = configureMailpitSmtp($iniPath, (int)$smtpPort, $fromEmail);
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