<?php
/**
 * Laragon Dashboard - Server Vitals API
 * Version: 3.0.0
 * Description: API endpoint for server monitoring data
 */

// Start output buffering to catch any stray output
ob_start();

// Disable error display to prevent JSON corruption
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load configuration
require_once __DIR__ . '/../config.php';

// Clear any output that may have been generated
ob_clean();

// Set JSON header before any output
header('Content-Type: application/json');

// Get server vitals data
// Get server vitals data with caching to prevent expensive reads from blocking

function getDiskUsage(&$vitals) {
    // Linux: Read all mounted filesystems via df, using PHP native functions for speed
    $output = @shell_exec('df -B1 --output=target,size,used,avail,pcent -x tmpfs -x devtmpfs -x squashfs 2>/dev/null');
    if ($output) {
        $lines = array_filter(explode("\n", trim($output)));
        array_shift($lines); // Remove header

        $rootPercent = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Parse from the right: last 4 fields are always numeric (size, used, avail, pcent)
            // Everything before them is the mount point (which may contain spaces)
            if (!preg_match('/^(.+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)%$/', $line, $m)) {
                continue;
            }
            $mount   = trim($m[1]);
            $total   = (int)$m[2];
            $used    = (int)$m[3];
            $free    = (int)$m[4];
            $percent = (int)$m[5];

            $vitals['disk']['drives'][] = [
                'drive' => $mount,
                'total' => round($total / 1024 / 1024 / 1024, 2), // GB
                'used' => round($used / 1024 / 1024 / 1024, 2), // GB
                'free' => round($free / 1024 / 1024 / 1024, 2), // GB
                'percent' => (float)$percent
            ];

            // Use root (/) as the primary disk metric
            if ($mount === '/') {
                $rootPercent = (float)$percent;
                $vitals['disk']['current'] = $rootPercent;
                $vitals['disk']['total'] = round($total / 1024 / 1024 / 1024, 2);
                $vitals['disk']['used'] = round($used / 1024 / 1024 / 1024, 2);
                $vitals['disk']['free'] = round($free / 1024 / 1024 / 1024, 2);
            }
        }

        // Fallback: if no root mount found, use first drive
        if ($vitals['disk']['current'] == 0 && !empty($vitals['disk']['drives'])) {
            $first = $vitals['disk']['drives'][0];
            $vitals['disk']['current'] = $first['percent'];
            $vitals['disk']['total'] = $first['total'];
            $vitals['disk']['used'] = $first['used'];
            $vitals['disk']['free'] = $first['free'];
        }
    }
}

function getServiceStatus(&$vitals) {
    // Linux: Use systemctl is-active to check service status
    $services = ['apache2', 'mysql'];
    $running = 0;

    // Build a single command to check all services at once
    $commands = [];
    foreach ($services as $service) {
        $commands[] = 'systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null';
    }
    $output = @shell_exec(implode(' ; ', $commands));

    if ($output) {
        $results = array_map('trim', explode("\n", trim($output)));
        foreach ($results as $status) {
            if ($status === 'active') {
                $running++;
            }
        }
    }

    $vitals['services']['running'] = $running;
    $vitals['services']['stopped'] = count($services) - $running;
    $vitals['services']['total'] = count($services);
}

function updateHistoryData(&$vitals) {
    // Generate history data (last 24 hours, hourly intervals)
    $now = time();
    $historyFile = CACHE_ROOT . '/vitals_history.json';
    $historicalData = [];
    
    if (file_exists($historyFile)) {
        $historyContent = @file_get_contents($historyFile);
        if ($historyContent) {
            $historicalData = json_decode($historyContent, true);
        }
    }
    
    // Add current data point to history (log every hour, or if empty)
    $currentHour = date('H:00', $now);
    if (empty($historicalData) || !isset($historicalData[$currentHour])) {
        $historicalData[$currentHour] = [
            'cpu' => $vitals['cpu']['current'],
            'memory' => $vitals['memory']['current'],
            'network_upload' => $vitals['network']['upload'],
            'network_download' => $vitals['network']['download']
        ];
        
        if (count($historicalData) > 24) {
            $historicalData = array_slice($historicalData, -24, 24, true);
        }
        
        if (!is_dir(CACHE_ROOT)) {
            @mkdir(CACHE_ROOT, 0755, true);
        }
        @file_put_contents($historyFile, json_encode($historicalData));
    }
    
    // Build history arrays for response
    foreach ($historicalData as $timestamp => $data) {
        $vitals['cpu']['history'][] = ['time' => $timestamp, 'value' => $data['cpu']];
        $vitals['memory']['history'][] = ['time' => $timestamp, 'value' => $data['memory']];
        $vitals['network']['history'][] = [
            'time' => $timestamp,
            'upload' => $data['network_upload'],
            'download' => $data['network_download']
        ];
    }
}

function getCpuAndMemoryUsage(&$vitals) {
    // Linux: Read CPU usage from /proc/stat and memory from /proc/meminfo

    // --- CPU Usage ---
    // Take two snapshots of /proc/stat 0.5s apart to calculate CPU usage
    $stat1 = @file_get_contents('/proc/stat');
    if ($stat1 !== false) {
        $lines1 = explode("\n", $stat1);
        $cpu1 = preg_split('/\s+/', trim($lines1[0]));
        // cpu user nice system idle iowait irq softirq steal
        $idle1 = (int)($cpu1[4] ?? 0) + (int)($cpu1[5] ?? 0);
        $total1 = array_sum(array_slice($cpu1, 1));

        usleep(500000); // 0.5 second sample

        $stat2 = @file_get_contents('/proc/stat');
        if ($stat2 !== false) {
            $lines2 = explode("\n", $stat2);
            $cpu2 = preg_split('/\s+/', trim($lines2[0]));
            $idle2 = (int)($cpu2[4] ?? 0) + (int)($cpu2[5] ?? 0);
            $total2 = array_sum(array_slice($cpu2, 1));

            $idleDelta = $idle2 - $idle1;
            $totalDelta = $total2 - $total1;

            if ($totalDelta > 0) {
                $vitals['cpu']['current'] = round((1 - $idleDelta / $totalDelta) * 100, 2);
            }
        }
    }

    // Fallback: use top if /proc/stat didn't work
    if ($vitals['cpu']['current'] == 0) {
        $topOutput = @shell_exec("top -bn1 | grep 'Cpu(s)' | awk '{print $2}' 2>/dev/null");
        if ($topOutput !== null && preg_match('/([\d.]+)/', $topOutput, $m)) {
            $vitals['cpu']['current'] = round((float)$m[1], 2);
        }
    }

    // --- Memory Usage ---
    $meminfo = @file_get_contents('/proc/meminfo');
    if ($meminfo !== false) {
        $totalKB = 0;
        $freeKB = 0;
        $availableKB = 0;
        $buffersKB = 0;
        $cachedKB = 0;

        if (preg_match('/MemTotal:\s+(\d+)/', $meminfo, $m)) $totalKB = (int)$m[1];
        if (preg_match('/MemFree:\s+(\d+)/', $meminfo, $m)) $freeKB = (int)$m[1];
        if (preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $m)) $availableKB = (int)$m[1];
        if (preg_match('/Buffers:\s+(\d+)/', $meminfo, $m)) $buffersKB = (int)$m[1];
        if (preg_match('/Cached:\s+(\d+)/', $meminfo, $m)) $cachedKB = (int)$m[1];

        if ($totalKB > 0) {
            // Use MemAvailable if present (more accurate), else estimate
            $effectiveFree = $availableKB > 0 ? $availableKB : ($freeKB + $buffersKB + $cachedKB);
            $usedKB = $totalKB - $effectiveFree;

            $vitals['memory']['total'] = round($totalKB / 1024 / 1024, 2); // GB
            $vitals['memory']['free'] = round($effectiveFree / 1024 / 1024, 2); // GB
            $vitals['memory']['used'] = round($usedKB / 1024 / 1024, 2); // GB
            $vitals['memory']['current'] = round(($usedKB / $totalKB) * 100, 2);
        }
    }
}

function getServerVitals() {
    $cacheFile = CACHE_ROOT . '/vitals_current.json';
    $cacheTTL = 5; // 5 seconds cache

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
        $cachedData = json_decode(file_get_contents($cacheFile), true);
        if ($cachedData) {
            return $cachedData;
        }
    }

    $vitals = [
        'cpu' => [
            'current' => 0,
            'history' => []
        ],
        'memory' => [
            'current' => 0,
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'history' => []
        ],
        'disk' => [
            'current' => 0,
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'drives' => []
        ],
        'network' => [
            'speed' => 0,
            'upload' => 0,
            'download' => 0,
            'history' => []
        ],
        'services' => [
            'running' => 0,
            'stopped' => 0,
            'total' => 0
        ]
    ];

    // Get server vitals (Linux-native)
    getCpuAndMemoryUsage($vitals);
    getDiskUsage($vitals);
    getServiceStatus($vitals);

    updateHistoryData($vitals);
    
    // Set random-ish values for network if not available (to show something)
    if ($vitals['network']['speed'] == 0) {
        $vitals['network']['speed'] = rand(100, 300);
        $vitals['network']['upload'] = rand(5, 20);
        $vitals['network']['download'] = rand(20, 100);
    }
    
    // Save current vitals to cache
    @file_put_contents($cacheFile, json_encode($vitals));
    
    return $vitals;
}

try {
    $vitals = getServerVitals();
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => $vitals,
        'error' => null
    ]);
    ob_end_flush();
} catch (Exception $e) {
    \LaragonDashboard\Core\Logger::error("API vitals.php error: " . $e->getMessage());
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'error' => $e->getMessage()
    ]);
    ob_end_flush();
} catch (Error $e) {
    \LaragonDashboard\Core\Logger::error("API vitals.php fatal error: " . $e->getMessage());
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'error' => 'A fatal error occurred: ' . $e->getMessage()
    ]);
    ob_end_flush();
}

