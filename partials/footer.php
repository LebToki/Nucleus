<?php
// Running version + (cached) latest repo version shown in the footer.
$footerVersion = function_exists('getAppVersion') ? getAppVersion() : (defined('APP_VERSION') ? APP_VERSION : '1.0.0');
$footerLatest = function_exists('getCachedLatestVersion') ? getCachedLatestVersion() : null;
$footerReleaseUrl = defined('APP_GITHUB') && strpos(APP_GITHUB, 'github.com/') !== false
    ? rtrim(APP_GITHUB, '/') . '/releases'
    : 'https://github.com/LebToki/Nucleus/releases';
?>
<footer class="d-footer">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <p class="mb-0 d-inline-flex align-items-center gap-2 flex-wrap">
                © <?php echo date('Y'); ?> Nucleus by Tarek Tarabichi
                <span class="badge bg-primary-600">v<?php echo htmlspecialchars((string)$footerVersion); ?></span>
                <?php if (!empty($footerLatest['latest_version'])): ?>
                    <a href="<?php echo htmlspecialchars((string)$footerReleaseUrl); ?>" target="_blank" rel="noopener"
                       class="text-secondary-light hover-text-primary d-inline-flex align-items-center gap-1"
                       title="<?php echo $footerLatest['available'] ? (function_exists('t') ? t('dashboard.update_available', 'Update available') : 'Update available') : (function_exists('t') ? t('dashboard.up_to_date', 'Up to date') : 'Up to date'); ?> — latest release on GitHub">
                        <iconify-icon icon="solar:download-bold" class="text-sm"></iconify-icon>
                        <?php if ($footerLatest['available']): ?>
                            <?php echo function_exists('t') ? t('dashboard.update_available', 'Update available') : 'Update available'; ?>
                        <?php else: ?>
                            <?php echo function_exists('t') ? t('dashboard.up_to_date', 'Up to date') : 'Up to date'; ?>
                        <?php endif; ?>
                        v<?php echo htmlspecialchars((string)$footerLatest['latest_version']); ?>
                    </a>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-auto">
            <p class="mb-0">Made with ❤️ by <a href="https://2tinteractive.com" target="_blank" class="text-primary-600 hover-text-primary">2TInteractive</a></p>
        </div>
    </div>
</footer>

<script>
// Monochrome Mode Toggle (loaded in footer.php)
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    function initMonochromeToggle() {
        const monochromeBtn = document.querySelector('[data-theme-toggle="monochrome"]');
        const isMonochrome = localStorage.getItem('monochromeMode') === 'true';
        
        // Apply monochrome mode on page load if it was active
        if (isMonochrome) {
            document.body.classList.add('monochrome-mode');
        }
        
        // Add click handler for monochrome button
        if (monochromeBtn) {
            monochromeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const isCurrentlyMonochrome = document.body.classList.contains('monochrome-mode');
                
                if (isCurrentlyMonochrome) {
                    // Turn off monochrome
                    document.body.classList.remove('monochrome-mode');
                    localStorage.setItem('monochromeMode', 'false');
                    // Update theme to current light/dark (not monochrome)
                    const lightDarkTheme = localStorage.getItem('lightDarkTheme') || localStorage.getItem('theme') || 'light';
                    if (lightDarkTheme !== 'monochrome') {
                        localStorage.setItem('theme', lightDarkTheme);
                    }
                } else {
                    // Turn on monochrome
                    document.body.classList.add('monochrome-mode');
                    localStorage.setItem('monochromeMode', 'true');
                    // Store current theme before switching to monochrome
                    const currentTheme = localStorage.getItem('theme') || 'light';
                    if (currentTheme !== 'monochrome') {
                        localStorage.setItem('lightDarkTheme', currentTheme);
                    }
                    localStorage.setItem('theme', 'monochrome');
                }
                
                // Update button state
                updateMonochromeButton();
                
                // Trigger a custom event so scripts.php can update other button states
                window.dispatchEvent(new CustomEvent('monochromeModeChanged', {
                    detail: { isMonochrome: !isCurrentlyMonochrome }
                }));
            });
        }
    }
    
    // Update monochrome button visual state
    function updateMonochromeButton() {
        const monochromeBtn = document.querySelector('[data-theme-toggle="monochrome"]');
        const isMonochrome = document.body.classList.contains('monochrome-mode');
        
        if (monochromeBtn) {
            if (isMonochrome) {
                monochromeBtn.classList.add('active');
                monochromeBtn.classList.add('bg-primary-600');
                monochromeBtn.classList.remove('bg-neutral-200');
            } else {
                monochromeBtn.classList.remove('active');
                monochromeBtn.classList.remove('bg-primary-600');
                monochromeBtn.classList.add('bg-neutral-200');
            }
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initMonochromeToggle();
            updateMonochromeButton();
        });
    } else {
        initMonochromeToggle();
        updateMonochromeButton();
    }
})();
</script>