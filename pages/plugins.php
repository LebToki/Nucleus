<?php
/**
 * Nucleus - Plugins Page
 * Manage installable plugins (Mailpit, etc.)
 */

if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
}
if (file_exists(__DIR__ . '/../includes/helpers.php')) {
    require_once __DIR__ . '/../includes/helpers.php';
}
if (file_exists(__DIR__ . '/../includes/i18n.php')) {
    require_once __DIR__ . '/../includes/i18n.php';
}

$available = \Nucleus\Core\PluginManager::getAvailablePlugins();
$installed = \Nucleus\Core\PluginManager::getInstalledPlugins();

include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Plugins</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Plugins</li>
            </ul>
        </div>

        <div class="row g-3" id="plugins-grid">
            <?php foreach ($available as $key => $plugin):
                $isInstalled = isset($installed[$key]);
                $isRunning = $isInstalled && ($installed[$key]['running'] ?? false);
                $isWebapp = ($plugin['type'] ?? 'binary') === 'webapp';
                $gradientVariant = (($key === 'mailpit') ? 5 : 1);
            ?>
            <div class="col-lg-4 col-md-6" id="plugin-card-<?php echo htmlspecialchars($key); ?>">
                <div class="card shadow-none border radius-12 h-100">
                    <div class="card-body p-24">
                        <!-- Header: Icon + Status -->
                        <div class="d-flex align-items-start justify-content-between mb-16">
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-48-px h-48-px bg-<?php echo $plugin['color']; ?>-100 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                    <iconify-icon icon="<?php echo htmlspecialchars($plugin['icon']); ?>" class="text-<?php echo $plugin['color']; ?>-main text-2xl"></iconify-icon>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($plugin['name']); ?></h6>
                                    <small class="text-secondary-light">by <?php echo htmlspecialchars($plugin['author']); ?></small>
                                </div>
                            </div>
                            <?php if ($isInstalled): ?>
                                <?php if ($isWebapp): ?>
                                    <span class="badge bg-success-focus text-success-main px-12 py-4 rounded-pill">Installed</span>
                                <?php else: ?>
                                    <span class="badge bg-<?php echo $isRunning ? 'success' : 'secondary'; ?>-focus text-<?php echo $isRunning ? 'success' : 'secondary'; ?>-main px-12 py-4 rounded-pill">
                                        <?php echo $isRunning ? 'Running' : 'Stopped'; ?>
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-neutral-200 text-secondary-light px-12 py-4 rounded-pill">Not Installed</span>
                            <?php endif; ?>
                        </div>

                        <!-- Description -->
                        <p class="text-secondary-light mb-16"><?php echo htmlspecialchars($plugin['description']); ?></p>

                        <!-- Details -->
                        <div class="d-flex flex-wrap gap-2 mb-16">
                            <?php if (!$isWebapp): ?>
                                <?php foreach ($plugin['ports'] as $portName => $portNum): ?>
                                    <span class="badge bg-neutral-100 text-secondary-light text-sm px-8 py-4">
                                        <?php echo htmlspecialchars(ucfirst($portName)); ?>: <?php echo $portNum; ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <span class="badge bg-neutral-100 text-secondary-light text-sm px-8 py-4">
                                <?php echo htmlspecialchars($plugin['install_size']); ?>
                            </span>
                        </div>

                        <?php if ($isInstalled): ?>
                            <!-- Installed: Show controls -->
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!$isWebapp): ?>
                                    <?php if ($isRunning): ?>
                                        <button type="button" class="btn btn-sm btn-warning-100 text-warning-600 d-flex align-items-center gap-1"
                                                onclick="pluginAction('<?php echo $key; ?>', 'stop')">
                                            <iconify-icon icon="solar:stop-bold"></iconify-icon>
                                            Stop
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-success-100 text-success-600 d-flex align-items-center gap-1"
                                                onclick="pluginAction('<?php echo $key; ?>', 'start')">
                                            <iconify-icon icon="solar:play-bold"></iconify-icon>
                                            Start
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($isWebapp): ?>
                                    <a href="<?php echo htmlspecialchars($installed[$key]['web_url'] ?? '/' . ($plugin['install_dir'] ?? $key) . '/'); ?>" target="_blank"
                                       class="btn btn-sm btn-primary-100 text-primary-600 d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:link-bold"></iconify-icon>
                                        Open
                                    </a>
                                <?php elseif (isset($plugin['ports']['web'])): ?>
                                    <a href="http://localhost:<?php echo $plugin['ports']['web']; ?>" target="_blank"
                                       class="btn btn-sm btn-primary-100 text-primary-600 d-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:link-bold"></iconify-icon>
                                        Open Web UI
                                    </a>
                                <?php endif; ?>

                                <button type="button" class="btn btn-sm btn-danger-100 text-danger-600 d-flex align-items-center gap-1 ms-auto"
                                        onclick="pluginAction('<?php echo $key; ?>', 'uninstall')">
                                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                    Uninstall
                                </button>
                            </div>

                            <?php if (!$isWebapp && $isRunning && isset($plugin['ports']['web'])): ?>
                                <div class="mt-12 p-8 bg-success-50 rounded-8">
                                    <small class="text-success-main">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                        SMTP: localhost:<?php echo $plugin['ports']['smtp']; ?> | Web: <a href="http://localhost:<?php echo $plugin['ports']['web']; ?>" target="_blank">localhost:<?php echo $plugin['ports']['web']; ?></a>
                                    </small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Not installed: Show install button -->
                            <button type="button" class="btn btn-sm btn-primary-600 d-flex align-items-center gap-2"
                                    onclick="pluginAction('<?php echo $key; ?>', 'install')" id="install-btn-<?php echo $key; ?>">
                                <iconify-icon icon="solar:download-bold"></iconify-icon>
                                Install <?php echo htmlspecialchars($plugin['name']); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($available)): ?>
            <div class="card shadow-none border radius-12">
                <div class="card-body p-24 text-center">
                    <iconify-icon icon="solar:box-bold" class="text-secondary-light text-5xl mb-16"></iconify-icon>
                    <p class="text-secondary-light mb-0">No plugins available yet.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function pluginAction(pluginKey, action) {
    const labels = {
        install: 'Installing',
        uninstall: 'Uninstalling',
        start: 'Starting',
        stop: 'Stopping'
    };

    if (action === 'uninstall') {
        if (!confirm('Uninstall this plugin? The binary and service will be removed.')) return;
    }

    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<iconify-icon icon="solar:restart-bold" class="spin"></iconify-icon> ' + (labels[action] || 'Working') + '...';

    const formData = new FormData();
    formData.append('action', action);
    formData.append('plugin', pluginKey);
    formData.append('csrf_token', window.csrfToken);

    fetch('api/plugins.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            if (typeof showNotification === 'function') {
                showNotification(data.message || 'Success', 'success');
            }
            setTimeout(() => window.location.reload(), 1000);
        } else {
            btn.innerHTML = originalHtml;
            if (typeof showNotification === 'function') {
                showNotification(data.error || 'Action failed', 'error');
            } else {
                alert(data.error || 'Action failed');
            }
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert('Error: ' + err.message);
    });
}
</script>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>
