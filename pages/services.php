<?php
/**
 * Nucleus - Services Page
 * Version: 3.0.0
 * Description: Service management with port configuration
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
$servicesTranslations = [];
if (function_exists('load_translations')) {
    $servicesTranslations = load_translations('services');
}

function t_services($key, $fallback = '') {
    global $servicesTranslations;
    if (function_exists('t')) {
        $translated = t('services.' . $key);
        if ($translated !== 'services.' . $key) {
            return $translated;
        }
    }
    return $servicesTranslations[$key] ?? ($fallback ?: $key);
}

$nucleusRoot = defined('NUCLEUS_ROOT') ? NUCLEUS_ROOT : __DIR__ . '/../..'; // Nucleus root directory fallback

// Define available services with default ports (Linux/Nucleus)
$availableServices = [
    'Apache' => [
        'name' => 'Apache',
        'port' => '80',
        'ssl_port' => '443',
        'enabled' => true,
        'has_ssl' => true,
        'icon' => 'devicon-plain:apache',
        'color' => 'primary',
        'service_name' => 'apache2',
    ],
    'MySQL' => [
        'name' => 'MySQL',
        'port' => '3306',
        'ssl_port' => null,
        'enabled' => true,
        'has_ssl' => false,
        'icon' => 'tabler:brand-mysql',
        'color' => 'info',
        'service_name' => 'mysql',
    ],
    'PostgreSQL' => [
        'name' => 'PostgreSQL',
        'port' => '5432',
        'ssl_port' => null,
        'enabled' => false,
        'has_ssl' => false,
        'icon' => 'devicon-plain:postgresql',
        'color' => 'secondary',
        'service_name' => 'postgresql',
    ],
    'Nginx' => [
        'name' => 'Nginx',
        'port' => '8080',
        'ssl_port' => '8443',
        'enabled' => false,
        'has_ssl' => true,
        'icon' => 'devicon-plain:nginx',
        'color' => 'success',
        'service_name' => 'nginx',
    ],
    'Memcached' => [
        'name' => 'Memcached',
        'port' => '11211',
        'ssl_port' => null,
        'enabled' => false,
        'has_ssl' => false,
        'icon' => 'devicon-plain:memcached',
        'color' => 'warning',
        'service_name' => 'memcached',
    ],
    'Redis' => [
        'name' => 'Redis',
        'port' => '6379',
        'ssl_port' => null,
        'enabled' => false,
        'has_ssl' => false,
        'icon' => 'devicon-plain:redis',
        'color' => 'danger',
        'service_name' => 'redis-server',
    ],
    'MongoDB' => [
        'name' => 'MongoDB',
        'port' => '27017',
        'ssl_port' => null,
        'enabled' => false,
        'has_ssl' => false,
        'icon' => 'devicon-plain:mongodb',
        'color' => 'success',
        'service_name' => 'mongod',
    ],
    'Mailpit' => [
        'name' => 'Mailpit',
        'port' => '1025',
        'ssl_port' => '8025', // HTTP port for web UI
        'enabled' => false,
        'has_ssl' => true, // Show HTTP port field
        'icon' => 'solar:letter-bold',
        'color' => 'purple',
        'service_name' => 'mailpit',
    ]
];

// Check if a service is installed on the system (Linux-native via systemd)
function checkServiceInstalled(string $serviceName): bool {
    // Check if systemd unit exists (works for both active and inactive services)
    $output = @shell_exec('systemctl list-unit-files ' . escapeshellarg($serviceName . '.service') . ' 2>/dev/null');
    if ($output && stripos($output, $serviceName) !== false) {
        return true;
    }

    // Fallback: check if the binary exists in PATH
    $binaryMap = [
        'apache2' => 'apache2ctl',
        'mysql' => 'mysql',
        'mariadb' => 'mariadb',
        'postgresql' => 'psql',
        'nginx' => 'nginx',
        'redis-server' => 'redis-cli',
        'memcached' => 'memcached',
        'mongod' => 'mongod',
        'php-fpm' => 'php-fpm',
        'postfix' => 'postfix',
    ];

    $binary = $binaryMap[$serviceName] ?? $serviceName;
    $which = trim(@shell_exec('which ' . escapeshellarg($binary) . ' 2>/dev/null') ?? '');
    return !empty($which);
}

// Detect PHP-FPM service name (varies by version)
function detectPhpFpmService(): ?string {
    $versions = ['8.3', '8.2', '8.1', '8.0', '7.4'];
    foreach ($versions as $ver) {
        $unit = 'php' . $ver . '-fpm';
        $output = @shell_exec('systemctl list-unit-files ' . escapeshellarg($unit . '.service') . ' 2>/dev/null');
        if ($output && stripos($output, $unit) !== false) {
            return $unit;
        }
    }
    // Generic fallback
    $output = @shell_exec('systemctl list-unit-files 2>/dev/null | grep php.*fpm');
    if ($output && preg_match('/(php[\d.]+-fpm)/', $output, $m)) {
        return $m[1];
    }
    return null;
}

// Filter to only show installed services
$installedServices = [];
foreach ($availableServices as $key => $service) {
    if (checkServiceInstalled($service['service_name'])) {
        $installedServices[$key] = $service;
    }
}

// Add PHP-FPM dynamically if detected
$phpFpmService = detectPhpFpmService();
if ($phpFpmService) {
    $installedServices['PHP-FPM'] = [
        'name' => 'PHP-FPM',
        'port' => '9000',
        'ssl_port' => null,
        'enabled' => true,
        'has_ssl' => false,
        'icon' => 'devicon-plain:php',
        'color' => 'indigo',
        'service_name' => $phpFpmService,
    ];
}

// Add Postfix if detected (Linux mail transfer agent)
if (checkServiceInstalled('postfix')) {
    $installedServices['Postfix'] = [
        'name' => 'Postfix',
        'port' => '25',
        'ssl_port' => null,
        'enabled' => true,
        'has_ssl' => false,
        'icon' => 'solar:mail-bold',
        'color' => 'teal',
        'service_name' => 'postfix',
    ];
}

include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <strong><p class="fw-semibold mb-0"><?php echo t_services('services', 'Services'); ?></p></strong>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        <?php echo t_services('dashboard', 'Dashboard'); ?>
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium"><?php echo t_services('services', 'Services'); ?></li>
            </ul>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-24" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab">
                    <?php echo t_services('services_ports', 'Services & Ports'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <?php echo t_services('general', 'General'); ?>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Services & Ports Tab -->
            <div class="tab-pane fade show active" id="services" role="tabpanel">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <form id="services-form">
                            <div class="table-responsive scroll-sm">
                                <table class="table bordered-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="bg-transparent rounded-0" style="width: 200px;"><?php echo t_services('service', 'Service'); ?></th>
                                            <th scope="col" class="bg-transparent rounded-0" style="width: 120px;"><?php echo t_services('port', 'Port'); ?></th>
                                            <th scope="col" class="bg-transparent rounded-0" style="width: 120px;"><?php echo t_services('ssl_port', 'SSL Port'); ?></th>
                                            <th scope="col" class="bg-transparent rounded-0" style="width: 100px;"><?php echo t_services('enabled', 'Enabled'); ?></th>
                                            <th scope="col" class="bg-transparent rounded-0" style="width: 100px;"><?php echo t_services('status', 'Status'); ?></th>
                                            <th scope="col" class="bg-transparent rounded-0" style="width: 150px;"><?php echo t_services('resources', 'Resources'); ?></th>
                                            <th scope="col" class="bg-transparent rounded-0"><?php echo t_services('actions', 'Actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="services-list">
                                        <?php
                                        // ⚡ Bolt: Batch systemctl queries for all services
                                        $systemctlServices = array_column($installedServices, 'service_name');
                                        $systemctlOutput = '';
                                        if (!empty($systemctlServices)) {
                                            $systemctlOutput = @shell_exec('systemctl is-active ' . implode(' ', array_map('escapeshellarg', $systemctlServices)) . ' 2>&1');
                                        }
                                        $systemctlLines = array_filter(explode("\n", trim($systemctlOutput ?? '')));

                                        // ⚡ Bolt: Single ss call for all port checks
                                        $ssOutput = @shell_exec('ss -tlnp 2>&1');

                                        foreach ($installedServices as $idx => $service):
                                            // Check service status via systemctl
                                            $line = trim($systemctlLines[$idx] ?? 'inactive');
                                            $status = ($line === 'active') ? 'running' : 'stopped';
                                            $runningPorts = [];

                                            if ($status === 'running') {
                                                if (!empty($service['port']) && $ssOutput && preg_match('/:' . preg_quote((string)$service['port'], '/') . '\s/', $ssOutput)) {
                                                    $runningPorts[] = $service['port'];
                                                }
                                                if (!empty($service['ssl_port']) && $ssOutput && preg_match('/:' . preg_quote((string)$service['ssl_port'], '/') . '\s/', $ssOutput)) {
                                                    $runningPorts[] = $service['ssl_port'];
                                                }
                                            }

                                            // JavaScript will refresh status via API for real-time updates

                                            $runningPortsStr = !empty($runningPorts) ? implode('/', $runningPorts) : '-';
                                        ?>
                                        <tr class="service-row" data-key="<?php echo $key; ?>">
                                            <td data-field="service">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="form-check style-check">
                                                        <input class="form-check-input service-enabled-checkbox" type="checkbox" id="service-<?php echo $key; ?>" name="services[<?php echo $key; ?>][enabled]" value="1" data-service="<?php echo $key; ?>" <?php echo $service['enabled'] ? 'checked' : ''; ?>>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="w-32-px h-32-px bg-<?php echo $service['color']; ?>-50 rounded-circle d-flex justify-content-center align-items-center">
                                                            <iconify-icon icon="<?php echo htmlspecialchars($service['icon']); ?>" class="text-<?php echo $service['color']; ?>-main"></iconify-icon>
                                                        </div>
                                                        <span class="fw-medium"><?php echo htmlspecialchars($service['name']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-field="port">
                                                <input type="number" class="form-control form-control-sm" name="services[<?php echo $key; ?>][port]" value="<?php echo htmlspecialchars($service['port']); ?>" min="1" max="65535">
                                            </td>
                                            <td data-field="ssl_port">
                                                <?php if ($service['has_ssl']): ?>
                                                    <input type="number" class="form-control form-control-sm" name="services[<?php echo $key; ?>][ssl_port]" value="<?php echo htmlspecialchars($service['ssl_port'] ?? ''); ?>" min="1" max="65535" placeholder="<?php echo $key === 'Mailpit' ? 'HTTP Port' : 'SSL Port'; ?>">
                                                <?php else: ?>
                                                    <span class="text-secondary-light">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-field="enabled">
                                                <div class="form-switch switch-<?php echo $service['color']; ?> d-flex align-items-center">
                                                    <input class="form-check-input service-enabled-switch" type="checkbox" role="switch" name="services[<?php echo $key; ?>][enabled_check]" data-service="<?php echo $key; ?>" <?php echo $service['enabled'] ? 'checked' : ''; ?>>
                                                </div>
                                            </td>
                                            <td data-field="status">
                                                <span class="bg-<?php echo $status === 'running' ? 'success' : 'secondary'; ?>-focus text-<?php echo $status === 'running' ? 'success' : 'secondary'; ?>-main px-24 py-4 rounded-pill fw-medium text-sm">
                                                    <?php echo $status === 'running' ? t_services('running', 'Running') : t_services('stopped', 'Stopped'); ?>
                                                </span>
                                                <?php if ($status === 'running' && !empty($runningPortsStr)): ?>
                                                    <br><small class="text-secondary-light mt-4 d-block"><?php echo htmlspecialchars($runningPortsStr); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td data-field="resources">
                                                <!-- Resources Column Content -->
                                                <div class="d-flex align-items-center gap-2 text-secondary-light">
                                                    <a href="#" class="text-primary-600 hover-text-primary small cursor-pointer" title="<?php echo t_services('resources', 'Resources'); ?>"><?php echo t_services('resource_info', 'Resource Info'); ?></a>
                                                </div>
                                            </td>
                                            <td data-field="actions">
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if ($key === 'Apache'): ?>
                                                        <button type="button" class="btn btn-sm btn-primary-100 text-primary-600" onclick="reloadApache()" aria-label="<?php echo t_services('reload', 'Reload') . ' ' . htmlspecialchars($key); ?>">
                                                            <iconify-icon icon="solar:refresh-bold" class="icon"></iconify-icon>
                                                            <?php echo t_services('reload', 'Reload'); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($status === 'running'): ?>
                                                        <button type="button" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="restartService('<?php echo $key; ?>')" title="<?php echo t_services('restart', 'Restart'); ?>" aria-label="<?php echo t_services('restart', 'Restart') . ' ' . htmlspecialchars($key); ?>">
                                                            <iconify-icon icon="solar:refresh-bold"></iconify-icon>
                                                        </button>
                                                        <button type="button" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="stopService('<?php echo $key; ?>')" title="<?php echo t_services('stop', 'Stop'); ?>" aria-label="<?php echo t_services('stop', 'Stop') . ' ' . htmlspecialchars($key); ?>">
                                                            <iconify-icon icon="solar:stop-bold"></iconify-icon>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="startService('<?php echo $key; ?>')" title="<?php echo t_services('start', 'Start'); ?>" aria-label="<?php echo t_services('start', 'Start') . ' ' . htmlspecialchars($key); ?>">
                                                            <iconify-icon icon="solar:play-bold"></iconify-icon>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table >
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between mt-24">
                                <a href="#" class="text-primary-600 hover-text-primary"><?php echo t_services('advanced', 'Advanced'); ?></a>
                                <button type="submit" class="btn btn-primary-600">
                                    <iconify-icon icon="solar:diskette-bold" class="icon"></iconify-icon>
                                    <?php echo t_services('save', 'Save'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- General Tab -->
            <div class="tab-pane fade" id="general" role="tabpanel">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-24">
                        <form id="general-settings-form">
                            <div class="row g-3">
                                <!-- Document Root -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-8"><?php echo t_services('document_root', 'Document Root'); ?></label>
                                    <input type="text" class="form-control" name="DocumentRoot" id="document-root" value="<?php echo htmlspecialchars($laragonRoot . '/html'); ?>" placeholder="<?php echo htmlspecialchars($laragonRoot . '/html'); ?>">
                                    <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('document_root_desc', 'Directory where your projects are stored'); ?></small>
                                </div>

                                <!-- Data Directory -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-8"><?php echo t_services('data_directory', 'Data Directory'); ?></label>
                                    <input type="text" class="form-control" name="DataDirectory" id="data-directory" value="<?php echo htmlspecialchars($laragonRoot . '/data'); ?>" placeholder="<?php echo htmlspecialchars($laragonRoot . '/data'); ?>">
                                    <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('data_directory_desc', 'Directory for Nucleus data files'); ?></small>
                                </div>

                                <!-- Domain Suffix -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-8"><?php echo t_services('domain_suffix', 'Domain Suffix'); ?></label>
                                    <input type="text" class="form-control" name="DomainSuffix" id="domain-suffix" value=".local" placeholder=".local">
                                    <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('domain_suffix_desc', 'Default domain suffix for virtual hosts'); ?></small>
                                </div>

                                <!-- Hostname Format -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-8"><?php echo t_services('hostname_format', 'Hostname Format'); ?></label>
                                    <input type="text" class="form-control" name="HostnameFormat" id="hostname-format" value="{name}.local" placeholder="{name}.local">
                                    <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('hostname_format_desc', 'Format for auto-generated hostnames'); ?></small>
                                </div>

                                <!-- Auto-start Services -->
                                <div class="col-12">
                                    <div class="form-check form-switch mb-16">
                                        <input class="form-check-input" type="checkbox" name="StartAllAutomatically" id="start-all-automatically" value="1">
                                        <label class="form-check-label fw-medium" for="start-all-automatically">
                                            <?php echo t_services('start_all_automatically', 'Start All Services Automatically'); ?>
                                        </label>
                                        <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('start_all_automatically_desc', 'Automatically start all enabled services on boot'); ?></small>
                                    </div>
                                </div>

                                <!-- Auto-create Virtual Hosts -->
                                <div class="col-12">
                                    <div class="form-check form-switch mb-16">
                                        <input class="form-check-input" type="checkbox" name="AutoCreateVirtualHosts" id="auto-create-vhosts" value="1">
                                        <label class="form-check-label fw-medium" for="auto-create-vhosts">
                                            <?php echo t_services('auto_create_vhosts', 'Auto-create Virtual Hosts'); ?>
                                        </label>
                                        <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('auto_create_vhosts_desc', 'Automatically create virtual hosts for new projects'); ?></small>
                                    </div>
                                </div>

                                <!-- Auto Backup -->
                                <div class="col-12">
                                    <div class="form-check form-switch mb-16">
                                        <input class="form-check-input" type="checkbox" name="AutoBackup" id="auto-backup" value="1">
                                        <label class="form-check-label fw-medium" for="auto-backup">
                                            <?php echo t_services('auto_backup', 'Auto Backup'); ?>
                                        </label>
                                        <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('auto_backup_desc', 'Automatically backup projects at scheduled intervals'); ?></small>
                                    </div>
                                </div>

                                <!-- Backup Interval -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-8"><?php echo t_services('backup_interval', 'Backup Interval (hours)'); ?></label>
                                    <input type="number" class="form-control" name="BackupInterval" id="backup-interval" value="8" min="1" max="168" step="1">
                                    <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('backup_interval_desc', 'Hours between automatic backups (1-168)'); ?></small>
                                </div>

                                <!-- Auto Update -->
                                <div class="col-12">
                                    <div class="form-check form-switch mb-16">
                                        <input class="form-check-input" type="checkbox" name="AutoUpdate" id="auto-update" value="1">
                                        <label class="form-check-label fw-medium" for="auto-update">
                                            <?php echo t_services('auto_update', 'Auto Update'); ?>
                                        </label>
                                        <small class="text-secondary-light text-sm mt-4 d-block"><?php echo t_services('auto_update_desc', 'Automatically check for Nucleus updates'); ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-end mt-24">
                                <button type="submit" class="btn btn-primary-600">
                                    <iconify-icon icon="solar:diskette-bold" class="icon"></iconify-icon>
                                    <?php echo t_services('save', 'Save'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Store script variables for later inclusion
$GLOBALS['servicesScript'] = true;
$GLOBALS['installedServices'] = $installedServices;
?>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>