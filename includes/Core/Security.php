<?php

namespace Nucleus\Core;

/**
 * Security Class
 * Version: 1.0.0
 * Handles Authentication and CSRF protection
 */
class Security {
    
    /**
     * Generate a CSRF token and store it in the session
     */
    public static function generateCSRFToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Get the current CSRF token
     */
    public static function getCSRFToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        return $_SESSION['csrf_token'] ?? self::generateCSRFToken();
    }

    /**
     * Verify a CSRF token
     */
    public static function verifyCSRFToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if the current request is over HTTPS/SSL
     */
    public static function isSecureConnection(): bool {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        return false;
    }

    /**
     * Check if the request originates from localhost
     */
    public static function isLocalhost(): bool {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        return $remoteAddr === '127.0.0.1'
            || $remoteAddr === '::1'
            || strpos($httpHost, 'localhost') !== false;
    }

    /**
     * Check if the user is authenticated
     *
     * Auth model:
     * - AUTH_ENABLED=false → no auth required
     * - HTTPS → always enforce password (no localhost bypass)
     * - HTTP localhost → auto-authenticate (local dev stack convenience)
     * - HTTP non-localhost → enforce password
     * - AUTH_SHARED_WORKSPACE=true → always enforce password regardless
     */
    public static function isAuthenticated(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (defined('AUTH_ENABLED') && !AUTH_ENABLED) {
            return true;
        }

        // Shared workspace: always require password
        if (defined('AUTH_SHARED_WORKSPACE') && AUTH_SHARED_WORKSPACE) {
            return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
        }

        // HTTPS: always require password (no auto-auth)
        if (self::isSecureConnection()) {
            return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
        }

        // HTTP localhost: auto-authenticate for local dev convenience
        if (self::isLocalhost()) {
            if (empty($_SESSION['authenticated'])) {
                $_SESSION['authenticated'] = true;
                $_SESSION['auth_source'] = 'local_auto';
            }
            return true;
        }

        // HTTP non-localhost: require password
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    /**
     * Enforce authentication
     */
    public static function checkAuth(): void {
        if (!self::isAuthenticated()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Authentication required']);
                exit;
            } else {
                header('Location: login.php');
                exit;
            }
        }
    }
}
