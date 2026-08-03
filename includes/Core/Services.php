<?php

namespace LaragonDashboard\Core;

/**
 * Services Class
 * Version: 2.0.0
 * Handles service management (Apache, MySQL, etc.) and resource monitoring
 * Linux-native implementation using systemd and procfs
 */
class Services {

    // Service name mapping (Linux systemd unit names)
    private static $serviceMap = [
        'Apache' => 'apache2',
        'MySQL' => 'mysql',
        'PostgreSQL' => 'postgresql',
        'Nginx' => 'nginx',
        'Redis' => 'redis-server',
        'Memcached' => 'memcached',
        'MongoDB' => 'mongod',
        'Mailpit' => 'mailpit'
    ];

    /**
     * Get real service name
     */
    public static function getRealName($name) {
        return self::$serviceMap[$name] ?? $name;
    }

    /**
     * Check if a service is running
     */
    public static function isRunning($name) {
        $realName = escapeshellarg(self::getRealName($name));
        $output = @shell_exec('systemctl is-active ' . $realName . ' 2>&1');
        return $output && trim($output) === 'active';
    }

    /**
     * Start a service
     */
    public static function start($name) {
        $realName = escapeshellarg(self::getRealName($name));
        $output = @shell_exec('sudo systemctl start ' . $realName . ' 2>&1');
        // systemctl start returns empty on success; verify the service is now active
        $status = @shell_exec('systemctl is-active ' . $realName . ' 2>&1');
        return $status && trim($status) === 'active';
    }

    /**
     * Stop a service
     */
    public static function stop($name) {
        $realName = escapeshellarg(self::getRealName($name));
        $output = @shell_exec('sudo systemctl stop ' . $realName . ' 2>&1');
        // systemctl stop returns empty on success; verify the service is now inactive
        $status = @shell_exec('systemctl is-active ' . $realName . ' 2>&1');
        return $status && (trim($status) === 'inactive' || trim($status) === 'failed');
    }

    /**
     * Check if a port is in use
     */
    public static function isPortInUse($port) {
        $port = intval($port);
        $output = @shell_exec('ss -tlnp 2>&1 | grep -q ":' . $port . '" && echo "in_use"');
        return $output && trim($output) === 'in_use';
    }

    /**
     * Get resource usage for a service (Linux)
     */
    public static function getResourceUsage($name) {
        $realName = self::getRealName($name);
        $safeName = escapeshellarg($realName);

        // Get MainPID from systemd
        $pidOutput = @shell_exec('systemctl show ' . $safeName . ' --property=MainPID --value 2>&1');
        $pid = intval(trim($pidOutput));

        if ($pid <= 0) {
            return ['cpu' => 0, 'ram' => 0, 'pid' => 0];
        }

        // Get CPU and memory usage from ps for the specific PID
        $psOutput = @shell_exec('ps -p ' . $pid . ' -o %cpu=,%rss= 2>&1');
        if (empty($psOutput)) {
            return ['cpu' => 0, 'ram' => 0, 'pid' => $pid];
        }

        $parts = preg_split('/\s+/', trim($psOutput));
        if (count($parts) < 2) {
            return ['cpu' => 0, 'ram' => 0, 'pid' => $pid];
        }

        $cpu = floatval($parts[0]);
        $ram = floatval($parts[1]) / 1024; // Convert KB to MB

        return [
            'cpu' => round($cpu, 2),
            'ram' => round($ram, 2),
            'pid' => $pid
        ];
    }
}
