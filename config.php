<?php
/**
 * Nucleus Configuration
 * Version: 2.0.0 — The Missing Dashboard for Linux Developers
 * Author: Tarek Tarabichi
 * Company: 2TInteractive (2tinteractive.com)
 * Platform: ZorinOS / Ubuntu / Mint (Linux Apache + systemd)
 * Born from: https://github.com/LebToki/Laragon-Dashboard (v4.0.5 for Windows)
 */

// Application Information (only define if not already defined)
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Nucleus');
}
if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.1');
}
if (!defined('APP_AUTHOR')) {
    define('APP_AUTHOR', 'Tarek Tarabichi');
}
if (!defined('APP_COMPANY')) {
    define('APP_COMPANY', '2TInteractive');
}
if (!defined('APP_COMPANY_URL')) {
    define('APP_COMPANY_URL', 'https://2tinteractive.com');
}
if (!defined('APP_GITHUB')) {
    define('APP_GITHUB', 'https://github.com/LebToki/Nucleus');
}
if (!defined('APP_START_YEAR')) {
    define('APP_START_YEAR', '2024');
}

// Application Settings (only define if not already defined)
if (!defined('APP_DEBUG')) {
    // Default to false for production
    define('APP_DEBUG', false);
}
if (!defined('APP_ENV')) {
    define('APP_ENV', 'production'); // development, staging, production
}

// Enable error reporting if debug mode is on
if (APP_DEBUG) {
    error_reporting(E_ALL);
    @ini_set('display_errors', 1);
    @ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    @ini_set('display_errors', 0);
    @ini_set('display_startup_errors', 0);
}

// Security settings
if (!defined('SECURITY_HEADERS_ENABLED')) {
    define('SECURITY_HEADERS_ENABLED', true);
}

// Rate limiting settings
if (!defined('RATE_LIMIT_REQUESTS_PER_MINUTE')) {
    define('RATE_LIMIT_REQUESTS_PER_MINUTE', 60);
}

// Session settings
if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', 3600); // 1 hour
}

// Authentication settings
// Auth model: HTTPS enforces password; HTTP localhost auto-authenticates (local dev stack).
// Set AUTH_SHARED_WORKSPACE=true to force password auth even on HTTP (multi-user environments).
if (!defined('AUTH_ENABLED')) {
    define('AUTH_ENABLED', true);
}
if (!defined('AUTH_SHARED_WORKSPACE')) {
    define('AUTH_SHARED_WORKSPACE', false);
}
if (!defined('ADMIN_PASSWORD')) {
    // Priority: env var → auto-generated file → random fallback
    $envPassword = getenv('NUCLEUS_PASSWORD') ?: getenv('LARAGON_DASHBOARD_PASSWORD');
    if ($envPassword) {
        define('ADMIN_PASSWORD', $envPassword);
    } else {
        $pwFile = (defined('APP_ROOT') ? APP_ROOT : __DIR__) . '/data/admin_password.txt';
        if (file_exists($pwFile)) {
            define('ADMIN_PASSWORD', trim(file_get_contents($pwFile)));
        } else {
            $generated = bin2hex(random_bytes(16));
            $dataDir = (defined('APP_ROOT') ? APP_ROOT : __DIR__) . '/data';
            if (!is_dir($dataDir)) { @mkdir($dataDir, 0755, true); }
            @file_put_contents($pwFile, $generated);
            define('ADMIN_PASSWORD', $generated);
        }
    }
}

// Fix session path — use system temp directory
$sessionPath = sys_get_temp_dir();
if (is_dir($sessionPath) && is_writable($sessionPath)) {
    @session_save_path($sessionPath);
}

// Suppress errors for API endpoints (they handle their own error reporting)
if (basename($_SERVER['PHP_SELF'] ?? '') === 'files.php' || 
    basename($_SERVER['PHP_SELF'] ?? '') === 'logs.php' ||
    basename($_SERVER['PHP_SELF'] ?? '') === 'vitals.php' ||
    basename($_SERVER['PHP_SELF'] ?? '') === 'services.php' ||
    basename($_SERVER['PHP_SELF'] ?? '') === 'preferences.php' ||
    basename($_SERVER['PHP_SELF'] ?? '') === 'mailpit.php') {
    @ini_set('display_errors', 0);
    @error_reporting(0);
}

// Path Definitions (only define if not already defined)
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__FILE__));
}
if (!defined('TEMPLATE_ROOT')) {
    define('TEMPLATE_ROOT', APP_ROOT . '/template');
}
if (!defined('ASSETS_ROOT')) {
    define('ASSETS_ROOT', APP_ROOT . '/assets');
}
if (!defined('PARTIALS_ROOT')) {
    define('PARTIALS_ROOT', APP_ROOT . '/partials');
}
if (!defined('INCLUDES_ROOT')) {
    define('INCLUDES_ROOT', APP_ROOT . '/includes');
}
if (!defined('LOGS_ROOT')) {
    define('LOGS_ROOT', APP_ROOT . '/logs');
}
if (!defined('CACHE_ROOT')) {
    define('CACHE_ROOT', APP_ROOT . '/cache');
}

// URL Path Definitions (relative to web root)
if (!defined('BASE_URL')) {
    // Determine base URL - this must work correctly for routing scenarios
    $basePath = '';
    
    // Method 1: Use SCRIPT_NAME (most reliable - always reflects the actual script being executed)
    // When accessing via index.php?page=projects, SCRIPT_NAME is /Laragon-Dashboard/index.php
    // When accessing pages/projects.php directly, SCRIPT_NAME is /Laragon-Dashboard/pages/projects.php
    // When accessing via custom domain (laragon-dashboard.local), SCRIPT_NAME is /index.php
    // When using PHP built-in server with -t ., SCRIPT_NAME is /index.php and DOCUMENT_ROOT is the dashboard dir
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? __FILE__;
    
    // Check if we're using PHP built-in server with dashboard as document root
    // In this case, DOCUMENT_ROOT will be the dashboard directory itself
    $appRootNormalized = rtrim(APP_ROOT, '/');
    $docRootNormalized = rtrim($docRoot, '/');
    
    if ($docRootNormalized === $appRootNormalized) {
        // PHP built-in server with -t . (dashboard is document root)
        // BASE_URL should be empty since we're at root
        $basePath = '';
    } else if (!empty($scriptName)) {
        $basePath = dirname($scriptName);
        // Normalize: dirname('/Laragon-Dashboard/index.php') = '/Laragon-Dashboard'
        // dirname('/index.php') = '/' or '.'
        // For custom domains, if script is in subdirectory, preserve it
        if ($basePath === '.' || $basePath === '/') {
            $basePath = '';
        }
    }

    // Method 2: Fallback - use REQUEST_URI to detect subdirectory
    if (empty($basePath) || $basePath === '') {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (!empty($requestUri)) {
            // Remove query string
            $requestPath = parse_url($requestUri, PHP_URL_PATH);
            // Get directory part
            $basePath = dirname($requestPath);
            if ($basePath === '.' || $basePath === '/') {
                $basePath = '';
            }
        }
    }
    
    // Method 3: Detect from document root if dashboard is in a subdirectory
    if (empty($basePath) || $basePath === '') {
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? __FILE__;

        // If script is in a subdirectory of document root, calculate the path
        if (!empty($docRoot) && !empty($scriptFile)) {
            $docRoot = rtrim($docRoot, '/');
            $scriptFile = str_replace('\\', '/', $scriptFile);

            // Check if script is inside document root
            if (strpos($scriptFile, $docRoot) === 0) {
                $relativePath = substr($scriptFile, strlen($docRoot));
                $relativeDir = dirname($relativePath);

                if ($relativeDir !== '.' && $relativeDir !== '/') {
                    $basePath = $relativeDir;
                }
            }
        }
    }
    
    // Final normalization - ensure proper format
    if ($basePath === '/' || $basePath === '.' || $basePath === '') {
        $basePath = '';
    } else {
        // Remove trailing slash and ensure it starts with /
        $basePath = rtrim($basePath, '/');
        if (substr($basePath, 0, 1) !== '/') {
            $basePath = '/' . $basePath;
        }
    }
    
    define('BASE_URL', $basePath);
}
if (!defined('ASSETS_URL')) {
    // Always use absolute path from web root for assets
    // This ensures CSS/JS files load correctly regardless of routing or direct access

    // Check if we're using PHP built-in server with dashboard as document root
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $appRootNormalized = rtrim(APP_ROOT, '/');
    $docRootNormalized = rtrim($docRoot, '/');

    if ($docRootNormalized === $appRootNormalized) {
        // PHP built-in server or Laragon auto-vhost: dashboard IS the document root, so assets are at /assets
        $assetsPath = '/assets';
    } else {
        // Check if assets directory exists at document root level
        // This handles cases where Laragon auto-vhost points directly to Laragon-Dashboard
        $assetsAtDocRoot = $docRootNormalized . '/assets';
        if (is_dir($assetsAtDocRoot)) {
            // Assets are directly under document root (auto-vhost scenario)
            $assetsPath = '/assets';
        } else if (BASE_URL === '') {
            // Fallback: dashboard is in subdirectory but BASE_URL is empty
            $assetsPath = '/assets';
        } else {
            // Normal Apache: dashboard is in subdirectory, use BASE_URL
            $assetsPath = BASE_URL . '/assets';
        }
    }

    // Ensure it starts with / (absolute path)
    if (substr($assetsPath, 0, 1) !== '/') {
        $assetsPath = '/' . $assetsPath;
    }
    define('ASSETS_URL', $assetsPath);
}
if (!defined('TEMPLATE_URL')) {
    define('TEMPLATE_URL', BASE_URL . '/template');
}

/**
 * Get Dashboard preferences (stored in JSON file)
 * Allows overriding Laragon detection
 */
if (!function_exists('getDashboardPreferences')) {
    function getDashboardPreferences() {
        $dataDir = APP_ROOT . '/data';
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0755, true);
        }
        
        $prefsFile = $dataDir . '/preferences.json';
        $defaults = [
            'laragon_root' => null, // null means auto-detect
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
        $dataDir = APP_ROOT . '/data';
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0755, true);
        }
        
        $prefsFile = $dataDir . '/preferences.json';
        $existing = getDashboardPreferences();
        
        // Normalize paths before saving
        if (isset($preferences['laragon_root'])) {
            $preferences['laragon_root'] = rtrim(str_replace('\\', '/', $preferences['laragon_root']), '/');
        }
        if (isset($preferences['document_root'])) {
            $preferences['document_root'] = rtrim(str_replace('\\', '/', $preferences['document_root']), '/');
        }
        
        $merged = array_merge($existing, $preferences);
        
        // Remove null and empty string values to allow auto-detection
        $merged = array_filter($merged, function($value) {
            return $value !== null && $value !== '';
        });
        
        return @file_put_contents($prefsFile, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
    }
}

/**
 * Get the project root directory
 * Linux-only: checks env vars, Apache DocumentRoot, then fallback to /var/www
 */
function getLaragonRoot() {
    static $laragonRoot = null;
    if ($laragonRoot !== null) {
        return $laragonRoot;
    }

    // Check if constant already defined
    if (defined('LARAGON_ROOT')) {
        return $laragonRoot = LARAGON_ROOT;
    }

    // Delegate to the Linux-aware System class if available
    if (class_exists('\Nucleus\Core\System')) {
        return $laragonRoot = \Nucleus\Core\System::getLaragonRoot();
    }

    // 1. Check Dashboard Preferences override
    $prefs = getDashboardPreferences();
    if (!empty($prefs['laragon_root'])) {
        $prefPath = rtrim($prefs['laragon_root'], '/');
        if (is_dir($prefPath)) {
            return $laragonRoot = $prefPath;
        }
    }

    // 2. Check environment variables
    $envPath = getenv('PROJECTS_ROOT') ?: getenv('LARAGON_ROOT');
    if (!empty($envPath) && is_dir($envPath)) {
        return $laragonRoot = rtrim($envPath, '/');
    }

    // 3. Try Apache DocumentRoot detection
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if (!empty($docRoot) && is_dir($docRoot)) {
        return $laragonRoot = rtrim($docRoot, '/');
    }

    // 4. Fallback
    return $laragonRoot = '/var/www';
}

/**
 * Get Laragon configuration
 * On Linux (Nucleus), laragon.ini does not exist — returns empty array.
 * MySQL settings fall through to defaults in the MySQL config section.
 */
function getLaragonConfig() {
    return [];
}

/**
 * Get Laragon general preferences
 * Linux-only: no laragon.ini, uses Dashboard preferences and Linux defaults
 */
function getLaragonPreferences() {
    $laragonRoot = getLaragonRoot();
    $dashboardPrefs = getDashboardPreferences();

    $defaults = [
        'StartAllAutomatically' => false,
        'DocumentRoot' => $laragonRoot . '/html',
        'DataDirectory' => $laragonRoot . '/data',
        'HostnameFormat' => '{name}.local',
        'AutoBackup' => false,
        'BackupInterval' => 8,
        'AutoUpdate' => false,
        'AutoCreateVirtualHosts' => true,
    ];

    // Apply Dashboard Preferences overrides (highest priority)
    if (!empty($dashboardPrefs['document_root'])) {
        $defaults['DocumentRoot'] = $dashboardPrefs['document_root'];
    }
    if (!empty($dashboardPrefs['domain_suffix'])) {
        $suffix = ltrim($dashboardPrefs['domain_suffix'], '.');
        $defaults['HostnameFormat'] = '{name}.' . $suffix;
    }

    return $defaults;
}

/**
 * Auto-detect domain suffix
 * Linux-only: returns '.local' as default, with Dashboard preference override
 */
function getLaragonDomainSuffix() {
    $prefs = getDashboardPreferences();
    if (!empty($prefs['domain_suffix'])) {
        $suffix = ltrim($prefs['domain_suffix'], '.');
        return '.' . $suffix;
    }

    return '.local';
}

/**
 * Auto-detect sendmail output directory (Linux-only)
 */
function getLaragonSendmailDir() {
    if (class_exists('\Nucleus\Core\System')) {
        return \Nucleus\Core\System::getSendmailDir();
    }
    return '/var/log/nucleus/mail/';
}

/**
 * Get application version from Git or VERSION file
 */
function getAppVersion() {
    static $appVersion = null;
    if ($appVersion !== null) {
        return $appVersion;
    }

    $versionFile = __DIR__ . '/VERSION';
    
    // Check for VERSION file first
    if (file_exists($versionFile)) {
        $version = trim(@file_get_contents($versionFile));
        if (!empty($version)) {
            return $appVersion = $version;
        }
    }
    
    // Try to get version from Git
    $gitDir = __DIR__ . '/.git';
    if (is_dir($gitDir)) {
        // Try git describe
        $command = 'cd ' . escapeshellarg(__DIR__) . ' && git describe --tags --always 2>/dev/null';
        $version = @shell_exec($command);
        if ($version) {
            $version = trim($version);
            // Remove 'v' prefix if present
            $version = preg_replace('/^v/', '', $version);
            return $appVersion = $version;
        }
        
        // Fallback to short commit hash
        $command = 'cd ' . escapeshellarg(__DIR__) . ' && git rev-parse --short HEAD 2>/dev/null';
        $hash = @shell_exec($command);
        if ($hash) {
            return $appVersion = 'dev-' . trim($hash);
        }
    }
    
    // Default fallback
    return $appVersion = defined('APP_VERSION') ? APP_VERSION : '4.0.0';
}

// Get Laragon root path (only define if not already defined)
if (!defined('LARAGON_ROOT')) {
    $LARAGON_ROOT = getLaragonRoot();
    define('LARAGON_ROOT', $LARAGON_ROOT);
}

// Auto-detect configuration values (only define if not already defined)
if (!defined('SENDMAIL_OUTPUT_DIR')) {
    define('SENDMAIL_OUTPUT_DIR', getenv('SENDMAIL_OUTPUT_DIR') ?: getLaragonSendmailDir());
}
if (!defined('DOMAIN_SUFFIX')) {
    define('DOMAIN_SUFFIX', getenv('DOMAIN_SUFFIX') ?: getLaragonDomainSuffix());
}

// Force HTTPS for project URLs
// Set to true if all your projects should use HTTPS instead of HTTP
if (!defined('FORCE_HTTPS')) {
    define('FORCE_HTTPS', false);
}
if (!defined('APP_VERSION_DETECTED')) {
    define('APP_VERSION_DETECTED', getenv('APP_VERSION') ?: getAppVersion());
}

// URL to access PhpMyAdmin (only define if not already defined)
if (!defined('PHPMYADMIN_URL')) {
    define('PHPMYADMIN_URL', getenv('PHPMYADMIN_URL') ?: 'http://localhost/phpmyadmin');
}

// MySQL connection settings - Priority: Dashboard Preferences > Environment Variable > Laragon Config > Defaults
if (!defined('MYSQL_HOST')) {
    $prefs = getDashboardPreferences();
    $mysqlHost = $prefs['mysql_host'] ?? getenv('MYSQL_HOST');
    if (empty($mysqlHost)) {
        // Try to get from Laragon config
        $laragonConfig = getLaragonConfig();
        $mysqlHost = $laragonConfig['MySQLHost'] ?? 'localhost';
    }
    define('MYSQL_HOST', $mysqlHost ?: 'localhost');
}
if (!defined('MYSQL_USER')) {
    $prefs = getDashboardPreferences();
    $mysqlUser = $prefs['mysql_user'] ?? getenv('MYSQL_USER');
    if (empty($mysqlUser)) {
        // Try to get from Laragon config
        $laragonConfig = getLaragonConfig();
        $mysqlUser = $laragonConfig['MySQLUser'] ?? 'root';
    }
    define('MYSQL_USER', $mysqlUser ?: 'root');
}
if (!defined('MYSQL_PASSWORD')) {
    $prefs = getDashboardPreferences();
    $mysqlPassword = $prefs['mysql_password'] ?? getenv('MYSQL_PASSWORD');
    if ($mysqlPassword === null || $mysqlPassword === '') {
        // Try to get from Laragon config
        $laragonConfig = getLaragonConfig();
        $mysqlPassword = $laragonConfig['MySQLRootPassword'] ?? '';
    }
    define('MYSQL_PASSWORD', $mysqlPassword ?: '');
}

// Security settings (only define if not already defined)
if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', SESSION_LIFETIME); // Use the defined session lifetime
}
if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);
}

// Additional security headers
if (defined('SECURITY_HEADERS_ENABLED') && SECURITY_HEADERS_ENABLED) {
    // Clickjacking protection — SAMEORIGIN to match .htaccess and allow same-site embedding
    header('X-Frame-Options: SAMEORIGIN');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // XSS protection (legacy, CSP is the modern replacement)
    header('X-XSS-Protection: 1; mode=block');

    // HSTS — only on HTTPS connections
    if (\Nucleus\Core\Security::isSecureConnection()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Prevent caching of authenticated pages
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    // Content Security Policy
    // Note: unsafe-inline/unsafe-eval required for inline scripts in PHP templates.
    // Future refactor should move all inline JS to external files to tighten this.
    if (headers_sent() === false) {
        @header_remove('Content-Security-Policy');
    }
    header("Content-Security-Policy: default-src 'self' blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https://code.iconify.design; script-src-elem 'self' 'unsafe-inline' blob: https://code.iconify.design; worker-src 'self' blob:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https://*; font-src 'self' https://fonts.gstatic.com; connect-src 'self' https://api.iconify.design https://api.unisvg.com https://api.simplesvg.com; frame-src 'self'; object-src 'none'");

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions policy (replaces deprecated Feature-Policy)
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
}