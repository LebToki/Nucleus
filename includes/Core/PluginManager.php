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
    private static string $serviceDir = '/etc/systemd/system';

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
     * Whether the PHP process runs as root
     */
    private static function isRoot(): bool {
        if (function_exists('posix_geteuid')) {
            return posix_geteuid() === 0;
        }
        return trim((string)@shell_exec('id -u 2>/dev/null')) === '0';
    }

    /**
     * Home directory of the PHP process user
     */
    private static function getPhpHome(): string {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $info = posix_getpwuid(posix_geteuid());
            if (!empty($info['dir'])) {
                return $info['dir'];
            }
        }
        return getenv('HOME') ?: '/var/www';
    }

    /**
     * Whether the PHP process can install to the system bin dir without elevation
     */
    private static function systemBinWritable(): bool {
        return is_writable(self::$binDir);
    }

    /**
     * Whether a plugin of type binary/service needs root (sudo) to install on this system
     */
    public static function needsElevation(array $plugin): bool {
        $type = $plugin['type'] ?? 'binary';
        if ($type === 'webapp') {
            $appRoot = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
            return !is_writable($appRoot);
        }
        return !self::isRoot() && !self::systemBinWritable() && !self::hasPasswordlessSudo();
    }

    /**
     * Whether the PHP user can run sudo without a password (NOPASSWD rule for www-data etc.)
     */
    public static function hasPasswordlessSudo(): bool {
        if (self::isRoot()) {
            return true;
        }
        [$code] = self::runCommand(['sudo', '-n', 'true']);
        return $code === 0;
    }

    /**
     * Resolve how an elevated operation should run.
     * @return array{elevated: bool, passwordless: bool, error: ?string}
     */
    private static function resolveElevation(?string $sudoPassword): array {
        if (self::isRoot() || self::systemBinWritable()) {
            return ['elevated' => false, 'passwordless' => false, 'error' => null];
        }
        if (self::hasPasswordlessSudo()) {
            return ['elevated' => true, 'passwordless' => true, 'error' => null];
        }
        if (!empty($sudoPassword)) {
            if (self::testSudoPassword($sudoPassword)) {
                return ['elevated' => true, 'passwordless' => false, 'error' => null];
            }
            return ['elevated' => true, 'passwordless' => false, 'error' => 'Incorrect sudo password. Nothing was changed.'];
        }
        return ['elevated' => true, 'passwordless' => false, 'error' => 'needs_sudo'];
    }

    /**
     * Validate a sudo password non-interactively (sudo -S). Never stored or logged.
     */
    public static function testSudoPassword(string $password): bool {
        if ($password === '' || $password === null || strlen($password) > 512) {
            return false;
        }
        [$code] = self::sudoRun(['true'], $password);
        return $code === 0;
    }

    /**
     * Run a command with the provided sudo password via stdin (-S),
     * or passwordless via sudo -n when the caller already has NOPASSWD rights.
     * A password lives only in a temporary 0600 file for the duration of the call.
     */
    private static function sudoRun(array $argv, string $password = '', bool $nonInteractive = false): array {
        $cmd = 'sudo ' . ($nonInteractive ? '-n ' : '') . '-p "" ' . implode(' ', array_map('escapeshellarg', $argv)) . ' 2>&1';

        if ($nonInteractive || $password === '') {
            $output = [];
            $code = 0;
            @exec($cmd, $output, $code);
            return [$code, $output];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'nucleus_sudo_');
        if ($tmp === false) {
            return [1, [], 'Could not create temporary file'];
        }
        @chmod($tmp, 0600);
        @file_put_contents($tmp, $password . "\n");

        $fullCmd = $cmd . ' < ' . escapeshellarg($tmp);
        $output = [];
        $code = 0;
        @exec($fullCmd, $output, $code);
        @unlink($tmp);
        return [$code, $output];
    }

    /**
     * Run a command directly (no elevation)
     */
    private static function runCommand(array $argv): array {
        $cmd = implode(' ', array_map('escapeshellarg', $argv)) . ' 2>&1';
        $output = [];
        $code = 0;
        @exec($cmd, $output, $code);
        return [$code, $output];
    }

    /**
     * Run a shell snippet, optionally with elevation (password or NOPASSWD).
     */
    private static function runSnippet(string $snippet, ?string $sudoPassword = null, bool $requireSudo = false, bool $passwordless = false): array {
        if ($requireSudo) {
            return self::sudoRun(['bash', '-c', $snippet], (string)$sudoPassword, $passwordless);
        }
        return self::runCommand(['bash', '-c', $snippet]);
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
                $userBinPath = self::getPhpHome() . '/.local/bin/' . $plugin['binary'];

                $foundPath = null;
                $scope = 'system';
                if (is_file($binaryPath) && is_executable($binaryPath)) {
                    $foundPath = $binaryPath;
                    $scope = 'system';
                } elseif (is_file($userBinPath) && is_executable($userBinPath)) {
                    $foundPath = $userBinPath;
                    $scope = 'user';
                }

                if ($foundPath) {
                    $plugin['installed'] = true;
                    $plugin['binary_path'] = $foundPath;
                    $plugin['scope'] = $scope;

                    $versionOutput = @shell_exec(escapeshellarg($foundPath) . ' version 2>/dev/null');
                    $plugin['installed_version'] = $versionOutput ? trim($versionOutput) : 'unknown';

                    // Service status: check system unit first, then user unit
                    $active = '';
                    if ($scope === 'system') {
                        $serviceStatus = @shell_exec('systemctl is-active ' . escapeshellarg($plugin['service']) . ' 2>/dev/null');
                        $active = trim($serviceStatus ?? '');
                        $enabledStatus = @shell_exec('systemctl is-enabled ' . escapeshellarg($plugin['service']) . ' 2>/dev/null');
                    } else {
                        $serviceStatus = @shell_exec('systemctl --user is-active ' . escapeshellarg($plugin['service']) . ' 2>/dev/null');
                        $active = trim($serviceStatus ?? '');
                        $enabledStatus = @shell_exec('systemctl --user is-enabled ' . escapeshellarg($plugin['service']) . ' 2>/dev/null');
                    }

                    // Fall back to port probe when the unit state is unknown (e.g. user-scope service of another user)
                    if ($active !== 'active' && !empty($plugin['ports'])) {
                        foreach ($plugin['ports'] as $portName => $portNum) {
                            if (self::portOpen((int)$portNum)) {
                                $active = 'active';
                                $plugin['scope'] = 'detected';
                                $plugin['running_scope'] = 'detected';
                                $plugin['detected_via'] = $portName;
                                break;
                            }
                        }
                    }

                    $plugin['running'] = ($active === 'active');
                    $plugin['enabled'] = ($active === 'active');

                    $installed[$key] = $plugin;
                }
            }
        }

        return $installed;
    }

    /**
     * Check if a specific plugin is installed (system or user scope, or port-detectable)
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
        $userBinPath = self::getPhpHome() . '/.local/bin/' . $plugin['binary'];

        if ((is_file($binaryPath) && is_executable($binaryPath)) || (is_file($userBinPath) && is_executable($userBinPath))) {
            return true;
        }

        // Port probe fallback (service may run under another user's scope)
        if (!empty($plugin['ports'])) {
            foreach ($plugin['ports'] as $portName => $portNum) {
                if (self::portOpen((int)$portNum)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check whether a TCP port is accepting connections
     */
    private static function portOpen(int $port): bool {
        $sock = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
        if ($sock) {
            @fclose($sock);
            return true;
        }
        return false;
    }

    /**
     * Install a plugin.
     * @param string $pluginKey Plugin registry key
     * @param string|null $sudoPassword Optional root password used only for this install request
     */
    public static function install(string $pluginKey, ?string $sudoPassword = null): array {
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
                return self::installMailpit($plugin, $sudoPassword);
            default:
                return ['success' => false, 'error' => 'No installer for plugin: ' . $pluginKey];
        }
    }

    /**
     * Uninstall a plugin
     */
    public static function uninstall(string $pluginKey, ?string $sudoPassword = null): array {
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
                return self::uninstallMailpit($plugin, $sudoPassword);
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
     * @param array $plugin Plugin registry entry
     * @param string|null $sudoPassword Optional root password (only used for this request)
     */
    private static function installMailpit(array $plugin, ?string $sudoPassword = null): array {
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

        // Determine whether elevation is required to install into /usr/local/bin + /etc/systemd/system
        $elevation = self::resolveElevation($sudoPassword);
        if ($elevation['error'] !== null) {
            // Clean up downloaded artifacts before prompting
            @unlink($tmpFile);
            @array_map('unlink', glob($tmpDir . '/*'));
            @rmdir($tmpDir);
            if ($elevation['error'] === 'needs_sudo') {
                return [
                    'success' => false,
                    'needs_sudo' => true,
                    'error' => 'Installing this node requires root privileges. Enter your sudo password, or grant the web user passwordless sudo with: sudo sh -c \'echo "www-data ALL=(root) NOPASSWD: /usr/bin/install, /usr/bin/systemctl, /bin/rm, /usr/bin/chmod, /usr/bin/tar" > /etc/sudoers.d/nucleus-nodes\'',
                ];
            }
            return ['success' => false, 'error' => $elevation['error']];
        }
        $elevated = $elevation['elevated'];
        $passwordless = $elevation['passwordless'];

        // Install binary + service unit + enable/start, elevated if required
        $snippet = 'install -o root -g root -m 0755 ' . escapeshellarg($extractedBinary) . ' ' . escapeshellarg($binaryPath)
            . ' && systemctl daemon-reload && systemctl enable mailpit && systemctl restart mailpit';
        [$code, $snippetOutput] = self::runSnippet($snippet, $sudoPassword, $elevated, $passwordless);

        // Clean up temp files
        @unlink($tmpFile);
        @array_map('unlink', glob($tmpDir . '/*'));
        @rmdir($tmpDir);

        if ($code !== 0 || !is_file($binaryPath)) {
            return ['success' => false, 'error' => 'Failed to install Mailpit: ' . trim(implode("\n", $snippetOutput))];
        }

        // Save plugin state
        self::savePluginState('mailpit', [
            'installed' => true,
            'installed_at' => date('c'),
            'version' => 'latest',
            'scope' => $elevated ? 'system' : 'user',
        ]);

        return [
            'success' => true,
            'message' => 'Mailpit installed successfully',
            'web_url' => 'http://localhost:8025',
            'smtp_port' => 1025,
            'scope' => $elevated ? 'system' : 'user',
        ];
    }

    /**
     * Create Mailpit systemd service (system scope)
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

        $serviceFile = self::$serviceDir . '/mailpit.service';
        $tmpFile = sys_get_temp_dir() . '/mailpit.service';

        // Write to temp file first, then sudo copy
        @file_put_contents($tmpFile, $serviceContent);
        [$code] = self::runSnippet(
            'install -o root -g root -m 0644 ' . escapeshellarg($tmpFile) . ' ' . escapeshellarg($serviceFile)
                . ' && systemctl daemon-reload',
            null,
            true
        );
        @unlink($tmpFile);

        return $code === 0 && file_exists($serviceFile);
    }

    /**
     * Uninstall Mailpit
     */
    private static function uninstallMailpit(array $plugin, ?string $sudoPassword = null): array {
        $elevation = self::resolveElevation($sudoPassword);
        if ($elevation['error'] !== null) {
            return ['success' => false, 'error' => $elevation['error'] === 'needs_sudo' ? 'Root access is required to uninstall this node.' : $elevation['error']];
        }
        $elevated = $elevation['elevated'];
        $passwordless = $elevation['passwordless'];

        // Stop and disable service
        self::runSnippet('systemctl stop mailpit; systemctl disable mailpit', $sudoPassword, $elevated, $passwordless);

        // Remove service file
        $serviceFile = self::$serviceDir . '/mailpit.service';
        if (file_exists($serviceFile)) {
            self::runSnippet('rm -f ' . escapeshellarg($serviceFile) . ' && systemctl daemon-reload', $sudoPassword, $elevated, $passwordless);
        }

        // Remove binary
        $binaryPath = self::$binDir . '/' . $plugin['binary'];
        if (file_exists($binaryPath)) {
            self::runSnippet('rm -f ' . escapeshellarg($binaryPath), $sudoPassword, $elevated, $passwordless);
        }

        // Remove plugin state
        self::removePluginState('mailpit');

        return ['success' => true, 'message' => 'Mailpit uninstalled successfully'];
    }

    /**
     * Start a plugin's service (system unit, or user unit when not elevated)
     */
    public static function startService(string $pluginKey, ?string $sudoPassword = null): array {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return ['success' => false, 'error' => 'Unknown plugin'];
        }

        $service = $available[$pluginKey]['service'];
        $elevation = self::resolveElevation($sudoPassword);
        if ($elevation['error'] !== null) {
            return ['success' => false, 'needs_sudo' => $elevation['error'] === 'needs_sudo', 'error' => $elevation['error'] === 'needs_sudo' ? 'Root access is required to start this node.' : $elevation['error']];
        }
        $elevated = $elevation['elevated'];
        $passwordless = $elevation['passwordless'];

        self::runSnippet('systemctl start ' . escapeshellarg($service), $sudoPassword, $elevated, $passwordless);

        $status = '';
        if ($elevated) {
            $status = trim(@shell_exec('systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
        } else {
            $status = trim(@shell_exec('systemctl --user is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
            if ($status !== 'active') {
                $status = trim(@shell_exec('systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
            }
        }

        return [
            'success' => $status === 'active',
            'message' => $status === 'active' ? 'Service started' : 'Failed to start service',
        ];
    }

    /**
     * Stop a plugin's service
     */
    public static function stopService(string $pluginKey, ?string $sudoPassword = null): array {
        $available = self::getAvailablePlugins();
        if (!isset($available[$pluginKey])) {
            return ['success' => false, 'error' => 'Unknown plugin'];
        }

        $service = $available[$pluginKey]['service'];
        $elevation = self::resolveElevation($sudoPassword);
        if ($elevation['error'] !== null) {
            return ['success' => false, 'needs_sudo' => $elevation['error'] === 'needs_sudo', 'error' => $elevation['error'] === 'needs_sudo' ? 'Root access is required to stop this node.' : $elevation['error']];
        }
        $elevated = $elevation['elevated'];
        $passwordless = $elevation['passwordless'];

        self::runSnippet('systemctl stop ' . escapeshellarg($service), $sudoPassword, $elevated, $passwordless);

        $status = '';
        if ($elevated) {
            $status = trim(@shell_exec('systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
        } else {
            $status = trim(@shell_exec('systemctl --user is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
            if ($status !== 'inactive') {
                $status = trim(@shell_exec('systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null') ?? '');
            }
        }

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
