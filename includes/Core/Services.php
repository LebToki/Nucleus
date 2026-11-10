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
     * Run one whitelisted action through the privileged helper.
     *
     * The web process is not root. /usr/local/bin/nucleus-service accepts only
     * (start|stop|restart|reload) on a fixed unit list, and is the single
     * command www-data may sudo. Using `sudo systemctl ...` directly both
     * required broad sudo rights and silently failed.
     */
    private static function run(string $action, string $name): bool {
        $realName = escapeshellarg(self::getRealName($name));
        $output = [];
        $code = 0;
        @exec('sudo -n /usr/local/bin/nucleus-service ' . $action . ' ' . $realName . ' 2>&1', $output, $code);
        return $code === 0;
    }

    /**
     * Start a service
     */
    public static function start(string $name): bool {
        if (!self::run('start', $name)) {
            return false;
        }
        return self::waitForState(escapeshellarg(self::getRealName($name)), ['active']);
    }

    /**
     * Stop a service
     */
    public static function stop(string $name): bool {
        if (!self::run('stop', $name)) {
            return false;
        }
        return self::waitForState(escapeshellarg(self::getRealName($name)), ['inactive', 'failed']);
    }

    /**
     * Restart a service.
     *
     * A real `systemctl restart`, not stop-then-start: stop+start dropped the
     * listening socket and raced the unit back up.
     */
    public static function restart(string $name): bool {
        if (!self::run('restart', $name)) {
            return false;
        }
        return self::waitForState(escapeshellarg(self::getRealName($name)), ['active']);
    }

    /**
     * Reload a service's configuration without dropping connections.
     */
    public static function reload(string $name): bool {
        return self::run('reload', $name);
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
