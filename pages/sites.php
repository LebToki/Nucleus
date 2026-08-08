<?php
/**
 * Nucleus - Sites (Virtual Hosts) Page
 * Version: 3.0.0
 * Description: Manage Apache virtual hosts with code editor
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
$sitesTranslations = [];
if (function_exists('load_translations')) {
    $sitesTranslations = load_translations('sites');
}

function t_sites($key, $fallback = '') {
    global $sitesTranslations;
    if (function_exists('t')) {
        $translated = t('sites.' . $key);
        if ($translated !== 'sites.' . $key) {
            return $translated;
        }
    }
    return $sitesTranslations[$key] ?? ($fallback ?: $key);
}

// Get Laragon / Nucleus root and virtual host directory
$nucleusRoot = defined('NUCLEUS_ROOT') ? NUCLEUS_ROOT : __DIR__ . '/../..'; // Assuming the root is now determined differently
$sitesEnabledDir = '/etc/apache2/sites-enabled';
if (!is_dir($sitesEnabledDir)) {
    $sitesEnabledDir = '/etc/apache2/sites-available';
}
if (!is_dir($sitesEnabledDir) && is_dir('/etc/nginx/sites-enabled')) {
    $sitesEnabledDir = '/etc/nginx/sites-enabled';
}
$selectedFile = $_GET['file'] ?? '';

// Get list of virtual host files
$siteFiles = [];
if (is_dir($sitesEnabledDir)) {
    $files = glob($sitesEnabledDir . '/*.conf');
    foreach ($files as $file) {
        $siteFiles[] = [
            'name' => basename($file),
            'path' => $file,
            'size' => filesize($file),
            'modified' => filemtime($file)
        ];
    }
}

// Parse /etc/hosts for pretty URL management
$hostsContent = @file_get_contents('/etc/hosts');
$hostsEntries = [];
if ($hostsContent !== false) {
    foreach (explode("\n", $hostsContent) as $index => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '#') === 0) {
            continue;
        }
        $body = preg_replace('/\s+#.*$/', '', $trimmed);
        $parts = preg_split('/\s+/', $body);
        if (count($parts) >= 2 && filter_var($parts[0], FILTER_VALIDATE_IP)) {
            $hostsEntries[] = [
                'index' => $index,
                'ip' => $parts[0],
                'hosts' => implode(' ', array_slice($parts, 1)),
                'name' => $parts[1],
            ];
        }
    }
}

include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <strong><p class="fw-semibold mb-0"><?php echo t_sites('sites_enabled', 'Sites Enabled'); ?></p></strong>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        <?php echo t_sites('dashboard', 'Dashboard'); ?>
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium"><?php echo t_sites('sites_enabled', 'Sites Enabled'); ?></li>
            </ul>
        </div>

        <div class="row g-3">
            <!-- File List -->
            <div class="col-lg-4">
                <div class="card shadow-none border radius-12">
                    <div class="card-header border-bottom bg-base py-16 px-24">
                        <div class="d-flex align-items-center justify-content-between">
                            <strong><p class="fw-semibold mb-0"><?php echo t_sites('virtual_hosts', 'Virtual Hosts'); ?></p></strong>
                            <button type="button" class="btn btn-sm btn-primary-600" onclick="createNewSite()">
                                <iconify-icon icon="solar:add-circle-bold" class="icon"></iconify-icon>
                                <?php echo t_sites('new', 'New'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <?php if (empty($siteFiles)): ?>
                            <div class="text-center p-24 text-secondary-light">
                                <iconify-icon icon="solar:file-text-bold" class="text-4xl mb-8"></iconify-icon>
                                <p class="mb-0"><?php echo t_sites('no_files', 'No virtual host files found'); ?></p>
                            </div>
                        <?php else: ?>
                            <ul class="list-group radius-8" id="sites-list">
                                <?php 
                                $fileCount = count($siteFiles);
                                $index = 0;
                                foreach ($siteFiles as $file): 
                                    $isActive = $selectedFile === $file['name'];
                                    $isLast = (++$index === $fileCount);
                                    $borderClass = $isLast ? '' : 'border-bottom-0';
                                    // Alternate background: even = bg-neutral-50, odd = bg-base (matching template pattern)
                                    $bgClass = ($index % 2 === 0) ? 'bg-neutral-50' : 'bg-base';
                                    // Override with active state if selected
                                    if ($isActive) {
                                        $bgClass = 'bg-primary-50';
                                    }
                                ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between border text-secondary-light p-16 <?php echo $bgClass; ?> <?php echo $borderClass; ?>" style="cursor: pointer;" onclick="window.location.href='index.php?page=sites&file=<?php echo urlencode($file['name']); ?>'">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="w-32-px h-32-px bg-primary-50 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            <iconify-icon icon="solar:file-text-bold" class="text-primary-main text-sm"></iconify-icon>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-medium"><?php echo htmlspecialchars($file['name']); ?></p>
                                            <small class="text-secondary-light text-sm"><?php echo date('Y-m-d H:i', $file['modified']); ?></small>
                                        </div>
                                    </div>
                                    <?php if ($isActive): ?>
                                        <span class="text-xs bg-primary-100 text-primary-600 radius-4 px-10 py-2 fw-semibold"><?php echo t_sites('active', 'Active'); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs bg-neutral-100 text-neutral-600 radius-4 px-10 py-2 fw-semibold"><?php echo t_sites('view', 'View'); ?></span>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hosts File (Pretty URLs) -->
                <div class="card shadow-none border radius-12 mt-24">
                    <div class="card-header border-bottom bg-base py-16 px-24">
                        <div class="d-flex align-items-center justify-content-between">
                            <strong><p class="fw-semibold mb-0"><?php echo t_sites('hosts_file', '/etc/hosts — Pretty URLs'); ?></p></strong>
                            <button type="button" class="btn btn-sm btn-primary-100 text-primary-600" onclick="editHostsRaw()">
                                <iconify-icon icon="solar:code-bold" class="icon"></iconify-icon>
                                <?php echo t_sites('edit_raw', 'Edit Raw'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <form id="hosts-add-form" class="mb-16">
                            <div class="row g-2">
                                <div class="col-5">
                                    <input type="text" class="form-control form-control-sm" id="hosts-ip" placeholder="127.0.0.1" pattern="^[0-9a-fA-F.:]+$" required>
                                </div>
                                <div class="col-7">
                                    <input type="text" class="form-control form-control-sm" id="hosts-names" placeholder="mysite.local www.mysite.local" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary-600 w-100 mt-8 d-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:add-circle-bold" class="icon"></iconify-icon>
                                <?php echo t_sites('add_host', 'Add Host'); ?>
                            </button>
                        </form>
                        <?php if (empty($hostsEntries)): ?>
                            <div class="text-center p-16 text-secondary-light">
                                <p class="mb-0"><?php echo t_sites('no_hosts', 'No host entries found (or /etc/hosts unreadable)'); ?></p>
                            </div>
                        <?php else: ?>
                            <ul class="list-group radius-8" id="hosts-list" style="max-height: 340px; overflow-y: auto;">
                                <?php foreach ($hostsEntries as $entry): ?>
                                    <li class="list-group-item d-flex align-items-center gap-2 border text-secondary-light p-12">
                                        <div class="form-check form-switch mb-0 flex-shrink-0">
                                            <input class="form-check-input hosts-toggle" type="checkbox" role="switch" checked data-index="<?php echo $entry['index']; ?>">
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <p class="mb-0 fw-medium text-sm text-primary-600"><?php echo htmlspecialchars($entry['name']); ?></p>
                                            <small class="text-secondary-light text-xs d-block text-truncate"><?php echo htmlspecialchars($entry['ip'] . '  ' . $entry['hosts']); ?></small>
                                        </div>
                                        <button type="button" class="btn btn-sm p-0 border-0 bg-transparent text-danger hosts-remove" data-index="<?php echo $entry['index']; ?>" title="Disable entry">
                                            <iconify-icon icon="solar:trash-bin-trash-bold" class="text-lg"></iconify-icon>
                                        </button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Code Editor -->
            <div class="col-lg-8">
                <div class="card shadow-none border radius-12">
                    <div class="card-body p-0">
                        <div class="p-16 border-bottom d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-semibold mb-0" id="editor-title"><?php echo t_sites('select_file', 'Select a file'); ?></h6>
                                <p class="mb-0 text-secondary-light text-sm" id="editor-path"></p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-primary-100 text-primary-600" id="save-file" onclick="saveFile()" style="display: none;">
                                    <iconify-icon icon="solar:diskette-bold" class="icon"></iconify-icon>
                                    <?php echo t_sites('save', 'Save'); ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary-100 text-primary-600" id="refresh-file" onclick="refreshFile()" style="display: none;">
                                    <iconify-icon icon="solar:refresh-bold" class="icon"></iconify-icon>
                                    <?php echo t_sites('refresh', 'Refresh'); ?>
                                </button>
                            </div>
                        </div>
                        <div id="code-editor-container" class="position-relative" style="min-height: 500px;">
                            <?php if (empty($selectedFile)): ?>
                                <div class="p-24 text-center">
                                    <iconify-icon icon="solar:file-text-bold" class="text-secondary-light text-5xl mb-16"></iconify-icon>
                                    <p class="text-secondary-light mb-0"><?php echo t_sites('select_file_message', 'Select a virtual host file from the list to edit'); ?></p>
                                </div>
                            <?php else: ?>
                                <textarea id="code-editor"><?php echo t_sites('loading', 'Loading...'); ?></textarea>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Store script variables for later inclusion
$GLOBALS['sitesScript'] = true;
$GLOBALS['selectedFile'] = $selectedFile;
$GLOBALS['sitesEnabledDir'] = $sitesEnabledDir;
?>

<script>
(function() {
    'use strict';
    const HOSTS_API = 'api/hosts.php';

    function hostsRequest(action, payload) {
        const formData = new FormData();
        formData.append('csrf_token', window.csrfToken || '');
        for (const key in payload) {
            formData.append(key, payload[key]);
        }
        return fetch(HOSTS_API + '?action=' + action, { method: 'POST', body: formData })
            .then(response => response.json());
    }

    function hostsReload() {
        window.location.reload();
    }

    const addForm = document.getElementById('hosts-add-form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const ip = document.getElementById('hosts-ip').value.trim();
            const names = document.getElementById('hosts-names').value.trim();
            if (!ip || !names) return;
            hostsRequest('add', { ip: ip, hosts: names })
                .then(data => {
                    alert(data.success ? 'Host added' : 'Error: ' + (data.error || 'Failed'));
                    if (data.success) hostsReload();
                })
                .catch(error => alert('Error: ' + error.message));
        });
    }

    document.querySelectorAll('.hosts-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            hostsRequest('toggle', { index: this.dataset.index })
                .then(data => {
                    if (!data.success) {
                        alert('Error: ' + (data.error || 'Failed to toggle'));
                        this.checked = !this.checked;
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    this.checked = !this.checked;
                });
        });
    });

    document.querySelectorAll('.hosts-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Disable this host entry? It will be commented out in /etc/hosts.')) return;
            hostsRequest('remove', { index: this.dataset.index })
                .then(data => {
                    alert(data.success ? 'Host entry disabled' : 'Error: ' + (data.error || 'Failed'));
                    if (data.success) hostsReload();
                })
                .catch(error => alert('Error: ' + error.message));
        });
    });
})();
</script>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>

