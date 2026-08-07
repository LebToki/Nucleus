<?php
/**
 * Nucleus - Helper Functions
 * Version: 4.0.3
 * Provides utility functions for the dashboard
 */

// Load core autoloader to ensure dependent classes are available
if (file_exists(__DIR__ . '/autoload.php')) {
    require_once __DIR__ . '/autoload.php';
}

// Start output buffering to prevent stray output
ob_start();

/**
 * Standalone wrapper for Security::getCSRFToken()
 * Used by layoutTop.php and AJAX scripts
 */
if (!function_exists('getCSRFToken')) {
    function getCSRFToken(): string {
        return \Nucleus\Core\Security::getCSRFToken();
    }
}

/**
 * Standalone wrapper for Security::check_auth()
 * Used by index.php entry point
 */
if (!function_exists('check_auth')) {
    function check_auth(): void {
        \Nucleus\Core\Security::checkAuth();
    }
}

// Note: getLaragonRoot(), getLaragonSendmailDir(), getLaragonDomainSuffix(), getAppVersion()
// are defined in config.php which loads before this file.

/**
 * Get Apache version
 */
if (!function_exists('getApacheVersion')) {
    function getApacheVersion() {
        // Try apache2ctl first (Debian/Ubuntu)
        $output = @shell_exec('apache2ctl -v 2>&1');
        if ($output && preg_match('/(\d+\.\d+\.\d+)/', $output, $matches)) {
            return $matches[1];
        }

        // Try httpd (RHEL/CentOS/Fedora)
        $output = @shell_exec('httpd -v 2>&1');
        if ($output && preg_match('/Apache\/(\d+\.\d+\.\d+)/', $output, $matches)) {
            return $matches[1];
        }

        return 'Apache2';
    }
}

/**
 * Get current PHP version (simplified to avoid truncation)
 */
if (!function_exists('getCurrentPHPVersion')) {
    function getCurrentPHPVersion() {
        // Simply return the running PHP version to avoid truncation
        return htmlspecialchars(PHP_VERSION);
    }
}

/**
 * Get MySQL version
 */
if (!function_exists('getMySQLVersion')) {
    function getMySQLVersion() {
        // Try mysql CLI first
        $output = @shell_exec('mysql -V 2>&1');
        if ($output && preg_match('/(\d+\.\d+\.\d+)/', $output, $matches)) {
            return $matches[1] . ' (MySQL)';
        }

        // Try mariadb
        $output = @shell_exec('mariadb -V 2>&1');
        if ($output && preg_match('/(\d+\.\d+\.\d+)/', $output, $matches)) {
            return $matches[1] . ' (MariaDB)';
        }

        return 'MySQL/MariaDB';
    }
}

/**
 * Check if phpMyAdmin is installed
 */
if (!function_exists('isPhpMyAdminInstalled')) {
    function isPhpMyAdminInstalled() {
        $linuxPaths = [
            '/usr/share/phpmyadmin',
            '/usr/share/phpMyAdmin',
            '/var/www/html/phpmyadmin',
            '/var/www/html/phpMyAdmin',
            '/usr/local/share/phpmyadmin',
        ];
        foreach ($linuxPaths as $p) {
            if (is_dir($p)) return true;
        }
        return false;
    }
}

/**
 * Get phpMyAdmin version
 */
if (!function_exists('getPhpMyAdminVersion')) {
    function getPhpMyAdminVersion() {
        $linuxPaths = [
            '/usr/share/phpmyadmin',
            '/usr/share/phpMyAdmin',
            '/var/www/html/phpmyadmin',
            '/var/www/html/phpMyAdmin',
        ];
        foreach ($linuxPaths as $pmaPath) {
            if (is_dir($pmaPath)) {
                $versionFile = $pmaPath . '/README';
                if (file_exists($versionFile)) {
                    $content = @file_get_contents($versionFile);
                    if (preg_match('/Version\s+(\d+\.\d+\.\d+)/', $content, $matches)) {
                        return $matches[1];
                    }
                }
            }
        }
        return null;
    }
}

/**
 * Check if Adminer is installed
 */
if (!function_exists('isAdminerInstalled')) {
    function isAdminerInstalled() {
        if (!class_exists('AdminerModule')) {
            require_once __DIR__ . '/AdminerModule.php';
        }
        $adminer = new AdminerModule();
        return $adminer->isInstalled();
    }
}

/**
 * Get Adminer URL
 */
if (!function_exists('getAdminerUrl')) {
    function getAdminerUrl($database = null) {
        if (!class_exists('AdminerModule')) {
            require_once __DIR__ . '/AdminerModule.php';
        }
        $adminer = new AdminerModule();
        return $adminer->getUrl($database);
    }
}

/**
 * Get OpenSSL version
 */
if (!function_exists('getOpenSSLVersion')) {
    function getOpenSSLVersion() {
        $output = @shell_exec('openssl version 2>&1');
        if ($output && preg_match('/OpenSSL\s+(\S+)/', $output, $matches)) {
            return $matches[1];
        }
        
        return 'OpenSSL';
    }
}

/**
 * Format raw log text to structured HTML
 */
if (!function_exists('formatLogToHtml')) {
    function formatLogToHtml($text) {
        if (empty($text)) return '';
        
        $lines = explode("\n", $text);
        $html = '<div class="table-responsive"><table class="table table-sm table-hover text-xs mb-0"><tbody>';
        
        foreach ($lines as $line) {
            $rowLine = trim($line);
            if (empty($rowLine)) continue;
            
            $bgClass = '';
            $textClass = '';
            
            if (stripos($rowLine, 'error') !== false || stripos($rowLine, 'fatal') !== false || stripos($rowLine, 'critical') !== false) {
                $bgClass = 'bg-danger-50';
                $textClass = 'text-danger-main';
            } elseif (stripos($rowLine, 'warn') !== false) {
                $bgClass = 'bg-warning-50';
                $textClass = 'text-warning-main';
            } elseif (stripos($rowLine, 'info') !== false) {
                $bgClass = 'bg-info-50';
                $textClass = 'text-info-main';
            }
            
            $html .= '<tr class="' . $bgClass . '">';
            $html .= '<td class="font-monospace ' . $textClass . '">' . htmlspecialchars($line) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></div>';
        return $html;
    }
}

/**
 * Get PHP SAPI
 */
if (!function_exists('getPHPSAPI')) {
    function getPHPSAPI() {
        return PHP_SAPI;
    }
}

/**
 * Get document root
 */
if (!function_exists('getDocumentRoot')) {
    function getDocumentRoot() {
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if (!empty($docRoot)) {
            return $docRoot;
        }

        $laragonRoot = getLaragonRoot();
        return $laragonRoot . '/html';
    }
}

/**
 * Get Nucleus version (replaces legacy Laragon version)
 */
if (!function_exists('getLaragonVersion')) {
    function getLaragonVersion() {
        return 'Nucleus';
    }
}

/**
 * Get all projects from www directory
 */
if (!function_exists('getAllProjects')) {
    function getAllProjects() {
        // Use platform-aware www path detection
        if (class_exists('\Nucleus\Core\System') && method_exists('\Nucleus\Core\System', 'getWwwPath')) {
            $wwwPath = \Nucleus\Core\System::getWwwPath();
        } else {
            $laragonRoot = getLaragonRoot();
            $wwwPath = $laragonRoot . '/html';
        }

        if (!is_dir($wwwPath)) {
            return [];
        }
        
        $projects = [];
        $ignoredProjects = getIgnoredProjects();
        
        // Create cache directory if it doesn't exist
        $cacheDir = dirname(__DIR__) . '/cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/projects_cache.json';
        
        $cache = [];
        if (file_exists($cacheFile)) {
            $content = @file_get_contents($cacheFile);
            if ($content !== false) {
                $decoded = @json_decode($content, true);
                if ($decoded !== null && is_array($decoded)) {
                    $cache = $decoded;
                }
            }
        }
        
        $dirs = @glob($wwwPath . '/*', GLOB_ONLYDIR);
        $cacheUpdated = false;
        
        if ($dirs !== false) {
            foreach ($dirs as $dir) {
                $name = basename($dir);
                
                // Validate directory name to prevent any potential injection
                // Exclude dashboard infrastructure, system dirs, and non-project folders
                $excludedDirs = [
                    // Dashboard infrastructure
                    'laragon', 'laragon-dashboard', 'dashboard', 'assets', 'build',
                    'includes', 'partials', 'pages', 'api', 'i18n', 'cache', 'data', 'logs',
                    // System / admin tools
                    'phpmyadmin', 'adminer', 'phppgadmin', 'html',
                    // Non-project directories
                    'nucleus-logo',
                ];
                // Skip hidden dirs and excluded dirs
                if (empty($name) || $name[0] === '.' || in_array($name, $excludedDirs, true)) {
                    continue;
                }
                
                if (in_array($name, $ignoredProjects, true)) {
                    continue;
                }
                
                $mtime = @filemtime($dir);
                if ($mtime === false) {
                    continue; // Skip if we can't get the modification time
                }
                
                if (isset($cache[$name]) && $cache[$name]['mtime'] === $mtime) {
                    $projects[] = $cache[$name]['data'];
                } else {
                    $projectData = analyzeProject($dir, $name);
                    if ($projectData) {
                        $projects[] = $projectData;
                        $cache[$name] = [
                            'mtime' => $mtime,
                            'data' => $projectData
                        ];
                        $cacheUpdated = true;
                    }
                }
            }
        }
        
        if ($cacheUpdated) {
            @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT));
        }
        
        return $projects;
    }
}

/**
 * Analyze a project directory
 */
if (!function_exists('analyzeProject')) {
    function analyzeProject($path, $name) {
    $project = [
        'name' => $name,
        'path' => $path,
        'url' => 'http://' . $name . '.local',
        'platform' => 'Unknown',
        'icon' => 'solar:folder-bold',
        'iconify' => null,
        'color' => 'primary',
        'is_wordpress' => false,
        'has_composer' => file_exists($path . '/composer.json'),
        'has_npm' => file_exists($path . '/package.json'),
        'has_git' => is_dir($path . '/.git') || is_file($path . '/.git'),
        'git_branch' => null,
        'git_status' => null,
        'favicon' => null,
    ];
    
    // Check for WordPress
    if (file_exists($path . '/wp-config.php') || file_exists($path . '/wp-includes/version.php')) {
        $project['platform'] = 'WordPress';
        $project['icon'] = 'devicon-plain:wordpress';
        $project['color'] = 'primary';
        $project['is_wordpress'] = true;

        // Quick look for WordPress favicon with fallback chain: .ico → .png → default
        $commonPaths = [['/wp-content/uploads/favicon.ico', '/wp-content/uploads/favicon.png'], ['/favicon.ico', '/favicon.png'], ['/favicon.png']];
        foreach ($commonPaths as $pathSet) {
            foreach ($pathSet as $relPath) {
                if (file_exists($path . $relPath)) {
                    $project['favicon'] = $name . $relPath;
                    break 2;
                }
            }
        }
        // Fallback to default asset if no favicon found
        if (empty($project['favicon'])) {
            $project['favicon'] = 'assets/images/favicon/favicon-32x32.png';
        }
    }
    // Check for Laravel
    elseif (file_exists($path . '/artisan')) {
        $project['platform'] = 'Laravel';
        $project['icon'] = 'devicon-plain:laravel';
        $project['color'] = 'danger';
        // Try favicon.ico first, then favicon.png
        if (file_exists($path . '/public/favicon.ico')) $project['favicon'] = $name . '/public/favicon.ico';
        elseif (file_exists($path . '/public/favicon.png')) $project['favicon'] = $name . '/public/favicon.png';
        else $project['favicon'] = 'assets/images/favicon/favicon-32x32.png';
    }
    // Check for Drupal
    elseif (file_exists($path . '/core/lib/Drupal.php') || file_exists($path . '/autoload.php')) {
        $project['platform'] = 'Drupal';
        $project['icon'] = 'devicon-plain:drupal';
        $project['color'] = 'info';
    }
    // Check for CodeIgniter
    elseif (file_exists($path . '/index.php') && file_exists($path . '/system/core/Controller.php')) {
        $project['platform'] = 'CodeIgniter';
        $project['icon'] = 'devicon-plain:codeigniter';
        $project['color'] = 'warning';
    }
    // Check for Symfony
    elseif (file_exists($path . '/bin/console') || (is_dir($path . '/src') && file_exists($path . '/composer.json') && strpos(file_get_contents($path . '/composer.json'), 'symfony/framework-bundle') !== false)) {
        $project['platform'] = 'Symfony';
        $project['icon'] = 'devicon-plain:symfony';
        $project['color'] = 'dark';
    }
    // Check for CakePHP
    elseif (file_exists($path . '/config/bootstrap.php') && file_exists($path . '/src/Controller/AppController.php')) {
        $project['platform'] = 'CakePHP';
        $project['icon'] = 'devicon-plain:cakephp';
        $project['color'] = 'success';
    }
    // Check for Joomla
    elseif (file_exists($path . '/configuration.php') && (file_exists($path . '/includes/defines.php') || file_exists($path . '/libraries/cms/version/version.php'))) {
        $project['platform'] = 'Joomla';
        $project['icon'] = 'devicon-plain:joomla';
        $project['color'] = 'info';
    }
    // Check for static HTML
    elseif (file_exists($path . '/index.html') && !file_exists($path . '/composer.json') && !file_exists($path . '/package.json')) {
        $project['platform'] = 'Static HTML';
        $project['icon'] = 'devicon-plain:html5';
        $project['color'] = 'warning';
        // Try favicon.ico first, then favicon.png, then default
        if (file_exists($path . '/favicon.ico')) $project['favicon'] = $name . '/favicon.ico';
        elseif (file_exists($path . '/favicon.png')) $project['favicon'] = $name . '/favicon.png';
        else $project['favicon'] = 'assets/images/favicon/favicon-32x32.png';
    }
    // Check for Node.js
    elseif (file_exists($path . '/package.json') && !file_exists($path . '/composer.json')) {
        $project['platform'] = 'Node.js';
        $project['icon'] = 'devicon-plain:nodejs';
        $project['color'] = 'success';
    }
    // Default to PHP
    else {
        $project['platform'] = 'PHP';
        $project['icon'] = 'devicon-plain:php';
        $project['color'] = 'primary';
        // Try favicon.ico first, then favicon.png, then default
        if (file_exists($path . '/favicon.ico')) $project['favicon'] = $name . '/favicon.ico';
        elseif (file_exists($path . '/favicon.png')) $project['favicon'] = $name . '/favicon.png';
        else $project['favicon'] = 'assets/images/favicon/favicon-32x32.png';
    }
    
    // Check for Git branch and status (optimized: only branch for now if it's slow)
    if ($project['has_git']) {
        // Try reading branch directly from .git/HEAD to avoid shell overhead
        // If .git is a file (submodule/worktree) or parsing fails, fallback to shell
        $branch = 'unknown';
        $headFile = $path . '/.git/HEAD';
        if (is_file($headFile)) {
            $head = trim((string)@file_get_contents($headFile));
            if (strpos($head, 'ref: refs/heads/') === 0) {
                $branch = substr($head, 16);
            } else {
                $branch = $head; // Detached HEAD, usually a commit hash
            }
        } elseif (is_file($path . '/.git')) {
            // It's a submodule or worktree where .git is a file, fallback to git CLI
            $branch = trim((string)@shell_exec('cd ' . escapeshellarg($path) . ' && git rev-parse --abbrev-ref HEAD 2>&1'));
        }

        $project['git_branch'] = $branch ?: 'unknown';
        
        // Optimization: Use proc_open to run git status in the background if possible, but PHP doesn't easily
        // return async results from a function without a Promise-like wrapper.
        // For project caching, we still need the result.
        // To reduce disk I/O, we can tell git status to only check the working tree and not recurse deep into untracked dirs
        // '--untracked-files=no' makes it significantly faster for large repositories.
        $status = @shell_exec('cd ' . escapeshellarg($path) . ' && git status --porcelain --untracked-files=no 2>&1');
        $project['git_status'] = !empty(trim((string)$status)) ? 'modified' : 'clean';
    }
    
    return $project;
}
}

/**
 * Find a file in a directory recursively
 */
if (!function_exists('findFile')) {
    function findFile($dir, $filenames, $maxDepth = 2, $currentDepth = 0) {
    if ($currentDepth > $maxDepth) {
        return null;
    }

    foreach ($filenames as $filename) {
        $path = $dir . '/' . $filename;
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // Search in subdirectories (limit depth and skip common large folders)
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
    if (!$subdirs) return null;

    foreach ($subdirs as $subdir) {
        $name = basename($subdir);
        if (in_array($name, ['node_modules', 'vendor', '.git', 'bower_components', 'cache', 'storage'])) {
            continue;
        }
        $result = findFile($subdir, $filenames, $maxDepth, $currentDepth + 1);
        if ($result) {
            return $result;
        }
    }
    
    return null;
}
}

/**
 * Get list of ignored projects
 */
if (!function_exists('getIgnoredProjects')) {
    function getIgnoredProjects() {
        $ignoredFile = dirname(__DIR__) . '/data/ignored_projects.json';
        
        if (file_exists($ignoredFile)) {
            $content = @file_get_contents($ignoredFile);
            if ($content) {
                $ignored = json_decode($content, true);
                if (is_array($ignored)) {
                    return $ignored;
                }
            }
        }
        
        return [];
    }
}

/**
 * Ignore a project
 */
if (!function_exists('ignoreProject')) {
    function ignoreProject($projectName) {
        $ignored = getIgnoredProjects();
        
        if (!in_array($projectName, $ignored)) {
            $ignored[] = $projectName;
            
            $ignoredFile = dirname(__DIR__) . '/data/ignored_projects.json';
            @file_put_contents($ignoredFile, json_encode($ignored, JSON_PRETTY_PRINT));
        }
    }
}

/**
 * Unignore a project
 */
if (!function_exists('unignoreProject')) {
    function unignoreProject($projectName) {
        $ignored = getIgnoredProjects();
        $ignored = array_filter($ignored, function($name) use ($projectName) {
            return $name !== $projectName;
        });
        
        $ignoredFile = dirname(__DIR__) . '/data/ignored_projects.json';
        @file_put_contents($ignoredFile, json_encode(array_values($ignored), JSON_PRETTY_PRINT));
    }
}

/**
 * Get services status
 */
if (!function_exists('getServicesStatus')) {
    function getServicesStatus() {
        $services = [
            'Apache' => ['name' => 'Apache', 'display' => 'Apache', 'port' => 80, 'systemd' => ['apache2', 'httpd']],
            'MySQL' => ['name' => 'MySQL', 'display' => 'MySQL', 'port' => 3306, 'systemd' => ['mysql', 'mariadb', 'mysqld']],
            'Redis' => ['name' => 'Redis', 'display' => 'Redis', 'port' => 6379, 'systemd' => ['redis', 'redis-server']],
            'Memcached' => ['name' => 'Memcached', 'display' => 'Memcached', 'port' => 11211, 'systemd' => ['memcached']],
            'PostgreSQL' => ['name' => 'PostgreSQL', 'display' => 'PostgreSQL', 'port' => 5432, 'systemd' => ['postgresql', 'postgresql-16', 'postgresql-15']],
        ];

        $status = [];

        // Linux: use systemctl and ss/netstat
        $netstatOutput = @shell_exec('ss -tlnp 2>/dev/null || netstat -tlnp 2>/dev/null');

        foreach ($services as $key => $service) {
            // Check if port is listening
            $isPortRunning = false;
            if ($netstatOutput) {
                if (preg_match('/:' . preg_quote($service['port'], '/') . '\s/m', $netstatOutput)) {
                    $isPortRunning = true;
                }
            }

            // Check systemd service status
            $systemdRunning = false;
            foreach ($service['systemd'] as $unit) {
                $output = @shell_exec("systemctl is-active {$unit} 2>/dev/null");
                if (trim($output) === 'active') {
                    $systemdRunning = true;
                    break;
                }
            }

            $status[$key] = [
                'name' => $service['display'],
                'running' => $isPortRunning || $systemdRunning,
                'port' => $service['port'],
                'systemd_active' => $systemdRunning,
            ];
        }

        return $status;
    }
}

/**
 * Check if a port is in use
 */
if (!function_exists('isPortInUse')) {
    function isPortInUse($port) {
        $output = @shell_exec("ss -tlnp 2>/dev/null | grep ':$port ' || netstat -tlnp 2>/dev/null | grep ':$port '");
        return !empty(trim((string)$output));
    }
}

/**
 * Get listening ports
 */
if (!function_exists('getListeningPorts')) {
    function getListeningPorts() {
        $output = @shell_exec('ss -tlnp 2>/dev/null || netstat -tlnp 2>/dev/null');
        $ports = [];
        if ($output) {
            // ss format: LISTEN 0 128 0.0.0.0:80 0.0.0.0:* users:(("apache2",pid=1234,fd=4))
            if (preg_match_all('/LISTEN\s+\d+\s+\d+\s+[\d.]+:(\d+)\s+.*?pid=(\d+)/', $output, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $ports[] = ['pid' => $match[2], 'port' => $match[1]];
                }
            }
            // Fallback: netstat format
            elseif (preg_match_all('/LISTEN\s+(\d+)\s+.*?:(\d+)/', $output, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $ports[] = ['pid' => $match[1], 'port' => $match[2]];
                }
            }
        }
        return $ports;
    }
}

/**
 * Start a service
 */
if (!function_exists('startService')) {
    function startService($service) {
        $services = [
            'Apache' => ['apache2', 'httpd'],
            'MySQL' => ['mysql', 'mariadb'],
            'Nginx' => ['nginx'],
        ];

        if (isset($services[$service])) {
            foreach ($services[$service] as $unit) {
                $output = @shell_exec("sudo systemctl start {$unit} 2>&1");
                $check = @shell_exec("systemctl is-active {$unit} 2>/dev/null");
                if (trim($check) === 'active') {
                    return true;
                }
            }
        }

        return false;
    }
}

/**
 * Stop a service
 */
if (!function_exists('stopService')) {
    function stopService($service) {
        $services = [
            'Apache' => ['apache2', 'httpd'],
            'MySQL' => ['mysql', 'mariadb'],
            'Nginx' => ['nginx'],
        ];

        if (isset($services[$service])) {
            foreach ($services[$service] as $unit) {
                $output = @shell_exec("sudo systemctl stop {$unit} 2>&1");
                $check = @shell_exec("systemctl is-active {$unit} 2>/dev/null");
                if (trim($check) !== 'active') {
                    return true;
                }
            }
        }

        return false;
    }
}

/**
 * Restart a service
 */
if (!function_exists('restartService')) {
    function restartService($service) {
        stopService($service);
        sleep(1);
        return startService($service);
    }
}

/**
 * Get log files
 */
if (!function_exists('getLogFiles')) {
    function getLogFiles() {
        $logs = [
            'apache_error' => [
                'name' => 'Apache Error Log',
                'path' => '/var/log/apache2/error.log',
                'type' => 'error',
            ],
            'apache_access' => [
                'name' => 'Apache Access Log',
                'path' => '/var/log/apache2/access.log',
                'type' => 'access',
            ],
            'php_error' => [
                'name' => 'PHP Error Log',
                'path' => '/var/log/php8.3-fpm.log',
                'type' => 'error',
            ],
            'mysql_error' => [
                'name' => 'MySQL Error Log',
                'path' => '/var/log/mysql/error.log',
                'type' => 'error',
            ],
            'mysql_slow' => [
                'name' => 'MySQL Slow Query Log',
                'path' => '/var/log/mysql/slow.log',
                'type' => 'slow',
            ],
            'nucleus' => [
                'name' => 'Nucleus Log',
                'path' => dirname(__DIR__) . '/logs/nucleus.log',
                'type' => 'app',
            ],
        ];

        // Filter out non-existent logs, but always show at least nucleus log
        $existing = array_filter($logs, function($log) {
            return file_exists($log['path']);
        });

        // If no logs found, try alternative paths
        if (empty($existing)) {
            $altPaths = [
                'apache_error' => '/var/log/httpd/error_log',
                'apache_access' => '/var/log/httpd/access_log',
                'php_error' => '/var/log/php-fpm/error.log',
                'mysql_error' => '/var/log/mariadb/mariadb.log',
            ];
            foreach ($altPaths as $key => $path) {
                if (file_exists($path) && isset($logs[$key])) {
                    $logs[$key]['path'] = $path;
                    $existing[$key] = $logs[$key];
                }
            }
        }

        return $existing ?: $logs;
    }
}

/**
 * Read log file
 */
if (!function_exists('readLogFile')) {
    function readLogFile($path, $lines = 100) {
        if (!file_exists($path)) {
            return ['error' => 'Log file not found'];
        }
        
        // ⚡ Bolt: Use native PHP implementation to avoid slow powershell subprocess creation
        if (class_exists('\\Nucleus\\Core\\Services\\Logs')) {
            $result = \Nucleus\Core\Services\Logs::read($path, $lines);
            if ($result) {
                return [
                    'content' => $result['content'],
                    'lines' => $result['displayed_lines']
                ];
            }
        }
        
        // Fallback to reading entire file if class not available
        $output = @file_get_contents($path);
        return [
            'content' => $output,
            'lines' => substr_count($output, "\n") + 1,
        ];
    }
}

/**
 * Clear log file
 */
if (!function_exists('clearLogFile')) {
    function clearLogFile($path) {
        if (file_exists($path)) {
            return @file_put_contents($path, '');
        }
        return false;
    }
}

/**
 * Create a project
 */
if (!function_exists('createProject')) {
    function createProject($name, $type, $options = []) {
        $laragonRoot = getLaragonRoot();
        $wwwDir = $laragonRoot . '/html';
        $wwwPath = $wwwDir . '/' . $name;

        if (is_dir($wwwPath)) {
            return ['success' => false, 'error' => 'Project already exists'];
        }

        $commands = [];

        switch ($type) {
            case 'laravel':
                // Create Laravel project using composer
                $commands[] = 'cd ' . escapeshellarg($wwwDir) . ' && composer create-project laravel/laravel ' . escapeshellarg($name) . ' --prefer-dist';
                break;

            case 'wordpress':
                // Use wp-cli or curl
                $commands[] = 'cd ' . escapeshellarg($wwwDir) . ' && (command -v wp >/dev/null 2>&1 && wp core download --locale=en_US --path=' . escapeshellarg($name) . ' || (curl -sO https://wordpress.org/latest.tar.gz && tar -xzf latest.tar.gz && mv wordpress ' . escapeshellarg($name) . ' && rm -f latest.tar.gz))';
                break;

            case 'nodejs':
                // Create Node.js project
                $commands[] = 'cd ' . escapeshellarg($wwwPath) . ' && npm init -y';
                break;

            case 'static':
                // Create basic static files
                $commands[] = 'mkdir -p ' . escapeshellarg($wwwPath);
                $commands[] = 'echo "<!DOCTYPE html><html><head><title>' . escapeshellarg($name) . '</title></head><body><h1>' . escapeshellarg($name) . '</h1></body></html>" > ' . escapeshellarg($wwwPath . '/index.html');
                break;

            default:
                // Create basic PHP project
                $commands[] = 'mkdir -p ' . escapeshellarg($wwwPath);
                $commands[] = 'echo "<?php phpinfo();" > ' . escapeshellarg($wwwPath . '/index.php');
        }

        $output = [];
        foreach ($commands as $command) {
            exec($command . ' 2>&1', $output);
        }

        return [
            'success' => is_dir($wwwPath),
            'output' => implode("\n", $output),
        ];
    }
}

/**
 * Delete a project
 */
if (!function_exists('deleteProject')) {
    function deleteProject($name) {
        $laragonRoot = getLaragonRoot();
        $wwwDir = $laragonRoot . '/html';
        $projectPath = $wwwDir . '/' . $name;

        if (!is_dir($projectPath)) {
            return ['success' => false, 'error' => 'Project not found'];
        }

        $output = @shell_exec('rm -rf ' . escapeshellarg($projectPath) . ' 2>&1');
        return [
            'success' => !is_dir($projectPath),
            'output' => $output,
        ];
    }
}

/**
 * Get dashboard preferences
 */
/**
 * Get Dashboard preferences (stored in JSON file)
 */
if (!function_exists('getDashboardPreferences')) {
    function getDashboardPreferences() {
        $dataDir = dirname(__DIR__) . '/data';
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0755, true);
        }
        
        $prefsFile = $dataDir . '/preferences.json';
        $defaults = [
            'laragon_root' => null,
            'mysql_host' => null,
            'mysql_user' => null,
            'mysql_password' => null,
            'document_root' => null,
            'domain_suffix' => null,
            'auto_update_check' => true,
            'auto_update_install' => false,
            'last_update_check' => null,
            'debug_banner' => false,
            'time_format' => null,
            'date_format' => null,
        ];
        
        if (file_exists($prefsFile)) {
            $content = @file_get_contents($prefsFile);
            if ($content) {
                $prefs = @json_decode($content, true);
                if (is_array($prefs)) {
                    return array_merge($defaults, $prefs);
                }
            }
        }
        
        return $defaults;
    }
}

/**
 * Save Dashboard preferences
 */
if (!function_exists('saveDashboardPreferences')) {
    function saveDashboardPreferences(array $preferences) {
        $dataDir = dirname(__DIR__) . '/data';
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0755, true);
        }
        
        $prefsFile = $dataDir . '/preferences.json';
        $existing = getDashboardPreferences();
        
        $merged = array_merge($existing, $preferences);
        
        // Remove null and empty string values
        $merged = array_filter($merged, function($value) {
            return $value !== null && $value !== '';
        });
        
        return @file_put_contents($prefsFile, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
    }
}

/**
 * Clear all caches
 */
if (!function_exists('clearAllCaches')) {
    function clearAllCaches() {
        $results = [];

        // Clear dashboard cache
        $cacheDir = dirname(__DIR__) . '/temp/cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            $results['dashboard_cache'] = true;
        }

        // Clear session cache
        $sessionDir = dirname(__DIR__) . '/temp/sessions';
        if (is_dir($sessionDir)) {
            $files = glob($sessionDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            $results['session_cache'] = true;
        }

        return $results;
    }
}

/**
 * Optimize databases
 */
if (!function_exists('optimizeDatabases')) {
    function optimizeDatabases() {
        // This would require MySQL credentials
        // For now, return a placeholder
        return [
            'success' => false,
            'message' => 'Database optimization requires MySQL credentials. Please configure in config.php',
        ];
    }
}

/**
 * Run composer command
 */
if (!function_exists('runComposerCommand')) {
    function runComposerCommand($projectPath, $command = 'install') {
        $fullCommand = 'composer ' . $command;
        $output = @shell_exec('cd ' . escapeshellarg($projectPath) . ' && ' . $fullCommand . ' 2>&1');

        return [
            'success' => strpos($output, 'Generating autoload files') !== false || strpos($output, 'Package operations') !== false,
            'output' => $output,
        ];
    }
}

/**
 * Run npm command
 */
if (!function_exists('runNpmCommand')) {
    function runNpmCommand($projectPath, $command = 'install') {
        $fullCommand = 'npm ' . $command;
        $output = @shell_exec('cd ' . escapeshellarg($projectPath) . ' && ' . $fullCommand . ' 2>&1');

        return [
            'success' => strpos($output, 'added') !== false || strpos($output, 'up to date') !== false,
            'output' => $output,
        ];
    }
}

/**
 * Check Git status
 */
if (!function_exists('checkGitStatus')) {
    function checkGitStatus($projectPath) {
        // 1. Get branch directly from .git/HEAD if possible
        $branch = 'unknown';
        $headFile = $projectPath . '/.git/HEAD';
        if (is_file($headFile)) {
            $head = trim((string)@file_get_contents($headFile));
            if (strpos($head, 'ref: refs/heads/') === 0) {
                $branch = substr($head, 16);
            } else {
                $branch = $head; // Detached HEAD, usually a commit hash
            }
        } elseif (is_file($projectPath . '/.git')) {
            // It's a submodule or worktree where .git is a file
            $branch = trim((string)@shell_exec('cd ' . escapeshellarg($projectPath) . ' && git rev-parse --abbrev-ref HEAD 2>&1'));
        }

        // 2. Get status via shell exec (too complex to parse manually reliably)
        // Optimization: Use --untracked-files=no to speed up disk I/O on large repos
        $status = @shell_exec('cd ' . escapeshellarg($projectPath) . ' && git status --porcelain --untracked-files=no 2>&1');

        // 3. Get remote URL from .git/config
        $remote = null;
        $configFile = $projectPath . '/.git/config';
        if (is_file($configFile)) {
            $config = @file_get_contents($configFile);
            if ($config && preg_match('/\[remote "origin"\][^\[]*url\s*=\s*([^\n]+)/s', $config, $matches)) {
                $remote = trim($matches[1]);
            }
        }

        // Fallback if config regex failed or it's a submodule/worktree where .git is a file
        if (empty($remote) && (is_file($configFile) || is_file($projectPath . '/.git'))) {
            $remote = trim((string)@shell_exec('cd ' . escapeshellarg($projectPath) . ' && git remote get-url origin 2>&1'));
        }
        
        return [
            'branch' => $branch ?: 'unknown',
            'status' => !empty(trim((string)$status)) ? 'modified' : 'clean',
            'remote' => $remote ?: null,
        ];
    }
}

/**
 * Get Nucleus config (replaces legacy Laragon config)
 */
if (!function_exists('getLaragonConfig')) {
    function getLaragonConfig() {
        $configFile = dirname(__DIR__) . '/data/nucleus.json';

        if (!file_exists($configFile)) {
            return [];
        }

        $content = @file_get_contents($configFile);
        $config = @json_decode($content, true);

        return is_array($config) ? $config : [];
    }
}

/**
 * Fix SMTP configuration
 */
if (!function_exists('fixSMTP')) {
    function fixSMTP() {
        $results = [];

        // Check PHP ini for mail configuration
        $phpIni = php_ini_loaded_file();
        if ($phpIni && file_exists($phpIni)) {
            $results['php_ini'] = $phpIni;
            $results['mail_configured'] = ini_get('SMTP') ? true : false;
        }

        return $results;
    }
}

/**
 * Translation helper function
 */
if (!function_exists('t')) {
    function t($key, $fallback = '') {
        // This is a placeholder - actual translation would be loaded from i18n files
        return $fallback ?: $key;
    }
}


/**
 * Get PHP ini path
 */
if (!function_exists('getPHPIniPath')) {
    function getPHPIniPath() {
        $linuxPaths = [
            '/etc/php/8.3/apache2/php.ini',
            '/etc/php/8.3/cli/php.ini',
            '/etc/php/8.2/apache2/php.ini',
            '/etc/php/8.1/apache2/php.ini',
            '/etc/php.ini'
        ];
        foreach ($linuxPaths as $path) {
            if (file_exists($path)) return $path;
        }

        return null;
    }
}

/**
 * Get MySQL ini path
 */
if (!function_exists('getMySQLIniPath')) {
    function getMySQLIniPath() {
        $linuxPaths = [
            '/etc/mysql/mariadb.conf.d/50-server.cnf',
            '/etc/mysql/my.cnf',
            '/etc/mysql/mariadb.cnf',
            '/etc/my.cnf'
        ];
        foreach ($linuxPaths as $path) {
            if (file_exists($path)) return $path;
        }

        return null;
    }
}

// Clear any output that may have been generated
ob_end_clean();


/**
 * Check Docker container status and list info.
 */
if (!function_exists('checkDockerStatus')) {
    function checkDockerStatus() {
        $results = [
            'is_installed' => false,
            'containers' => [],
            'status_message' => 'Docker not available or running.',
            'docker_output' => '',
        ];

        // 1. Check if Docker is installed and reachable
        $checkOutput = @shell_exec('docker info > /dev/null 2>&1');
        if ($checkOutput) {
            $results['is_installed'] = true;
            
            // 2. Get running containers (and optionally, all containers for full visibility)
            // Using 'ps' is standard for active services
            $containerListRaw = @shell_exec('docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}" 2>&1');
            if (!empty(trim($containerListRaw))) {
                // Adding a pseudo-header row for cleaner presentation in JS/HTML, though parsing needs care.
                $results['containers'][] = [
                    'names' => 'Container Name', 
                    'images' => 'Image', 
                    'status' => 'Status'
                ]; 

                $lines = explode("\n", trim($containerListRaw));
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    // Attempt to parse space-separated values: Name | Image | Status
                    $parts = preg_split('/\s+/', trim($line), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($parts) >= 3) {
                        $results['containers'][] = [
                            'names' => $parts[0], 
                            'images' => $parts[1] ?? 'N/A', 
                            'status' => $parts[2] 
                        ];
                    }
                }
            } else {
                $results['containers'][] = ['names' => 'No running containers found.', 'images' => '', 'status' => ''];
            }

            // Capture the full output for debugging/info panel
            $results['docker_output'] = @shell_exec('docker ps -a 2>&1');
        } else {
             $results['status_message'] = 'Docker CLI command failed. Is Docker service running?';
        }

        return $results;
    }
}