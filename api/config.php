<?php
/**
 * Nucleus - Config Editor API
 * Version: 1.0.0
 * Description: API for reading and writing service configuration files
 */

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (function_exists('check_auth')) {
    check_auth();
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

/**
 * Get available config files
 */
function getConfigFiles() {
    $files = [];
    
    // PHP.ini (active one)
    $phpIni = php_ini_loaded_file();
    if (!$phpIni || !file_exists($phpIni)) {
        $phpIni = function_exists('getPHPIniPath') ? getPHPIniPath() : null;
    }
    if ($phpIni && file_exists($phpIni)) {
        $files['php'] = [
            'name' => 'php.ini',
            'path' => $phpIni,
            'service' => 'PHP'
        ];
    }

    // MySQL/MariaDB (Linux config)
    $myIni = function_exists('getMySQLIniPath') ? getMySQLIniPath() : null;
    if ($myIni && file_exists($myIni)) {
        $files['mysql'] = [
            'name' => basename($myIni),
            'path' => $myIni,
            'service' => 'MySQL/MariaDB'
        ];
    } else {
        foreach (['/etc/mysql/my.cnf', '/etc/my.cnf'] as $candidate) {
            if (file_exists($candidate)) {
                $files['mysql'] = [
                    'name' => basename($candidate),
                    'path' => $candidate,
                    'service' => 'MySQL/MariaDB'
                ];
                break;
            }
        }
    }

    // Apache httpd.conf (Linux)
    $apacheConf = function_exists('getApacheConfPath') ? getApacheConfPath() : null;
    if ($apacheConf) {
        $files['apache'] = [
            'name' => basename($apacheConf),
            'path' => $apacheConf,
            'service' => 'Apache'
        ];
    }

    // Nginx nginx.conf (Linux)
    foreach (['/etc/nginx/nginx.conf', '/etc/nginx/conf.d/default.conf'] as $candidate) {
        if (file_exists($candidate)) {
            $files['nginx'] = [
                'name' => basename($candidate),
                'path' => $candidate,
                'service' => 'Nginx'
            ];
            break;
        }
    }

    return $files;
}

try {
    $configFiles = getConfigFiles();

    switch ($action) {
        case 'list':
            ob_clean();
            echo json_encode(['success' => true, 'data' => $configFiles]);
            break;

        case 'read':
            $key = $_GET['file'] ?? '';
            if (!isset($configFiles[$key])) throw new Exception('Invalid file requested');
            
            $path = $configFiles[$key]['path'];
            if (!file_exists($path)) throw new Exception('File not found');
            
            $content = file_get_contents($path);
            ob_clean();
            echo json_encode(['success' => true, 'content' => $content, 'path' => $path]);
            break;

        case 'save':
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('CSRF token validation failed');
            }
            
            $key = $_POST['file'] ?? '';
            if (!isset($configFiles[$key])) throw new Exception('Invalid file requested');
            
            $path = $configFiles[$key]['path'];
            $content = $_POST['content'] ?? null;
            
            if ($content === null) throw new Exception('No content provided');
            
            // Backup before saving
            copy($path, $path . '.bak.' . time());
            
            if (file_put_contents($path, $content) === false) {
                throw new Exception('Failed to write to file. Check permissions.');
            }
            
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Configuration saved successfully. A backup was created.']);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (\Throwable $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
