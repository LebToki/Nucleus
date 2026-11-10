<?php

namespace Nucleus\Core;

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
    public static function getRealName(string $name): string {
        return self::$serviceMap[$name] ?? $name;
    }

    /**
     * Check if a service is running
     */
    public static function isRunning(string $name): bool {
        $realName = escapeshellarg(self::getRealName($name));
        $output = @shell_exec('systemctl is-active ' . $realName . ' 2>&1');
        return $output && trim($output) === 'active';
    }

    /**
     * Wait for a unit to settle into one of the expected states.
     *
     * systemctl returns before the unit finishes transitioning, so a single
     * is-active check right after start/stop reports a false failure (the unit
     * is still "activating"/"deactivating"). Poll briefly instead.
     */
    private static function waitForState(string $quotedName, array $states, int $timeoutMs = 5000): bool {
        $deadline = microtime(true) + ($timeoutMs / 1000);
        do {
            $status = @shell_exec('systemctl is-active ' . $quotedName . ' 2>&1');
            if ($status !== null && in_array(trim($status), $states, true)) {
                return true;
            }
            usleep(150000);
        } while (microtime(true) < $deadline);

        return false;
    }

    /**
     * Start a service
     */
    public static function start(string $name): bool {
        $realName = escapeshellarg(self::getRealName($name));
        @shell_exec('sudo systemctl start ' . $realName . ' 2>&1');
        return self::waitForState($realName, ['active']);
    }

    /**
     * Stop a service
     */
    public static function stop(string $name): bool {
        $realName = escapeshellarg(self::getRealName($name));
        @shell_exec('sudo systemctl stop ' . $realName . ' 2>&1');
        return self::waitForState($realName, ['inactive', 'failed']);
    }

    /**
     * Restart a service.
     *
     * This is a real `systemctl restart`, not stop-then-start: stop+start drops
     * the listening socket and races the unit back up, which is why restarts
     * from the dashboard appeared to fail.
     */
    public static function restart(string $name): bool {
        $realName = escapeshellarg(self::getRealName($name));
        @shell_exec('sudo systemctl restart ' . $realName . ' 2>&1');
        return self::waitForState($realName, ['active']);
    }

    /**
     * Reload a service's configuration without dropping connections.
     * Returns false when the unit does not support reload.
     */
    public static function reload(string $name): bool {
        $realName = escapeshellarg(self::getRealName($name));
        // A reload issued while the unit is still settling after a restart fails
        // transiently, so wait for it to be active first (returns immediately
        // when it already is).
        self::waitForState($realName, ['active'], 3000);
        $output = [];
        $code = 0;
        @exec('sudo systemctl reload ' . $realName . ' 2>&1', $output, $code);
        return $code === 0;
    }

    /**
     * Check if a port is in use
     */
    public static function isPortInUse(int $port): bool {
        $port = intval($port);
        $output = @shell_exec('ss -tlnp 2>&1 | grep -q ":' . $port . '" && echo "in_use"');
        return $output && trim($output) === 'in_use';
    }

    /**
     * Get resource usage for a service (Linux)
     */
    public static function getResourceUsage(string $name): array {
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
