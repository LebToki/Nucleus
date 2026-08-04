<?php

namespace Nucleus\Core;

/**
 * PluginManager Class
 * Version: 1.0.0
 * Handles plugin discovery, installation, and lifecycle management
 */
class PluginManager {

    private static string $pluginsDir = '';
    private static string $binDir = '/usr/local/bin';

    /**
     * Get the plugins data directory
     */
    private static function getPluginsDir(): string {
        if (empty(self::$pluginsDir)) {
            self::$pluginsDir = (defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2)) . '/data/plugins';
            if (!is_dir(self::$pluginsDir)) {
                @mkdir(self::$pluginsDir, 0755, true);
            }
        }
        return self::$pluginsDir;
    }

    /**
     * Get the registry of available plugins (not installed, but known)
     */
    public static function getAvailablePlugins(): array {
        return [
            'mailpit' => [
                'name' => 'Mailpit',
                'description' => 'Email testing tool — catches all outgoing mail with a web UI for inspection',
                'version' => 'latest',
                'author' => 'Axllent',
                'url' => 'https://github.com/axllent/mailpit',
                'icon' => 'solar:mailbox-bold',
                'color' => 'purple',
                'type' => 'binary',
                'ports' => ['smtp' => 1025, 'web' => 8025],
                'requires' => [],
                'arch' => [
                    'x86_64' => 'mailpit_linux_amd64.tar.gz',
                    'aarch64' => 'mailpit_linux_arm64.tar.gz',
                    'armv7l' => 'mailpit_linux_armv7.tar.gz',
                ],
                'binary' => 'mailpit',
                'service' => 'mailpit',
                'install_size' => '~15 MB',
            ],
            'phpmyadmin' => [
                'name' => 'phpMyAdmin',
                'description' => 'Web-based MySQL/MariaDB administration tool — download and install into the dashboard root',
                'version' => '5.2.2',
                'author' => 'phpMyAdmin Project',
                'url' => 'https://www.phpmyadmin.net/',
                'icon' => 'tabler:brand-mysql',
                'color' => 'info',
                'type' => 'webapp',
                'ports' => ['web' => 80],
                'requires' => ['php-mysql', 'php-zip'],
                'download_url' => 'https://files.phpmyadmin.net/phpMyAdmin/5.2.2/phpMyAdmin-5.2.2-all-languages.zip',
                'install_dir' => 'phpmyadmin',
                'install_size' => '~45 MB',
            ],
        ];
    }

    /**
     * Get installed plugins with their status
     */
    public static function getInstalledPlugins(): array {
        $available = self::getAvailablePlugins();
        $installed = [];

        foreach ($available as $key => $plugin) {
            $type = $plugin['type'] ?? 'binary';

            if ($type === 'webapp') {
                $appRoot = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
                $installPath = $appRoot . '/' . ($plugin['install_dir'] ?? $key);
                if (is_dir($installPath)) {
                    $plugin['installed'] = true;
                    $plugin['install_path'] = $installPath;
                    $plugin['installed_version'] = $plugin['version'] ?? 'unknown';
                    $plugin['running'] = true;
                    $plugin['enabled'] = true;
                    $installed[$key] = $plugin;
                }
            } else {
                $binaryPath = self::$binDir . '/' . $plugin['binary'];
                if (is_file($binaryPath) && is_executable($binaryPath)) {
                    $plugin['installed'] = true;
                    $plugin['binary_path'] = $binaryPath;

                    $versionOutput = @shell_exec(escapeshellarg($binaryPath) . ' version 2>/dev/null');
                    $plugin['installed_version'] = $versionOutput ? trim($versionOutput) : 'unknown';

                    $serviceStatus = @shell_exec('systemctl is-active ' . escapeshellarg($plugin['service']) . ' 2>/dev/null');
                    $plugin['running'] = (trim($serviceStatus ?? '') === 'active');

                    $enabledStatus = @shell_exec('systemctl is-enabled ' . escapeshellarg($plugin['service']) . ' 2>/dev/null');
                    $plugin['enabled'] = (trim($enabledStatus ?? '') === 'enabled');

                    $installed[$key] = $plugin;
                }
            }
        }

        return $installed;
    }

    /**
     * Check if a specific plugin is installed
     */
    public static function isInstalled(string $pluginKey): bool {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return false;
        }

        $plugin = $available[$pluginKey];
        $type = $plugin['type'] ?? 'binary';

        if ($type === 'webapp') {
            $appRoot = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
            $installPath = $appRoot . '/' . ($plugin['install_dir'] ?? $pluginKey);
            return is_dir($installPath);
        }

        $binaryPath = self::$binDir . '/' . $plugin['binary'];
        return is_file($binaryPath) && is_executable($binaryPath);
    }

    /**
     * Install a plugin
     */
    public static function install(string $pluginKey): array {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return ['success' => false, 'error' => 'Unknown plugin: ' . $pluginKey];
        }

        $plugin = $available[$pluginKey];

        if (self::isInstalled($pluginKey)) {
            return ['success' => false, 'error' => 'Plugin already installed'];
        }

        $type = $plugin['type'] ?? 'binary';

        if ($type === 'webapp') {
            return self::installWebapp($pluginKey, $plugin);
        }

        switch ($pluginKey) {
            case 'mailpit':
                return self::installMailpit($plugin);
            default:
                return ['success' => false, 'error' => 'No installer for plugin: ' . $pluginKey];
        }
    }

    /**
     * Uninstall a plugin
     */
    public static function uninstall(string $pluginKey): array {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return ['success' => false, 'error' => 'Unknown plugin: ' . $pluginKey];
        }

        $plugin = $available[$pluginKey];

        if (!self::isInstalled($pluginKey)) {
            return ['success' => false, 'error' => 'Plugin not installed'];
        }

        $type = $plugin['type'] ?? 'binary';

        if ($type === 'webapp') {
            return self::uninstallWebapp($pluginKey, $plugin);
        }

        switch ($pluginKey) {
            case 'mailpit':
                return self::uninstallMailpit($plugin);
            default:
                return ['success' => false, 'error' => 'No uninstaller for plugin: ' . $pluginKey];
        }
    }

    /**
     * Install a webapp plugin (download archive → extract into APP_ROOT)
     */
    private static function installWebapp(string $pluginKey, array $plugin): array {
        $downloadUrl = $plugin['download_url'] ?? '';
        if (empty($downloadUrl)) {
            return ['success' => false, 'error' => 'No download URL configured for ' . $plugin['name']];
        }

        $appRoot = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
        $installDir = $plugin['install_dir'] ?? $pluginKey;
        $installPath = $appRoot . '/' . $installDir;

        if (is_dir($installPath)) {
            return ['success' => false, 'error' => $plugin['name'] . ' is already installed at ' . $installDir . '/'];
        }

        $tmpDir = sys_get_temp_dir() . '/nucleus_' . $pluginKey . '_' . time();
        if (!@mkdir($tmpDir, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create temp directory'];
        }

        $archivePath = $tmpDir . '/' . $installDir . '.zip';

        // Download
        if (function_exists('curl_init')) {
            $ch = curl_init($downloadUrl);
            $fp = fopen($archivePath, 'wb');
            if ($ch && $fp) {
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Nucleus-Dashboard/1.0.1');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                fclose($fp);
                if ($result === false || $httpCode < 200 || $httpCode >= 300) {
                    @unlink($archivePath);
                    @rmdir($tmpDir);
                    return ['success' => false, 'error' => 'Failed to download ' . $plugin['name'] . ' (HTTP ' . $httpCode . ')'];
                }
            } else {
                @fclose($fp);
                @curl_close($ch);
                @unlink($archivePath);
                @rmdir($tmpDir);
                return ['success' => false, 'error' => 'Failed to initialize download'];
            }
        } else {
            $content = @file_get_contents($downloadUrl);
            if ($content === false) {
                @rmdir($tmpDir);
                return ['success' => false, 'error' => 'Failed to download ' . $plugin['name']];
            }
            file_put_contents($archivePath, $content);
        }

        if (!file_exists($archivePath) || filesize($archivePath) < 1000) {
            @unlink($archivePath);
            @rmdir($tmpDir);
            return ['success' => false, 'error' => 'Downloaded file is empty or invalid'];
        }

        // Extract
        $extractDir = $tmpDir . '/extracted';
        @mkdir($extractDir, 0755, true);

        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($archivePath) === true) {
                $zip->extractTo($extractDir);
                $zip->close();
            } else {
                @unlink($archivePath);
                @rmdir($extractDir);
                @rmdir($tmpDir);
                return ['success' => false, 'error' => 'Failed to extract archive (corrupt zip?)'];
            }
        } else {
            $output = @shell_exec('unzip -o ' . escapeshellarg($archivePath) . ' -d ' . escapeshellarg($extractDir) . ' 2>&1');
            if (!is_dir($extractDir) || count(array_diff(scandir($extractDir), ['.', '..'])) === 0) {
                @unlink($archivePath);
                @rmdir($extractDir);
                @rmdir($tmpDir);
                return ['success' => false, 'error' => 'Failed to extract archive (install php-zip or unzip)'];
            }
        }

        // Find source dir (strip top-level if archive wraps everything in a folder)
        $sourceDir = $extractDir;
        $entries = array_diff(scandir($extractDir), ['.', '..']);
        if (count($entries) === 1) {
            $single = $extractDir . '/' . reset($entries);
            if (is_dir($single)) {
                $sourceDir = $single;
            }
        }

        // Copy to install path
        if (!@mkdir($installPath, 0755, true)) {
            self::removeDir($tmpDir);
            return ['success' => false, 'error' => 'Failed to create install directory'];
        }

        self::copyDir($sourceDir, $installPath);

        // Cleanup
        self::removeDir($tmpDir);

        self::savePluginState($pluginKey, [
            'installed' => true,
            'installed_at' => date('c'),
            'version' => $plugin['version'] ?? 'unknown',
        ]);

        return [
            'success' => true,
            'message' => $plugin['name'] . ' installed successfully',
            'web_url' => '/' . $installDir . '/',
        ];
    }

    /**
     * Uninstall a webapp plugin (remove its directory from APP_ROOT)
     */
    private static function uninstallWebapp(string $pluginKey, array $plugin): array {
        $appRoot = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
        $installDir = $plugin['install_dir'] ?? $pluginKey;
        $installPath = $appRoot . '/' . $installDir;

        if (!is_dir($installPath)) {
            return ['success' => false, 'error' => $plugin['name'] . ' is not installed'];
        }

        if (!self::removeDir($installPath)) {
            return ['success' => false, 'error' => 'Failed to remove ' . $installDir . '/ — check permissions'];
        }

        self::removePluginState($pluginKey);

        return ['success' => true, 'message' => $plugin['name'] . ' uninstalled successfully'];
    }

    /**
     * Recursively copy a directory
     */
    private static function copyDir(string $src, string $dst): bool {
        if (!is_dir($src)) return false;
        if (!is_dir($dst)) @mkdir($dst, 0755, true);

        $items = scandir($src);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $srcPath = $src . '/' . $item;
            $dstPath = $dst . '/' . $item;
            if (is_dir($srcPath)) {
                self::copyDir($srcPath, $dstPath);
            } else {
                @copy($srcPath, $dstPath);
            }
        }
        return true;
    }

    /**
     * Recursively remove a directory
     */
    private static function removeDir(string $dir): bool {
        if (!is_dir($dir)) return true;
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? self::removeDir($path) : @unlink($path);
        }
        return @rmdir($dir);
    }

    /**
     * Install Mailpit
     */
    private static function installMailpit(array $plugin): array {
        $arch = trim(@shell_exec('uname -m') ?? '');
        $archMap = $plugin['arch'];

        if (!isset($archMap[$arch])) {
            return ['success' => false, 'error' => 'Unsupported architecture: ' . $arch];
        }

        $assetName = $archMap[$arch];
        $downloadUrl = 'https://github.com/axllent/mailpit/releases/latest/download/' . $assetName;
        $tmpDir = sys_get_temp_dir() . '/nucleus_mailpit_' . time();
        $tmpFile = $tmpDir . '/' . $assetName;

        // Create temp directory
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }

        // Download
        $command = 'curl -sL ' . escapeshellarg($downloadUrl) . ' -o ' . escapeshellarg($tmpFile) . ' 2>&1';
        @exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($tmpFile) || filesize($tmpFile) < 1000) {
            @unlink($tmpFile);
            @rmdir($tmpDir);
            return ['success' => false, 'error' => 'Failed to download Mailpit'];
        }

        // Extract
        $command = 'tar -xzf ' . escapeshellarg($tmpFile) . ' -C ' . escapeshellarg($tmpDir) . ' 2>&1';
        @exec($command, $output, $returnVar);

        $binaryPath = self::$binDir . '/' . $plugin['binary'];
        $extractedBinary = $tmpDir . '/mailpit';

        if (!file_exists($extractedBinary)) {
            // Clean up
            @unlink($tmpFile);
            @array_map('unlink', glob($tmpDir . '/*'));
            @rmdir($tmpDir);
            return ['success' => false, 'error' => 'Failed to extract Mailpit binary'];
        }

        // Install binary (requires sudo)
        $command = 'sudo cp ' . escapeshellarg($extractedBinary) . ' ' . escapeshellarg($binaryPath) . ' && sudo chmod 755 ' . escapeshellarg($binaryPath) . ' 2>&1';
        @exec($command, $output, $returnVar);

        // Clean up temp files
        @unlink($tmpFile);
        @array_map('unlink', glob($tmpDir . '/*'));
        @rmdir($tmpDir);

        if ($returnVar !== 0 || !file_exists($binaryPath)) {
            return ['success' => false, 'error' => 'Failed to install binary. Check sudo permissions.'];
        }

        // Create systemd service
        $serviceResult = self::createMailpitService();
        if (!$serviceResult) {
            return ['success' => false, 'error' => 'Binary installed but failed to create systemd service'];
        }

        // Enable and start
        @exec('sudo systemctl enable mailpit 2>&1');
        @exec('sudo systemctl start mailpit 2>&1');

        // Save plugin state
        self::savePluginState('mailpit', [
            'installed' => true,
            'installed_at' => date('c'),
            'version' => 'latest',
        ]);

        return [
            'success' => true,
            'message' => 'Mailpit installed successfully',
            'web_url' => 'http://localhost:8025',
            'smtp_port' => 1025,
        ];
    }

    /**
     * Create Mailpit systemd service
     */
    private static function createMailpitService(): bool {
        $serviceContent = <<<'SYSTEMD'
[Unit]
Description=Mailpit - Email testing tool
Documentation=https://github.com/axllent/mailpit
After=network.target

[Service]
Type=simple
ExecStart=/usr/local/bin/mailpit
Restart=on-failure
RestartSec=5
Environment=MP_SMTP_LISTEN=0.0.0.0:1025
Environment=MP_UI_LISTEN=0.0.0.0:8025

[Install]
WantedBy=multi-user.target
SYSTEMD;

        $serviceFile = '/etc/systemd/system/mailpit.service';
        $tmpFile = sys_get_temp_dir() . '/mailpit.service';

        // Write to temp file first, then sudo copy
        @file_put_contents($tmpFile, $serviceContent);
        $command = 'sudo cp ' . escapeshellarg($tmpFile) . ' ' . escapeshellarg($serviceFile)
            . ' && sudo systemctl daemon-reload 2>&1';
        @exec($command, $output, $returnVar);
        @unlink($tmpFile);

        return $returnVar === 0 && file_exists($serviceFile);
    }

    /**
     * Uninstall Mailpit
     */
    private static function uninstallMailpit(array $plugin): array {
        // Stop and disable service
        @exec('sudo systemctl stop mailpit 2>&1');
        @exec('sudo systemctl disable mailpit 2>&1');

        // Remove service file
        $serviceFile = '/etc/systemd/system/mailpit.service';
        if (file_exists($serviceFile)) {
            @exec('sudo rm ' . escapeshellarg($serviceFile) . ' 2>&1');
            @exec('sudo systemctl daemon-reload 2>&1');
        }

        // Remove binary
        $binaryPath = self::$binDir . '/' . $plugin['binary'];
        if (file_exists($binaryPath)) {
            @exec('sudo rm ' . escapeshellarg($binaryPath) . ' 2>&1');
        }

        // Remove plugin state
        self::removePluginState('mailpit');

        return ['success' => true, 'message' => 'Mailpit uninstalled successfully'];
    }

    /**
     * Start a plugin's service
     */
    public static function startService(string $pluginKey): array {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return ['success' => false, 'error' => 'Unknown plugin'];
        }

        $service = $available[$pluginKey]['service'];
        @exec('sudo systemctl start ' . escapeshellarg($service) . ' 2>&1', $output, $returnVar);

        $status = trim(@shell_exec('systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
        return [
            'success' => $status === 'active',
            'message' => $status === 'active' ? 'Service started' : 'Failed to start service',
        ];
    }

    /**
     * Stop a plugin's service
     */
    public static function stopService(string $pluginKey): array {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return ['success' => false, 'error' => 'Unknown plugin'];
        }

        $service = $available[$pluginKey]['service'];
        @exec('sudo systemctl stop ' . escapeshellarg($service) . ' 2>&1', $output, $returnVar);

        $status = trim(@shell_exec('systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
        return [
            'success' => $status !== 'active',
            'message' => $status !== 'active' ? 'Service stopped' : 'Failed to stop service',
        ];
    }

    /**
     * Save plugin state to disk
     */
    private static function savePluginState(string $key, array $state): void {
        $file = self::getPluginsDir() . '/' . $key . '.json';
        @file_put_contents($file, json_encode($state, JSON_PRETTY_PRINT));
    }

    /**
     * Remove plugin state
     */
    private static function removePluginState(string $key): void {
        $file = self::getPluginsDir() . '/' . $key . '.json';
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Get plugin state
     */
    public static function getPluginState(string $key): array {
        $file = self::getPluginsDir() . '/' . $key . '.json';
        if (file_exists($file)) {
            $data = @json_decode(file_get_contents($file), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }
}
