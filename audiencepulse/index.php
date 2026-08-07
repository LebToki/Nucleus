<?php
/**
 * AudiencePulse Demo Application Entry Point
 * 
 * This file serves as the public router for the demo site, handling all incoming requests
 * and routing them to the correct controller/view based on WOWDASH standards.
 */

// Autoload composer dependencies (Assumes vendor directory exists)
require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\DashboardController;
use App\Controllers\ModerationController;
use App\Controllers\ContentController;

// Simple Router simulation for demo purposes
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($uri, '/'));
$controllerName = ucfirst($parts[0] ?? 'index') . 'Controller';
$action = $parts[1] ?? 'dashboard';

// Map routes to controllers (Simplified for demo)
if ($controllerName === 'DashboardController' && $action === 'dashboard') {
    require_once __DIR__ . '/Controllers/DashboardController.php';
    (new DashboardController())->dashboardAction();
} elseif ($controllerName === 'ModerationController' && $action === 'review') {
    require_once __DIR__ . '/Controllers/ModerationController.php';
    (new ModerationController())->reviewAction();
} elseif ($controllerName === 'ContentController' && $action === 'manage') {
    require_once __DIR__ . '/Controllers/ContentController.php';
    (new ContentController())->manageAction();
} else {
    // Fallback or 404 handling
    echo "<h1>Error 404</h1><p>Page not found for path: $uri</p>";
}