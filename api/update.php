<?php
/**
 * Nucleus System - Update API
 * Manages the process of checking for, downloading, and installing core system updates.
 * 
 * Version: 3.0.0 (Updated for modern Nucleus architecture)
 */

// Start output buffering
ob_start();

// Disable error display to prevent JSON corruption
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/UpdateManager.php';
require_once __DIR__ . '/../includes/ConfigMigrator.php';
require_once __DIR__ . '/../includes/helpers.php';

// Enforce authentication
check_auth();

// Clear any output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'check';

try {
    $updateManager = new UpdateManager();
    
    switch ($action) {
        case 'check':
            $updateInfo = $updateManager->checkForUpdates();
            // Warm the cached repo-version probe used by the footer/changelog page.
            if (function_exists('refreshLatestVersionCache') && !empty($updateInfo['latest_version'])) {
                refreshLatestVersionCache($updateInfo);
            }
            ob_clean();
            echo json_encode([
                'success' => true,
                'data' => $updateInfo
            ]);
            break;
            
        case 'backup':
            try {
                $backupPath = $updateManager->backupCurrentInstallation();
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'backup_path' => $backupPath
                ]);
            } catch (Exception $e) {
                // Re-throwing the exception here will be caught by the general handler below.
                throw new Exception("Failed to backup installation: " . $e->getMessage(), 0, $e);
            }
            break;
            
        case 'download':
            $downloadUrl = $_POST['download_url'] ?? $_GET['download_url'] ?? null;

            if (empty($downloadUrl)) {
                throw new Exception('Download URL required.');
            }

            try {
                // Basic validation: check if the URL seems valid
                if (!filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
                    throw new InvalidArgumentException("The provided download URL is not a valid format.");
                }
                
                $zipPath = $updateManager->downloadUpdate($downloadUrl);
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Update downloaded successfully',
                    'zip_path' => $zipPath
                ]);
            } catch (Exception $e) {
                 // Re-throwing the exception here will be caught by the general handler below.
                 throw new Exception("Failed to download update: " . $e->getMessage(), 0, $e);
            }
            break;
            
        case 'install':
            $zipPath = $_POST['zip_path'] ?? $_GET['zip_path'] ?? null;
            $backupPath = $_POST['backup_path'] ?? $_GET['backup_path'] ?? null;

            if (empty($zipPath)) {
                throw new Exception('ZIP path is required.');
            }
            if (empty($backupPath)) {
                throw new Exception('Backup path is required. Cannot install without backup.');
            }
            
            // Verify paths exist and are accessible
            if (!file_exists($zipPath) || !is_readable($zipPath)) {
                throw new Exception("ZIP file not found or unreadable: " . $zipPath);
            }
            if (!is_dir($backupPath) || !is_writable($backupPath)) {
                throw new Exception("Backup directory not found or not writable: " . $backupPath);
            }
            
            try {
                $result = $updateManager->installUpdate($zipPath, $backupPath);
                
                // Verify installation
                $verified = $updateManager->verifyInstallation();
                
                ob_clean();
                echo json_encode([
                    'success' => $result && $verified,
                    'message' => $verified ? 'Update installed successfully' : 'Update installed but verification failed',
                    'verified' => $verified
                ]);
            } catch (Exception $e) {
                 // Re-throwing the exception here will be caught by the general handler below.
                throw new Exception("Installation process failed: " . $e->getMessage(), 0, $e);
            }
            break;
            
        case 'rollback':
            $backupPath = $_POST['backup_path'] ?? $_GET['backup_path'] ?? null;

            if (empty($backupPath)) {
                throw new Exception('Backup path required.');
            }
            
            try {
                $result = $updateManager->rollback($backupPath);
                ob_clean();
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Rollback completed successfully' : 'Rollback failed'
                ]);
            } catch (Exception $e) {
                 // Re-throwing the exception here will be caught by the general handler below.
                throw new Exception("Rollback process failed: " . $e->getMessage(), 0, $e);
            }
            break;
            
        case 'verify':
            try {
                $verified = $updateManager->verifyInstallation();
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'verified' => $verified,
                    'message' => $verified ? 'Installation verified' : 'Installation verification failed'
                ]);
            } catch (Exception $e) {
                 throw new Exception("Verification failed: " . $e->getMessage(), 0, $e);
            }
            break;
            
        default:
            throw new Exception('Invalid action specified.');
    }

} catch (Exception|Error $e) {
    // Consolidated error handler for all exceptions and errors.
    ob_clean();
    http_response_code(500);

    // Log the detailed error including stack trace for debugging purposes.
    error_log("Update API: Request failed ({$action}). Error Details: " . get_class($e) . ": " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
    
    // Provide a user-friendly, non-specific error message.
    echo json_encode([
        'success' => false,
        'error' => 'An internal server error occurred during the update process. Please check the logs for details.'
    ]);

} finally {
    ob_end_flush();
}
?>
