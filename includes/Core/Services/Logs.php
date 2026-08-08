<?php

namespace Nucleus\Core\Services;

/**
 * Logs Class
 * Version: 4.0.3
 * Handles log file discovery and reading
 */
class Logs {
    
    /**
     * Scan for available log files
     */
    public static function scan(): array {
        $nucleusRoot = \Nucleus\Core\System::getNucleusRoot();
        $logFiles = [];
        
        // Apache (Linux + Legacy paths)
        $apacheErrorCandidates = [
            '/var/log/apache2/error.log',
            '/var/log/httpd/error_log',
            $nucleusRoot . '/logs/apache_error.log'
        ];
        
        foreach ($apacheErrorCandidates as $pattern) {
            $matched = strpos($pattern, '*') !== false ? glob($pattern) : (file_exists($pattern) ? [$pattern] : []);
            if (!empty($matched) && is_readable($matched[0])) {
                $logFiles['apache_error'] = [
                    'name' => 'Apache Error Log',
                    'path' => $matched[0],
                    'icon' => 'solar:server-bold',
                    'color' => 'danger'
                ];
                break;
            }
        }

        $apacheAccessCandidates = [
            '/var/log/apache2/access.log',
            '/var/log/httpd/access_log',
        ];
        foreach ($apacheAccessCandidates as $pattern) {
            $matched = strpos($pattern, '*') !== false ? glob($pattern) : (file_exists($pattern) ? [$pattern] : []);
            if (!empty($matched) && is_readable($matched[0])) {
                $logFiles['apache_access'] = [
                    'name' => 'Apache Access Log',
                    'path' => $matched[0],
                    'icon' => 'solar:server-path-bold',
                    'color' => 'primary'
                ];
                break;
            }
        }
        
        // Nginx (Linux + Legacy paths)
        $nginxErrorCandidates = [
            '/var/log/nginx/error.log',
        ];
        foreach ($nginxErrorCandidates as $pattern) {
            if (file_exists($pattern) && is_readable($pattern)) {
                $logFiles['nginx_error'] = [
                    'name' => 'Nginx Error Log',
                    'path' => $pattern,
                    'icon' => 'solar:server-square-bold',
                    'color' => 'warning'
                ];
                break;
            }
        }

        // PHP Errors (Linux + loaded ini)
        $phpCandidates = [
            '/var/log/php8.3-fpm.log',
            '/var/log/php8.2-fpm.log',
            '/var/log/php-fpm.log',
            '/var/log/php_errors.log',
            $nucleusRoot . '/tmp/php_errors.log',
            dirname(php_ini_loaded_file()) . '/php_errors.log'
        ];

        foreach ($phpCandidates as $pattern) {
            if (file_exists($pattern) && is_readable($pattern)) {
                $logFiles['php'] = [
                    'name' => 'PHP Error Log',
                    'path' => $pattern,
                    'icon' => 'solar:code-bold',
                    'color' => 'purple'
                ];
                break;
            }
        }

        // MySQL / MariaDB (Linux + Legacy)
        $mysqlCandidates = [
            '/var/log/mysql/error.log',
            '/var/log/mariadb/mariadb.log',
            '/var/log/mysql/mariadb.log',
            $nucleusRoot . '/data/mysqld.log'
        ];
        
        foreach ($mysqlCandidates as $pattern) {
            if (file_exists($pattern) && is_readable($pattern)) {
                $logFiles['mysql'] = [
                    'name' => 'MySQL / MariaDB Log',
                    'path' => $pattern,
                    'icon' => 'solar:database-bold',
                    'color' => 'info'
                ];
                break;
            }
        }

        // 2TI Orchestrator / Laravel Logs
        $laravelLog = '/var/www/html/2ti-orchestrator/storage/logs/laravel.log';
        if (file_exists($laravelLog) && is_readable($laravelLog)) {
            $logFiles['orchestrator'] = [
                'name' => '2TI Orchestrator Log',
                'path' => $laravelLog,
                'icon' => 'solar:box-minimalistic-bold',
                'color' => 'success'
            ];
        }

        // Postfix / Mail Log
        $mailCandidates = [
            '/var/log/mail.log',
            '/var/log/syslog'
        ];
        foreach ($mailCandidates as $pattern) {
            if (file_exists($pattern) && is_readable($pattern)) {
                $logFiles['mail'] = [
                    'name' => 'System Mail / Postfix Log',
                    'path' => $pattern,
                    'icon' => 'solar:letter-bold',
                    'color' => 'secondary'
                ];
                break;
            }
        }
        
        return $logFiles;
    }

    /**
     * Read N lines from a log file
     */
    public static function read(string $path, int $lines = 1000): array|false {
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }

        try {
            $handle = @fopen($path, 'r');
            if (!$handle) {
                return false;
            }

            $fileSize = filesize($path);
            $lines_array = [];
            
            // If file is small, just read everything
            if ($fileSize < 1024 * 512) { // 512KB
                $content_raw = file_get_contents($path);
                $lines_raw = explode("\n", $content_raw);
                $lines_array = array_slice($lines_raw, -$lines);
                $totalLines = count($lines_raw);
            } else {
                // Efficient tail for large files
                $buffer = 4096;
                fseek($handle, 0, SEEK_END);
                $pos = ftell($handle);
                $count = 0;
                $output = '';

                while ($pos > 0 && $count < $lines + 1) {
                    $readSize = min($pos, $buffer);
                    $pos -= $readSize;
                    fseek($handle, $pos);
                    $chunk = fread($handle, $readSize);
                    $output = $chunk . $output;
                    $count = substr_count($output, "\n");
                }

                $lines_raw = explode("\n", $output);
                $lines_array = array_slice($lines_raw, -$lines);
                // Approximate total lines based on average line length (fine for UI)
                $totalLines = (int)($fileSize / 100); 
            }
            
            fclose($handle);
            
            // Clean up lines
            $final_content = implode("\n", array_map(function($l) {
                return rtrim($l, "\r\n");
            }, $lines_array));

            return [
                'content' => $final_content ?: '(Empty log file)',
                'total_lines' => $totalLines,
                'displayed_lines' => count($lines_array),
                'path' => $path
            ];
        } catch (\Exception $e) {
            if (class_exists('\\Nucleus\\Core\\Logger')) {
                \Nucleus\Core\Logger::error("Failed to read log file $path: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Clear a log file
     */
    public static function clear(string $path): bool {
        if (file_exists($path) && is_writable($path)) {
            return file_put_contents($path, '') !== false;
        }
        return false;
    }
}
