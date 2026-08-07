ost<?php
/**
 * Nucleus - Server Vitals Page
 * Version: 3.0.0
 * Description: Server monitoring with charts
 */

// Load configuration and helpers
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
}

if (file_exists(__DIR__ . '/../includes/helpers.php')) {
    require_once __DIR__ . '/../includes/helpers.php';
}

// Load i18n helper
if (file_exists(__DIR__ . '/../includes/i18n.php')) {
    require_once __DIR__ . '/../includes/i18n.php';
}

// Load translations
$vitalsTranslations = [];
if (function_exists('load_translations')) {
    $vitalsTranslations = load_translations('vitals');
}

function t_vitals($key, $fallback = '') {
    global $vitalsTranslations;
    if (function_exists('t')) {
        $translated = t('vitals.' . $key);
        if ($translated !== 'vitals.' . $key) {
            return $translated;
        }
    }
    return $vitalsTranslations[$key] ?? ($fallback ?: $key);
}

/* -----------------------------------------
 * --- Hardware Vitals Monitoring Functions ---
 * ----------------------------------------- */
function get_cpu_cores() {
    // Tries to detect physical cores using standard Linux tools.
    $cores = trim(shell_exec('nproc'));
    return is_numeric($cores) ? (int)$cores : 'Unknown';
}

/**
 * Reads system hardware sensors and returns structured data.
 * Assumes the 'sensors' command is available on the host OS.
 */
function get_system_sensors() {
    $temp_data = ['cpu' => null, 'main' => null]; // Store temperature data by source/location
    $fan_status = [];

    // Check if sensors command exists and run it
    if (shell_exec('which sensors') === '') {
        return [
            'error' => t('vitals.sensor_unavailable', 'Hardware monitoring tools not found on this system.')
        ];
    }

    $sensor_output = shell_exec('sensors');

    if (empty($sensor_output) || strpos(strtolower($sensor_output), 'error') !== false) {
        return ['error' => t('vitals.sensor_unavailable', 'Failed to read sensor data.')];
    }

    $lines = explode("\n", $sensor_output);
    foreach ($lines as $line) {
        // Pattern matching CPU/Core temperature lines (e.g., Core 0: +45.0°C)
        if (preg_match('/([a-zA-Z0-9._-]+)[ \t]*[:]\s*([\d\.]+) *[°C]/', $line, $matches)) {
            $type = strtolower(trim($matches[1], ':-'));
            $value = (float)$matches[2];

            if ($type == 'core' || $type == 'cpu') {
                $temp_data['cpu'] = round($value, 1);
            } else if (!in_array($type, ['gpu', 'cpu'])) { // Avoid overwriting with noisy types
                 // Capture other detected temperatures as a general main temp if not already captured
                 if (is_null($temp_data['main']) || $temp_data['main'] > 0) {
                    $temp_data['main'] = round($value, 1);
                }
            }
        } 
        // Pattern matching Fan lines (e.g., fan: 1300 RPM)
        elseif (preg_match('/([a-zA-Z\s]+):\s*(\d+)\s*(RPM|Hz)/i', $line, $matches)) {
            $fan_status[] = ['name' => trim($matches[1]) ?: 'Fan', 'speed' => $matches[2], 'unit' => $matches[3]];
        }
    }

    return [
        'error' => null,
        'cpu_temp' => $temp_data['cpu'] ?? null,
        'main_temp' => $temp_data['main'] ?? null,
        // Combine all fan readings into a single status string
        'fan_status' => count($fan_status) > 0 ? implode(', ', array_map(function($f) { return "{$f['name']} {$f['speed']} {$f['unit']}"; }, $fan_status)) : 'N/A',
    ];
}

// Initialize global data structures for the template to use
$globalVitalsData = [
    'cores' => get_cpu_cores(),
    'hardware' => get_system_sensors()
];

$vitalsHardwareData = $globalVitalsData;


include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <strong><p class="fw-semibold mb-0"><?php echo t_vitals('server_vitals', 'Server Vitals'); ?></p></strong>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        <?php echo t_vitals('dashboard', 'Dashboard'); ?>
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium"><?php echo t_vitals('server_vitals', 'Server Vitals'); ?></li>
            </ul>
        </div>

        <!-- KPI Cards -->
        <div class="row g-3 mb-24">
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-primary-100 text-primary-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:cpu-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2" id="cpu-usage">0%</p>
                                    <span class="fw-medium text-secondary-light text-sm"><?php echo t_vitals('cpu_usage', 'CPU Usage'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div id="cpu-chart-mini" style="height: 50px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-success-100 text-success-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:database-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2" id="memory-usage">0%</p>
                                    <span class="fw-medium text-secondary-light text-sm"><?php echo t_vitals('memory_usage', 'Memory Usage'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div id="memory-chart-mini" style="height: 50px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-info-100 text-info-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:network-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2" id="network-status"><?php echo t_vitals('active', 'Active'); ?></p>
                                    <span class="fw-medium text-secondary-light text-sm"><?php echo t_vitals('network', 'Network'); ?></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-info-600" id="network-speed">0</span> <?php echo t_vitals('mbps', 'Mbps'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Hardware Vitals Card -->
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-primary-100 text-primary-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:cpu-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2"><?php echo t_vitals('hardware', 'Hardware Vitals'); ?></p>
                                    <span class="fw-medium text-secondary-light text-sm">Cores: <?php echo $globalVitalsData['cores']; ?></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm mb-0 mt-2 border-top pt-3">
                            <?php if (!empty($vitalsHardwareData['error'])): ?>
                                <span class="text-danger"><?php echo $vitalsHardwareData['error']; ?></span>
                            <?php else : ?>
                                <strong class="d-block mb-1 text-muted">CPU Temp:</strong> <span id="cpu-temp-val"><?php echo $vitalsHardwareData['cpu_temp'] ? $vitalsHardwareData['cpu_temp'] . '°C' : '--'; ?></span><br>
                                <strong class="d-block mb-1 text-muted">Main Temp:</strong> <span id="main-temp-val"><?php echo $vitalsHardwareData['main_temp'] ? $vitalsHardwareData['main_temp'] . '°C' : '--'; ?></span><br>
                                <strong class="d-block mb-1 text-muted">Fans:</strong> <span id="fan-status-val"><?php echo $vitalsHardwareData['fan_status']; ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-warning-100 text-warning-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:hard-drive-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2" id="disk-usage">0%</p>
                                    <span class="fw-medium text-secondary-light text-sm"><?php echo t_vitals('disk_usage', 'Disk Usage'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div id="disk-chart-mini" style="height: 50px;"></div>
                    </div>
                </div>
            </div>

            <!-- Hardware Vitals Card (NEW) -->
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-primary-100 text-primary-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:cpu-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2"><?php echo t_vitals('hardware', 'Hardware Vitals'); ?></p>
                                    <span class="fw-medium text-secondary-light text-sm">Cores: <?php echo $globalVitalsData['cores']; ?></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm mt-2 border-top pt-3">
                            <?php if (!empty($vitalsHardwareData['error'])): ?>
                                <span class="text-danger"><?php echo $vitalsHardwareData['error']; ?></span>
                            <?php else : ?>
                                <strong>CPU Temp:</strong> <span id="cpu-temp-val"><?php echo $vitalsHardwareData['cpu_temp'] ? $vitalsHardwareData['cpu_temp'] . '°C' : '--'; ?></span><br>
                                <strong>Main Temp:</strong> <span id="main-temp-val"><?php echo $vitalsHardwareData['main_temp'] ? $vitalsHardwareData['main_temp'] . '°C' : '--'; ?></span><br>
                                <strong>Fans:</strong> <span id="fan-status-val"><?php echo $vitalsHardwareData['fan_status']; ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-info-100 text-info-600 flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="solar:network-bold" class="text-xl"></iconify-icon>
                                </span>
                                <div>
                                    <p class="fw-semibold mb-2" id="network-status"><?php echo t_vitals('active', 'Active'); ?></p>
                                    <span class="fw-medium text-secondary-light text-sm"><?php echo t_vitals('network', 'Network'); ?></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-info-600" id="network-speed">0</span> <?php echo t_vitals('mbps', 'Mbps'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1: CPU & Memory -->
        <div class="row g-3 mb-24">
            <div class="col-lg-6">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <strong><p class="fw-semibold mb-0"><?php echo t_vitals('cpu_usage', 'CPU Usage'); ?></p></strong>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary-light text-sm"><?php echo t_vitals('last_24_hours', 'Last 24 Hours'); ?></span>
                            </div>
                        </div>
                        <div id="cpu-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <strong><p class="fw-semibold mb-0"><?php echo t_vitals('memory_usage', 'Memory Usage'); ?></p></strong>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary-light text-sm"><?php echo t_vitals('last_24_hours', 'Last 24 Hours'); ?></span>
                            </div>
                        </div>
                        <div id="memory-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2: Disk Usage & Service Status -->
        <div class="row g-3 mb-24">
            <div class="col-lg-6">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <strong><p class="fw-semibold mb-0"><?php echo t_vitals('disk_usage', 'Disk Usage'); ?></p></strong>
                        </div>
                        <div id="disk-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <strong><p class="fw-semibold mb-0"><?php echo t_vitals('service_status', 'Service Status'); ?></p></strong>
                        </div>
                        <div id="services-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3: Network Traffic -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <strong><p class="fw-semibold mb-0"><?php echo t_vitals('network_traffic', 'Network Traffic'); ?></p></strong>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary-light text-sm"><?php echo t_vitals('last_24_hours', 'Last 24 Hours'); ?></span>
                            </div>
                        </div>
                        <div id="network-chart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Store script variables for later inclusion
$GLOBALS['vitalsScript'] = true;
?>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>

