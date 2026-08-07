<div class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>
                <div class="navbar-title">
                    <h6 class="fw-semibold mb-0"><?= e($title ?? t('app.name')) ?></h6>
                    <small class="text-muted"><?= e($subTitle ?? t('app.tagline')) ?></small>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Theme Toggle -->
                <button type="button" data-theme-toggle class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" aria-label="light"></button>

                <!-- Language Dropdown -->
                <div class="dropdown">
                    <button class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="ri:global-line" class="icon text-xl"></iconify-icon>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-0"><?= e(t('common.language')) ?></h6>
                            </div>
                        </div>
                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-8">
                            <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="english">
                                    <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="text-md fw-semibold mb-0">English</span>
                                    </span>
                                </label>
                                <a class="btn btn-sm btn-outline-primary" href="?locale=en"><?= e(t('common.english')) ?></a>
                            </div>
                            <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="arabic">
                                    <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                        <span class="text-md fw-semibold mb-0">العربية</span>
                                    </span>
                                </label>
                                <a class="btn btn-sm btn-outline-primary" href="?locale=ar"><?= e(t('common.arabic')) ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
                        <img src="assets/images/user.png" alt="user" class="w-40-px h-40-px object-fit-cover rounded-circle">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="https://<?= e(APP_COMPANY_URL) ?>" target="_blank"><iconify-icon icon="ri:external-link-line" class="me-2"></iconify-icon> <?= e(APP_COMPANY_URL) ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>