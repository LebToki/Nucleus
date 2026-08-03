<?php

namespace LaragonDashboard\Core;

/**
 * System Class
 * Version: 2.0.0 — ProjectsForge (Linux adaptation)
 * Handles project root detection, environment detection, and versioning
 * Adapted from Laragon Dashboard for ZorinOS / Ubuntu / Mint
 */
class System {

    /**
     * Detect if running on Linux
     */
    public static function isLinux() {
        return PHP_OS_FAMILY !== 'Windows';
    }

    /**
     * Get the project root directory
     * On Windows/Laragon: returns the Laragon root (e.g. C:/laragon)
     * On Linux: returns /var/www (so that getWwwPath() = /var/www/html)
     */
    public static function getLaragonRoot() {
        // Environment variable override (works on both platforms)
        if (getenv('PROJECTS_ROOT')) {
            return rtrim(str_replace('\\', '/', getenv('PROJECTS_ROOT')), '/');
        }
        if (getenv('LARAGON_ROOT')) {
            return rtrim(str_replace('\\', '/', getenv('LARAGON_ROOT')), '/');
        }

        // Linux detection
        if (self::isLinux()) {
            // On Linux with Apache, DocumentRoot is typically /var/www/html
            // We return /var/www so that getWwwPath() = /var/www/html
            if (!empty($_SERVER['DOCUMENT_ROOT'])) {
                $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
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

        // Windows / Laragon detection
        $possiblePaths = ['C:/laragon', 'D:/laragon', 'E:/laragon'];

        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
            if (stripos($docRoot, '/laragon/www') !== false) {
                $parts = explode('/www', $docRoot);
                return $parts[0];
            }
            if (stripos($docRoot, 'laragon') !== false) {
                $parts = explode('laragon', $docRoot);
                return rtrim($parts[0], '/') . '/laragon';
            }
        }

        foreach ($possiblePaths as $path) {
            if (is_dir($path)) {
                return $path;
            }
        }

        return 'C:/laragon';
    }

    /**
     * Get the www directory where projects live
     */
    public static function getWwwPath() {
        $root = self::getLaragonRoot();
        if (self::isLinux()) {
            // On Linux, www is /var/www/html (Apache DocumentRoot)
            // But if getLaragonRoot() already returned the DocumentRoot itself,
            // just return that
            if ($root === ($_SERVER['DOCUMENT_ROOT'] ?? '')) {
                return $root;
            }
            return $root . '/html';
        }
        return $root . '/www';
    }

    /**
     * Get sendmail/log directory (platform-aware)
     */
    public static function getSendmailDir() {
        if (self::isLinux()) {
            $sendmailPath = '/var/log/projectsforge/mail/';
            if (!is_dir($sendmailPath)) {
                @mkdir($sendmailPath, 0755, true);
            }
            return $sendmailPath;
        }

        $laragonRoot = self::getLaragonRoot();
        $sendmailPath = $laragonRoot . '/bin/sendmail/output/';

        if (!is_dir($sendmailPath)) {
            @mkdir($sendmailPath, 0755, true);
        }

        return $sendmailPath;
    }

    /**
     * Get application version
     */
    public static function getAppVersion() {
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
