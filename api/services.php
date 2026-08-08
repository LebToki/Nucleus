<?php
/**
 * Nucleus - Services API
 * Version: 3.0.0
 * Description: API endpoint for managing services and ports
 */

// Start output buffering to catch any stray output
ob_start();

// Disable error display to prevent JSON corruption
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Enforce authentication
if (function_exists('check_auth')) {
    check_auth();
}

// Clear any output that may have been generated
ob_clean();

// Set JSON header before any output
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'status';
$serviceName = $_GET['service'] ?? '';

// CSRF check for destructive actions
$destructiveActions = ['start', 'stop', 'restart', 'reload'];
if (in_array($action, $destructiveActions)) {
    $token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? '');
    if (!verifyCSRFToken($token)) {
        ob_clean();
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
        ob_end_flush();
        exit;
    }
}

if (!defined('NUCLEUS_ROOT')) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Install root not defined']);
    ob_end_flush();
    exit;
}

// Service name mapping (systemd service names)
$serviceMap = [
    'Apache' => 'apache2',
    'MySQL' => 'mysql',
    'PostgreSQL' => 'postgresql',
    'Nginx' => 'nginx',
    'Redis' => 'redis-server',
    'Memcached' => 'memcached',
    'MongoDB' => 'mongod',
    'Mailpit' => 'mailpit',
    'Postfix' => 'postfix',
];

// Detect PHP-FPM service name dynamically (varies by version)
$phpFpmVersions = ['8.3', '8.2', '8.1', '8.0', '7.4'];
foreach ($phpFpmVersions as $ver) {
    $unit = 'php' . $ver . '-fpm';
    $check = @shell_exec('systemctl list-unit-files ' . escapeshellarg($unit . '.service') . ' 2>/dev/null');
    if ($check && stripos($check, $unit) !== false) {
        $serviceMap['PHP-FPM'] = $unit;
        break;
    }
}

// Validate serviceName against whitelist
if (!empty($serviceName) && !isset($serviceMap[$serviceName]) && !in_array($serviceName, $serviceMap)) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid service name']);
    ob_end_flush();
    exit;
}

// Check systemd service status
function checkServiceStatus($serviceName) {
    $output = @shell_exec('systemctl is-active ' . escapeshellarg($serviceName) . ' 2>&1');
    return trim($output) === 'active' ? 'running' : 'stopped';
}

// Check if a process is running by name
function checkProcessRunning($processName) {
    $output = @shell_exec('ps aux 2>&1');
    return $output && preg_match('/' . preg_quote($processName, '/') . '/', $output);
}

// Start service
function startService($serviceName) {
    global $serviceMap;

    $mappedName = $serviceMap[$serviceName] ?? $serviceName;

    $command = 'sudo systemctl start ' . escapeshellarg($mappedName) . ' 2>&1';
    @exec($command);
    sleep(1); // Wait a bit
    return checkServiceStatus($mappedName) === 'running';
}

// Stop service
function stopService($serviceName) {
    global $serviceMap;

    $mappedName = $serviceMap[$serviceName] ?? $serviceName;

    $command = 'sudo systemctl stop ' . escapeshellarg($mappedName) . ' 2>&1';
    @exec($command);
    sleep(1);
    return checkServiceStatus($mappedName) === 'stopped';
}

// Reload Apache
function reloadApache() {
    $apacheService = 'apache2';
    $status = checkServiceStatus($apacheService);

    if ($status !== 'running') {
        return ['success' => false, 'error' => 'Apache is not running'];
    }

    // Try graceful reload via systemctl
    $command = 'sudo systemctl reload apache2 2>&1';
    $output = [];
    $returnCode = 0;
    @exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        return ['success' => true, 'message' => 'Apache reloaded successfully'];
    }

    // Fallback: full restart
    $command = 'sudo systemctl restart apache2 2>&1';
    @exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        return ['success' => true, 'message' => 'Apache restarted successfully'];
    }

    return ['success' => false, 'error' => 'Failed to reload or restart Apache'];
}

// Handle requests
try {
    switch ($action) {
        case 'status':
            // Default ports for each service (Linux/Nucleus)
            $defaultPorts = [
                'Apache' => ['port' => 80, 'ssl_port' => 443],
                'MySQL' => ['port' => 3306, 'ssl_port' => null],
                'PostgreSQL' => ['port' => 5432, 'ssl_port' => null],
                'Nginx' => ['port' => 8080, 'ssl_port' => 8443],
                'Redis' => ['port' => 6379, 'ssl_port' => null],
                'Memcached' => ['port' => 11211, 'ssl_port' => null],
                'MongoDB' => ['port' => 27017, 'ssl_port' => null],
                'Mailpit' => ['port' => 1025, 'ssl_port' => 8025],
            ];

            $services = \Nucleus\Core\Cache::get('service_status');
            if (!$services) {
                // ⚡ Bolt: Batch systemctl queries to avoid N+1 command execution
                $systemctlOutput = @shell_exec('systemctl is-active ' . implode(' ', array_map('escapeshellarg', array_values($serviceMap))) . ' 2>&1');
                $systemctlLines = array_filter(explode("\n", trim($systemctlOutput ?? '')));

                // Map systemctl output back to service names (output order matches input order)
                $serviceStatuses = [];
                $i = 0;
                foreach ($serviceMap as $key => $mappedName) {
                    $line = trim($systemctlLines[$i] ?? 'inactive');
                    $serviceStatuses[$key] = ($line === 'active') ? 'running' : 'stopped';
                    $i++;
                }

                // ⚡ Bolt: Single ss call for all port checks
                $ssOutput = @shell_exec('ss -tlnp 2>&1');

                foreach ($serviceMap as $key => $mappedName) {
                    $status = $serviceStatuses[$key] ?? 'stopped';
                    $usage = ($status === 'running') ? \Nucleus\Core\Services::getResourceUsage($key) : ['cpu' => 0, 'ram' => 0, 'pid' => 0];

                    $defaults = $defaultPorts[$key] ?? ['port' => '', 'ssl_port' => null];
                    $port = $defaults['port'];
                    $sslPort = $defaults['ssl_port'];

                    $runningPorts = [];
                    if ($status === 'running') {
                        // ⚡ Bolt: Check ports against cached ss output
                        if (!empty($port) && $ssOutput && preg_match('/:' . preg_quote((string)$port, '/') . '\s/', $ssOutput)) {
                            $runningPorts[] = $port;
                        }
                        if (!empty($sslPort) && $ssOutput && preg_match('/:' . preg_quote((string)$sslPort, '/') . '\s/', $ssOutput)) {
                            $runningPorts[] = $sslPort;
                        }
                    }

                    $services[$key] = [
                        'status' => $status,
                        'port' => $port,
                        'ssl_port' => $sslPort,
                        'enabled' => true,
                        'running_ports' => $runningPorts,
                        'usage' => $usage
                    ];
                }
                \Nucleus\Core\Cache::set('service_status', $services, 30);
            }

            // Get disk usage for Laragon Root
            // ⚡ Bolt: Cache disk space calls to avoid redundant filesystem operations
            $totalSpace = disk_total_space(NUCLEUS_ROOT);
            $freeSpace = disk_free_space(NUCLEUS_ROOT);
            $usedSpace = $totalSpace - $freeSpace;

            $disk = [
                'total' => round($totalSpace / 1024 / 1024 / 1024, 2),
                'free' => round($freeSpace / 1024 / 1024 / 1024, 2),
                'used' => round($usedSpace / 1024 / 1024 / 1024, 2),
                'percent' => $totalSpace > 0 ? round(($usedSpace / $totalSpace) * 100, 1) : 0
            ];
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'data' => [
                    'services' => $services,
                    'disk' => $disk
                ],
                'error' => null
            ]);
            ob_end_flush();
            break;
            
        case 'start':
            if (empty($serviceName)) {
                throw new Exception('Service name required');
            }
            
            $result = \Nucleus\Core\Services::start($serviceName);
            if ($result) {
                \Nucleus\Core\Cache::delete('service_status');
            }
            ob_clean();
            echo json_encode([
                'success' => $result,
                'data' => null,
                'error' => $result ? null : 'Failed to start service'
            ]);
            ob_end_flush();
            break;
            
        case 'stop':
            if (empty($serviceName)) {
                throw new Exception('Service name required');
            }
            
            $result = \Nucleus\Core\Services::stop($serviceName);
            if ($result) {
                \Nucleus\Core\Cache::delete('service_status');
            }
            ob_clean();
            echo json_encode([
                'success' => $result,
                'data' => null,
                'error' => $result ? null : 'Failed to stop service'
            ]);
            ob_end_flush();
            break;

        case 'restart':
            if (empty($serviceName)) {
                throw new Exception('Service name required');
            }

            // Restart = stop + start with a brief delay
            $stopped = \Nucleus\Core\Services::stop($serviceName);
            usleep(500000); // 0.5s pause
            $started = \Nucleus\Core\Services::start($serviceName);
            $result = $stopped && $started;

            if ($result) {
                \Nucleus\Core\Cache::delete('service_status');
            }
            ob_clean();
            echo json_encode([
                'success' => $result,
                'data' => null,
                'error' => $result ? null : 'Failed to restart service'
            ]);
            ob_end_flush();
            break;

        case 'reload':
            if ($serviceName !== 'Apache') {
                throw new Exception('Only Apache can be reloaded');
            }
            
            $result = reloadApache();
            ob_clean();
            echo json_encode($result);
            ob_end_flush();
            break;

        case 'save':
            // Save service port/enabled preferences to preferences.json
            $services = $_POST['services'] ?? [];
            $prefs = getDashboardPreferences();
            $servicePrefs = [];

            foreach ($services as $key => $serviceData) {
                $servicePrefs[$key] = [
                    'port' => $serviceData['port'] ?? null,
                    'ssl_port' => $serviceData['ssl_port'] ?? null,
                    'enabled' => isset($serviceData['enabled']) || isset($serviceData['enabled_check']),
                ];
            }

            $prefs['services'] = $servicePrefs;
            $result = saveDashboardPreferences($prefs);
            ob_clean();
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Service configuration saved' : 'Failed to save configuration'
            ]);
            ob_end_flush();
            break;

        case 'save_general':
            // Save general settings to preferences.json
            $generalFields = ['DocumentRoot', 'HostnameFormat', 'BackupInterval'];
            $booleanFields = ['StartAllAutomatically', 'AutoCreateVirtualHosts', 'AutoBackup', 'AutoUpdate'];

            $prefs = getDashboardPreferences();

            foreach ($generalFields as $field) {
                if (isset($_POST[$field])) {
                    $prefs[strtolower($field)] = trim($_POST[$field]);
                }
            }

            foreach ($booleanFields as $field) {
                $prefs[strtolower($field)] = isset($_POST[$field]) && $_POST[$field] == '1';
            }

            $result = saveDashboardPreferences($prefs);
            ob_clean();
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'General settings saved successfully' : 'Failed to save general settings'
            ]);
            ob_end_flush();
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (\Throwable $e) {
    \Nucleus\Core\Logger::error("API services.php error: " . $e->getMessage());
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'error' => $e->getMessage()
    ]);
    ob_end_flush();
}

