<?php
/**
 * AudiencePulse — Live Audience Interaction System
 * Brand: AudiencePulse (نبض الجمهور)
 * Copyright: 2TInteractive (2tinteractive.com)
 * Version: 1.0.0
 */

// Application constants
define('APP_NAME', 'AudiencePulse');
define('APP_NAME_AR', 'نبض الجمهور');
define('APP_VERSION', '1.0.0');
define('APP_COMPANY', '2TInteractive');
define('APP_COMPANY_URL', 'https://2tinteractive.com');
define('APP_ROOT', dirname(__DIR__));

// Database configuration (MySQL root, no password)
define('DB_HOST', 'localhost');
define('DB_NAME', 'audiencepulse');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Cairo');

// Base URL detection
$basePath = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (!empty($scriptName)) {
    $basePath = rtrim(dirname($scriptName), '/');
    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }
}
define('BASE_URL', $basePath);

// Load database
require_once __DIR__ . '/db.php';

// Load i18n
require_once __DIR__ . '/i18n.php';

// Load functions
require_once __DIR__ . '/functions.php';

// Load settings
$settings = getSettings();
