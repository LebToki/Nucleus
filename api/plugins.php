<?php
/**
 * Nucleus - Plugins API
 * Handles plugin installation, removal, and service management
 */

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (function_exists('check_auth')) {
    check_auth();
}

ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// CSRF check
$token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? '');
if (!verifyCSRFToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
    exit;
}

$action = $_POST['action'] ?? ($_GET['action'] ?? 'list');
$pluginKey = $_POST['plugin'] ?? ($_GET['plugin'] ?? '');

try {
    switch ($action) {
        case 'list':
            $available = \Nucleus\Core\PluginManager::getAvailablePlugins();
            $installed = \Nucleus\Core\PluginManager::getInstalledPlugins();

            foreach ($available as $key => &$plugin) {
                $plugin['installed'] = isset($installed[$key]);
                if ($plugin['installed']) {
                    $plugin['running'] = $installed[$key]['running'] ?? false;
                    $plugin['installed_version'] = $installed[$key]['installed_version'] ?? 'unknown';
                }
            }
            unset($plugin);

            echo json_encode([
                'success' => true,
                'data' => [
                    'available' => $available,
                    'installed' => $installed,
                ]
            ]);
            break;

        case 'install':
            if (empty($pluginKey)) {
                throw new Exception('Plugin name required');
            }
            $result = \Nucleus\Core\PluginManager::install($pluginKey);
            http_response_code($result['success'] ? 200 : 500);
            echo json_encode($result);
            break;

        case 'uninstall':
            if (empty($pluginKey)) {
                throw new Exception('Plugin name required');
            }
            $result = \Nucleus\Core\PluginManager::uninstall($pluginKey);
            http_response_code($result['success'] ? 200 : 500);
            echo json_encode($result);
            break;

        case 'start':
            if (empty($pluginKey)) {
                throw new Exception('Plugin name required');
            }
            $result = \Nucleus\Core\PluginManager::startService($pluginKey);
            echo json_encode($result);
            break;

        case 'stop':
            if (empty($pluginKey)) {
                throw new Exception('Plugin name required');
            }
            $result = \Nucleus\Core\PluginManager::stopService($pluginKey);
            echo json_encode($result);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    \Nucleus\Core\Logger::error("API plugins.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
