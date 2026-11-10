<?php
/**
 * Nucleus - File Editor API
 * Version: 3.0.0
 * Description: API endpoint for reading/writing configuration files
 */

// Start output buffering to catch any stray output
ob_start();

// Disable error display to prevent JSON corruption
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load configuration
require_once __DIR__ . '/../config.php';

// Clear any output that may have been generated
ob_clean();

// Set JSON header before any output
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'read';
$filePath = $_GET['path'] ?? '';

if (!defined('NUCLEUS_ROOT')) {
    // Try to get Laragon root from config
    if (function_exists('getLaragonRoot')) {
        define('NUCLEUS_ROOT', getLaragonRoot());
    } else {
        ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Laragon root not configured']);
        ob_end_flush();
        exit;
    }
}

if (empty(NUCLEUS_ROOT)) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Laragon root is empty']);
    ob_end_flush();
    exit;
}

// System paths the dashboard legitimately reads: vhosts and logs live outside
// the project root, and the Sites/Logs pages were rejected as "Invalid file
// path" because every path had to be under NUCLEUS_ROOT.
const SYSTEM_READ_PATHS = [
    '/etc/apache2/sites-available',
    '/etc/apache2/sites-enabled',
    '/etc/apache2/conf-enabled',
    '/etc/apache2/apache2.conf',
    '/etc/nginx/sites-available',
    '/etc/nginx/sites-enabled',
    '/etc/nginx/nginx.conf',
    '/etc/hosts',
    '/var/log/apache2',
    '/var/log/nginx',
    '/var/log/php',
    '/var/log/syslog',
];

function isSystemReadablePath($realPath) {
    foreach (SYSTEM_READ_PATHS as $allowed) {
        $realAllowed = realpath($allowed) ?: $allowed;
        if ($realPath === $realAllowed || strpos($realPath, rtrim($realAllowed, '/') . '/') === 0) {
            return true;
        }
    }
    return false;
}

// Security: writes stay inside the project root; reads also allow system config
// and log locations, which the Sites and Logs views need.
function validatePath($path, $forWrite = false) {
    $laragonRoot = defined('NUCLEUS_ROOT') ? NUCLEUS_ROOT : '';

    $realPath = realpath($path);
    if (!$realPath) {
        return false;
    }

    if (!$forWrite && isSystemReadablePath($realPath)) {
        return true;
    }

    if (empty($laragonRoot)) {
        return false;
    }

    $realLaragonRoot = realpath($laragonRoot);
    if (!$realLaragonRoot) {
        return false;
    }

    return strpos($realPath, $realLaragonRoot) === 0;
}

// Read file content
if (!function_exists('readConfigFile')) {
    function readConfigFile($path) {
        if (!validatePath($path)) {
            return ['success' => false, 'error' => 'Invalid file path'];
        }
        
        if (!file_exists($path)) {
            return ['success' => false, 'error' => 'File not found'];
        }
        
        if (!is_readable($path)) {
            return ['success' => false, 'error' => 'File is not readable'];
        }
        
        try {
            $content = file_get_contents($path);
            return [
                'success' => true,
                'content' => $content,
                'path' => $path,
                'size' => filesize($path),
                'modified' => filemtime($path)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// Write file content
if (!function_exists('writeConfigFile')) {
    function writeConfigFile($path, $content) {
        if (!validatePath($path)) {
            return ['success' => false, 'error' => 'Invalid file path'];
        }
        
        // Check if directory exists
        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return ['success' => false, 'error' => 'Cannot create directory'];
            }
        }
        
        if (file_exists($path) && !is_writable($path)) {
            return ['success' => false, 'error' => 'File is not writable'];
        }
        
        // Check if directory is writable
        if (!is_writable($dir)) {
            return ['success' => false, 'error' => 'Directory is not writable'];
        }
        
        try {
            // Create backup
            if (file_exists($path)) {
                $backupPath = $path . '.backup.' . date('Y-m-d_H-i-s');
                @copy($path, $backupPath);
            }
            
            $result = file_put_contents($path, $content);
            if ($result === false) {
                return ['success' => false, 'error' => 'Failed to write file'];
            }
            
            return [
                'success' => true,
                'message' => 'File saved successfully',
                'path' => $path,
                'size' => strlen($content)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// List files in directory
function listFiles($dir) {
    if (!validatePath($dir)) {
        return ['success' => false, 'error' => 'Invalid directory path'];
    }
    
    if (!is_dir($dir)) {
        return ['success' => false, 'error' => 'Directory not found'];
    }
    
    $files = [];
    $items = glob($dir . '/*');
    foreach ($items as $item) {
        $files[] = [
            'name' => basename($item),
            'path' => $item,
            'type' => is_dir($item) ? 'directory' : 'file',
            'size' => is_file($item) ? filesize($item) : 0,
            'modified' => filemtime($item)
        ];
    }
    
    return [
        'success' => true,
        'files' => $files
    ];
}

// Handle requests
try {
    switch ($action) {
        case 'read':
            if (empty($filePath)) {
                throw new Exception('File path required');
            }
            
            $result = readConfigFile($filePath);
            // Clear any output before sending JSON
            ob_clean();
            echo json_encode($result);
            ob_end_flush();
            break;
            
        case 'write':
            if (empty($filePath)) {
                throw new Exception('File path required');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $content = $input['content'] ?? '';
            
            $result = writeConfigFile($filePath, $content);
            // Clear any output before sending JSON
            ob_clean();
            echo json_encode($result);
            ob_end_flush();
            break;
            
        case 'list':
            if (empty($filePath)) {
                throw new Exception('Directory path required');
            }
            
            $result = listFiles($filePath);
            // Clear any output before sending JSON
            ob_clean();
            echo json_encode($result);
            ob_end_flush();
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    // Clear any output before sending error JSON
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    ob_end_flush();
} catch (Error $e) {
    // Handle PHP 7+ errors
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    ob_end_flush();
}

