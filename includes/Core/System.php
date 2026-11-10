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
    public static function getNucleusRoot(): string {
        // Environment variable override
        if (getenv('PROJECTS_ROOT')) {
            return rtrim(getenv('PROJECTS_ROOT'), '/');
        }
        if (getenv('NUCLEUS_ROOT')) {
            return rtrim(getenv('NUCLEUS_ROOT'), '/');
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
        // An explicit document_root preference wins: when the dashboard is
        // served from its own vhost, DOCUMENT_ROOT is the dashboard itself,
        // not the directory that holds the projects.
        $preferred = self::preferredWwwPath();
        if ($preferred !== null) {
            return $preferred;
        }

        $root = self::getNucleusRoot();
        // On Linux, www is /var/www/html (Apache DocumentRoot)
        // But if getNucleusRoot() already returned the DocumentRoot itself,
        // just return that
        if ($root === ($_SERVER['DOCUMENT_ROOT'] ?? '')) {
            return $root;
        }
        return $root . '/html';
    }

    /**
     * Read document_root from data/preferences.json when it is set and valid.
     */
    private static function preferredWwwPath(): ?string {
        $file = dirname(__DIR__, 2) . '/data/preferences.json';
        if (!is_file($file)) {
            return null;
        }
        $prefs = json_decode((string) @file_get_contents($file), true);
        if (!is_array($prefs) || empty($prefs['document_root'])) {
            return null;
        }
        $path = rtrim((string) $prefs['document_root'], '/');
        return is_dir($path) ? $path : null;
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
