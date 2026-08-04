<?php

namespace Nucleus\Core;

/**
 * Databases Class
 * Version: 1.0.0
 * Handles MySQL database management
 */
class Databases {
    private static $connection = null;

    /**
     * Get MySQL connection
     */
    private static function getConnection(): ?\mysqli {
        if (self::$connection === null) {
            $host = defined('MYSQL_HOST') ? MYSQL_HOST : '127.0.0.1';
            $user = defined('MYSQL_USER') ? MYSQL_USER : 'root';
            $pass = defined('MYSQL_PASSWORD') ? MYSQL_PASSWORD : ''; 
            
            // Try to connect
            try {
                self::$connection = new \mysqli($host, $user, $pass);
                if (self::$connection->connect_error) {
                    throw new \Exception("Connection failed: " . self::$connection->connect_error);
                }
            } catch (\Exception $e) {
                if (class_exists('\\Nucleus\\Core\\Logger')) {
                    \Nucleus\Core\Logger::error("MySQL Connection Error: " . $e->getMessage());
                }
                return null;
            }
        }
        return self::$connection;
    }

    /**
     * List all databases
     */
    public static function list(): array {
        $db = self::getConnection();
        if (!$db) return [];

        // ⚡ Bolt: Fixed N+1 query problem by getting DBs and their sizes in a single query
        $query = "
            SELECT
                s.schema_name AS 'name',
                COALESCE(SUM(t.data_length + t.index_length) / 1024 / 1024, 0) AS 'size'
            FROM information_schema.schemata s
            LEFT JOIN information_schema.tables t ON s.schema_name = t.table_schema
            WHERE s.schema_name NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
            GROUP BY s.schema_name
        ";

        $result = $db->query($query);
        $databases = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $databases[] = [
                    'name' => $row['name'],
                    'size' => round(floatval($row['size']), 2)
                ];
            }
        }
        return $databases;
    }

    /**
     * Create a database
     */
    public static function create(string $name): bool {
        $db = self::getConnection();
        if (!$db) return false;

        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        if (empty($name)) return false;

        return $db->query("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Drop a database
     */
    public static function drop(string $name): bool {
        $db = self::getConnection();
        if (!$db) return false;

        // Security: Validate database name to prevent syntax-breaking or unexpected behavior
        if (empty($name) || !preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            return false;
        }

        // Security: Prevent deletion of system databases
        $systemDatabases = ['information_schema', 'mysql', 'performance_schema', 'sys', 'phpmyadmin'];
        if (in_array(strtolower($name), $systemDatabases)) {
            if (class_exists('\\Nucleus\\Core\\Logger')) {
                \Nucleus\Core\Logger::error("Security Warning: Attempted to drop system database '$name'");
            }
            return false;
        }

        $name = $db->real_escape_string($name);
        return $db->query("DROP DATABASE `$name` ");
    }

    /**
     * Backup a database
     */
    public static function backup(string $name): array|false {
        $db = self::getConnection();
        if (!$db) return false;

        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        if (empty($name)) return false;

        // Ensure backup directory exists
        $backupDir = dirname(dirname(__DIR__)) . '/backups';
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }

        $filename = $name . '_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // Find mysqldump — check common paths, fall back to PATH
        $dumpPath = 'mysqldump'; // Default: assume it's in PATH

        $linuxPaths = ['/usr/bin/mysqldump', '/usr/local/bin/mysqldump', '/usr/bin/mariadb-dump'];
        foreach ($linuxPaths as $path) {
            if (is_executable($path)) {
                $dumpPath = $path;
                break;
            }
        }

        // Build command with configured credentials
        $host = defined('MYSQL_HOST') ? MYSQL_HOST : 'localhost';
        $user = defined('MYSQL_USER') ? MYSQL_USER : 'root';
        $pass = defined('MYSQL_PASSWORD') ? MYSQL_PASSWORD : '';

        $command = escapeshellarg($dumpPath)
            . ' --host=' . escapeshellarg($host)
            . ' --user=' . escapeshellarg($user);

        if (!empty($pass)) {
            $command .= ' --password=' . escapeshellarg($pass);
        }

        $command .= ' --result-file=' . escapeshellarg($filepath)
            . ' ' . escapeshellarg($name)
            . ' 2>&1';

        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'size' => round(filesize($filepath) / 1024, 2) . ' KB'
            ];
        }

        return false;
    }
}
