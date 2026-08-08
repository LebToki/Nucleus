<?php
/**
 * Nucleus - Plugins Page
 * Manage installable service nodes (Mailpit, etc.)
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
                $needsSudo = !$isWebapp && \Nucleus\Core\PluginManager::needsElevation($plugin);
                $installScope = $installed[$key]['scope'] ?? ($installed[$key]['running_scope'] ?? '');
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

                        <?php if ($isInstalled && !$isWebapp && !empty($installScope)): ?>
                            <div class="mb-12">
                                <span class="badge bg-info-100 text-info-600 text-sm px-8 py-4">
                                    <iconify-icon icon="solar:server-square-bold" class="icon"></iconify-icon>
                                    Node scope: <?php echo $installScope === 'system' ? 'System service' : ($installScope === 'user' ? 'User service' : ($installScope === 'detected' ? 'Detected (running)' : htmlspecialchars($installScope))); ?>
                                </span>
                            </div>
                        <?php endif; ?>

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
                            <?php if ($needsSudo): ?>
                                <small class="d-block mt-8 text-warning-600 text-xs d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:lock-keyhole-bold"></iconify-icon>
                                    Installing this node requires root access (sudo password)
                                </small>
                            <?php endif; ?>
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

<!-- Root access (sudo) modal for elevated node operations -->
<div class="modal fade" id="sudoModal" tabindex="-1" aria-labelledby="sudoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="sudoModalLabel">
                    <iconify-icon icon="solar:lock-keyhole-bold" class="text-warning-600 me-2"></iconify-icon>
                    Root access required
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <p class="text-secondary-light text-sm mb-16" id="sudo-modal-desc">
                    Installing this node registers a system service and binary under /usr/local/bin.
                    Enter your sudo (root) password to continue.
                </p>
                <div class="mb-16">
                    <label for="sudo-password" class="form-label fw-medium mb-8">Sudo password</label>
                    <div class="position-relative">
                        <input type="password" class="form-control pe-48" id="sudo-password" placeholder="••••••••" autocomplete="current-password">
                        <button type="button" class="btn btn-sm position-absolute top-50 translate-middle-y end-0 me-8 text-secondary-light"
                                id="sudo-password-toggle" tabindex="-1">
                            <iconify-icon icon="solar:eye-bold"></iconify-icon>
                        </button>
                    </div>
                    <small class="text-secondary-light text-xs mt-4 d-block">
                        Used only to run the install commands for this operation. It is never stored, logged, or reused.
                    </small>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-600" id="sudo-confirm-btn">
                    <iconify-icon icon="solar:lock-keyhole-unlocked-bold" class="icon"></iconify-icon>
                    Authenticate &amp; continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let pendingPluginAction = null;

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

    function send(sudoPassword) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('plugin', pluginKey);
        formData.append('csrf_token', window.csrfToken);
        if (sudoPassword) {
            formData.append('sudo_password', sudoPassword);
        }

        return fetch('api/plugins.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.needs_sudo) {
                // Elevation required — ask for the root password and retry
                pendingPluginAction = { send: send, btn: btn, originalHtml: originalHtml };
                document.getElementById('sudo-modal-desc').textContent =
                    data.error || 'Installing this node requires root privileges.';
                document.getElementById('sudo-password').value = '';
                new bootstrap.Modal(document.getElementById('sudoModal')).show();
                return null;
            }
            if (data.success) {
                if (typeof showNotification === 'function') {
                    showNotification(data.message || 'Success', 'success');
                }
                setTimeout(() => window.location.reload(), 1000);
            } else {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (typeof showNotification === 'function') {
                    showNotification(data.error || 'Action failed', 'error');
                } else {
                    alert(data.error || 'Action failed');
                }
            }
            return null;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert('Error: ' + err.message);
        });
    }

    send(null);
}

document.addEventListener('DOMContentLoaded', function() {
    const sudoModalEl = document.getElementById('sudoModal');
    const confirmBtn = document.getElementById('sudo-confirm-btn');
    const pwInput = document.getElementById('sudo-password');

    confirmBtn.addEventListener('click', function() {
        const password = pwInput.value;
        if (!password) {
            pwInput.classList.add('is-invalid');
            return;
        }
        if (!pendingPluginAction) return;

        const btn = pendingPluginAction.btn;
        btn.disabled = true;
        btn.innerHTML = '<iconify-icon icon="solar:restart-bold" class="spin"></iconify-icon> Installing with root...';

        this.disabled = true;
        this.innerHTML = '<iconify-icon icon="solar:restart-bold" class="spin"></iconify-icon> Verifying...';

        const action = pendingPluginAction;
        pendingPluginAction = null;

        bootstrap.Modal.getInstance(sudoModalEl).hide();
        action.send(password).finally(() => {
            this.disabled = false;
            this.innerHTML = '<iconify-icon icon="solar:lock-keyhole-unlocked-bold" class="icon"></iconify-icon> Authenticate &amp; continue';
        });
    });

    sudoModalEl.addEventListener('shown.bs.modal', function() {
        pwInput.focus();
    });
    sudoModalEl.addEventListener('hidden.bs.modal', function() {
        pwInput.classList.remove('is-invalid');
        if (pendingPluginAction) {
            pendingPluginAction.btn.disabled = false;
            pendingPluginAction.btn.innerHTML = pendingPluginAction.originalHtml;
            pendingPluginAction = null;
        }
    });

    document.getElementById('sudo-password-toggle').addEventListener('click', function() {
        const isPw = pwInput.type === 'password';
        pwInput.type = isPw ? 'text' : 'password';
    });
});
</script>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>
