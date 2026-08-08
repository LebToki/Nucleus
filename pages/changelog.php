<?php
/**
 * Nucleus - Changelog & Version History Page
 * Version: 1.0.3
 * Shows running vs. repo version (online comparison) and lists every release
 * note from CHANGELOG.md in an accordion.
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

// Update machinery (only when a GitHub check is possible)
$updateStatus = null;
$updateManagerAvailable = class_exists('UpdateManager') || (file_exists(__DIR__ . '/../includes/UpdateManager.php') && (require_once __DIR__ . '/../includes/UpdateManager.php') === true);
if (!class_exists('UpdateManager')) {
    $updateManagerAvailable = false;
} else {
    $updateManagerAvailable = true;
}

if ($updateManagerAvailable) {
    try {
        $updateStatus = (new UpdateManager())->checkForUpdates();
        if (function_exists('refreshLatestVersionCache') && !empty($updateStatus['latest_version'])) {
            refreshLatestVersionCache($updateStatus);
        }
    } catch (\Throwable $e) {
        $updateStatus = null;
    }
}

include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div class="d-flex align-items-center gap-3">
                <h6 class="fw-semibold mb-0">Version History &amp; Changelog</h6>
            </div>
            <ul class="d-flex align-items-center gap-3">
                <li>
                    <span class="badge bg-primary-600 align-items-center d-inline-flex gap-1">v<?php echo getAppVersion(); ?></span>
                </li>
                <li class="fw-medium">
                    <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Changelog</li>
            </ul>
        </div>

        <?php if (is_array($updateStatus)): ?>
        <!-- Repo Update Status Card -->
        <div class="card shadow-none border radius-12 mb-24">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 <?php echo !empty($updateStatus['available']) ? 'bg-warning-main' : 'bg-success-main'; ?>">
                            <iconify-icon icon="<?php echo !empty($updateStatus['available']) ? 'solar:download-bold' : 'solar:check-circle-bold'; ?>" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="mb-4">
                                Running v<?php echo htmlspecialchars((string)($updateStatus['current_version'] ?? getAppVersion())); ?>
                                <?php if (!empty($updateStatus['available'])): ?>
                                    <span class="badge bg-warning-600">Update available</span>
                                <?php else: ?>
                                    <span class="badge bg-success-100 text-success-600">Up to date</span>
                                <?php endif; ?>
                            </h6>
                            <p class="text-secondary-light text-sm mb-0">
                                Latest release: v<?php echo htmlspecialchars((string)($updateStatus['latest_version'] ?? '—')); ?>
                                <?php if (!empty($updateStatus['repo_behind'])): ?>
                                    <span class="text-success-main fw-medium">(local build is ahead)</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (function_exists('getAppVersion') && !empty($updateStatus['download_url'])): ?>
                            <button type="button" class="btn btn-primary-600"<?php echo empty($updateStatus['available']) ? ' disabled' : ''; ?>
                                    data-update-url="<?php echo htmlspecialchars((string)$updateStatus['download_url']); ?>"
                                    data-update-version="<?php echo htmlspecialchars((string)($updateStatus['version'] ?? '')); ?>">
                                <iconify-icon icon="solar:download-bold" class="icon"></iconify-icon>
                                Update to v<?php echo htmlspecialchars((string)($updateStatus['version'] ?? $updateStatus['latest_version'])); ?>
                            </button>
                        <?php endif; ?>
                        <a href="https://github.com/LebToki/Nucleus/releases" target="_blank" rel="noopener" class="btn btn-neutral-100 text-secondary-light d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:link-bold" class="icon"></iconify-icon>
                            View GitHub
                        </a>
                    </div>
                </div>

                <?php if (!empty($updateStatus['available']) && !empty($updateStatus['body'])): ?>
                <div class="mt-16 p-16 radius-8 bg-neutral-50">
                    <p class="fw-medium text-sm mb-4">What's new in v<?php echo htmlspecialchars((string)$updateStatus['latest_version']); ?>:</p>
                    <div class="text-secondary-light text-sm mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars(substr($updateStatus['body'], 0, 2000)); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card shadow-none border radius-12">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16">
                    <strong><p class="fw-semibold mb-0">Release Notes</p></strong>
                    <span class="text-secondary-light text-sm">Parsed from CHANGELOG.md</span>
                </div>

                <div class="accordion" id="changelogAccordion">
                    <?php
                    $changelog = getChangelog();
                    $changelog = array_slice($changelog, 0, 5);
                    if (empty($changelog)): ?>
                        <p class="text-secondary-light mb-0">No changelog entries found.</p>
                    <?php else:
                        $first = true;
                        foreach ($changelog as $version => $data):
                            $collapseId = 'collapse' . preg_replace('/[^a-zA-Z0-9]+/', '', $version);
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $collapseId; ?>">
                            <button class="accordion-button <?php echo $first ? '' : 'collapsed'; ?> fw-medium py-12 px-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="<?php echo $first ? 'true' : 'false'; ?>" aria-controls="<?php echo $collapseId; ?>">
                                Version <?php echo htmlspecialchars($version); ?>
                                <span class="text-secondary-light ms-8 text-sm fw-normal"><?php echo htmlspecialchars($data['date'] ?? ''); ?></span>
                            </button>
                        </h2>
                        <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $first ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $collapseId; ?>" data-bs-parent="#changelogAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                    <?php foreach ((array)($data['changes'] ?? []) as $change): ?>
                                    <li class="d-flex align-items-start gap-2 text-secondary-light text-sm">
                                        <iconify-icon icon="solar:check-circle-bold" class="text-success-main mt-1 flex-shrink-0"></iconify-icon>
                                        <span><?php echo htmlspecialchars($change); ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                        $first = false;
                        endforeach;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.querySelector('[data-update-url]');
    if (btn && !btn.disabled && typeof window.installUpdate === 'function') {
        btn.addEventListener('click', function() {
            window.installUpdate(this.getAttribute('data-update-url'), this.getAttribute('data-update-version'));
        });
    }
});
</script>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>