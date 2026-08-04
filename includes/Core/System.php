<?php

namespace Nucleus\Core;

/**
 * System Class
 * Version: 2.0.0 — Nucleus (Linux-only)
 * Handles project root detection, environment detection, and versioning
 * Adapted for ZorinOS / Ubuntu / Mint
 */
class System {

    /**
     * Get the project root directory
     * Returns /var/www so that getWwwPath() = /var/www/html
     */
    public static function getLaragonRoot(): string {
        // Environment variable override
        if (getenv('PROJECTS_ROOT')) {
            return rtrim(getenv('PROJECTS_ROOT'), '/');
        }
        if (getenv('LARAGON_ROOT')) {
            return rtrim(getenv('LARAGON_ROOT'), '/');
        }

        // On Linux with Apache, DocumentRoot is typically /var/www/html
        // We return /var/www so that getWwwPath() = /var/www/html
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            if (!empty($docRoot)) {
                // If DocumentRoot is /var/www/html, return /var/www
                if (substr($docRoot, -5) === '/html') {
                    return substr($docRoot, 0, -5);
                }
                // If DocumentRoot ends with /www, return parent
                if (substr($docRoot, -4) === '/www') {
                    return substr($docRoot, 0, -4);
                }
                // Fallback: use DocumentRoot itself as the www root
                return $docRoot;
            }
        }

        // Standard Linux Apache path
        return '/var/www';
    }

    /**
     * Get the www directory where projects live
     */
    public static function getWwwPath(): string {
        $root = self::getLaragonRoot();
        // On Linux, www is /var/www/html (Apache DocumentRoot)
        // But if getLaragonRoot() already returned the DocumentRoot itself,
        // just return that
        if ($root === ($_SERVER['DOCUMENT_ROOT'] ?? '')) {
            return $root;
        }
        return $root . '/html';
    }

    /**
     * Get sendmail/log directory
     */
    public static function getSendmailDir(): string {
        $sendmailPath = '/var/log/projectsforge/mail/';
        if (!is_dir($sendmailPath)) {
            @mkdir($sendmailPath, 0755, true);
        }
        return $sendmailPath;
    }

    /**
     * Get application version
     */
    public static function getAppVersion(): string {
        $versionFile = dirname(__DIR__, 2) . '/VERSION';
        if (file_exists($versionFile)) {
            $version = trim(file_get_contents($versionFile));
            if (!empty($version)) {
                return $version;
            }
        }

        $gitDir = dirname(__DIR__, 2) . '/.git';
        if (is_dir($gitDir)) {
            return 'dev-git';
        }

        return defined('APP_VERSION') ? APP_VERSION : '2.0.0';
    }

}
