<?php
/**
 * Nucleus - Mailbox Page (Mailpit Integration)
 * Version: 3.0.1 (UX Overhaul)
 * Description: Email management using Mailpit API, modernized UI/UX.
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

// Mailpit API configuration
$mailpitApiUrl = 'http://localhost:8025/api/v1';
$mailpitWebUrl = 'http://localhost:8025';

/**
 * Checks if the local Mailpit service is running by attempting to fetch a message list.
 * @return bool True if connection succeeds and HTTP code is 200.
 */
function checkMailpitRunning() {
    global $mailpitApiUrl;
    $ch = @curl_init($mailpitApiUrl . '/messages?limit=1');
    if (!$ch) return false;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    @curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode === 200;
}

/**
 * Checks the system Postfix status.
 * @return bool True if Postfix is active.
 */
function checkPostfixRunning() {
    // Uses shell_exec to run 'systemctl is-active postfix' on Linux systems.
    $status = @shell_exec('systemctl is-active postfix 2>/dev/null');
    return trim((string)$status) === 'active';
}

$mailpitRunning = checkMailpitRunning();
$postfixRunning = checkPostfixRunning();

// Get current folder and email ID from query parameters
$currentFolder = $_GET['folder'] ?? 'inbox';
$emailId = $_GET['id'] ?? null;
$view = $_GET['view'] ?? 'list'; // 'list' or 'detail'

// Load translations (Assuming load_translations is a defined helper function)
$mailboxTranslations = [];
if (function_exists('load_translations')) {
    $mailboxTranslations = load_translations('mailbox');
}

/**
 * Helper function to retrieve translated strings for the Mailbox module.
 */
function t_mailbox($key, $fallback = '') {
    global $mailboxTranslations;
    // Attempt translation using global 't' function first
    if (function_exists('t')) {
        $translated = t('mailbox.' . $key);
        if ($translated !== 'mailbox.' . $key) {
            return $translated;
        }
    }
    // Fallback to loaded translations or the key itself
    return $mailboxTranslations[$key] ?? ($fallback ?: $key);
}

include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid pt-3 pb-4">
        <!-- Page Title and Navigation Breadcrumbs -->
        <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5 border-bottom pb-3">
            <h6 class="fw-semibold mb-0 breadcrumb-title"><?php echo t_mailbox('mailbox', 'Mailbox'); ?></h6>
            <ul class="d-flex align-items-center gap-2 text-secondary-light">
                <!-- Dashboard Link (Placeholder structure retained) -->
                <li>
                    <a href="index.php" class="d-flex align-items-center gap-1 text-decoration-none hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        <?php echo t_mailbox('dashboard', 'Dashboard'); ?>
                    </a>
                </li>
                <li class="me-2">/</li>
                <!-- Current Page Name -->
                <li class="fw-medium"><?php echo t_mailbox('mailbox', 'Mailbox'); ?></li>
            </ul>
        </header>

        <!-- System Mail & Postfix / Mailpit Service Banner (Enhanced visual grouping) -->
        <div class="card p-4 radius-12 mb-5 shadow-sm">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-4">
                    <!-- Icon container -->
                    <div class="w-12 h-auto bg-primary-50 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                        <iconify-icon icon="solar:letter-bold" class="text-primary-600 text-xl"></iconify-icon>
                    </div >
                    <!-- Info container -->
                    <div class="flex-grow-1 min-width-0">
                        <h6 class="text-md fw-semibold mb-3">Linux Mail Services Status</h6>
                        <small class="text-secondary-light text-sm d-block mb-4">Monitoring Postfix and Mailpit to ensure reliable email capture.</small>
                        <div class="d-flex align-items-center gap-4 mt-2 flex-wrap">
                            <!-- Postfix Status -->
                            <div>
                                <span class="me-3 fw-medium text-secondary-light">Postfix Daemon:</span> 
                                <?php if ($postfixRunning): ?>
                                    <span class="badge bg-success-main text-white px-4 py-1 rounded-pill shadow-sm">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-400 text-white px-4 py-1 rounded-pill shadow-sm">INACTIVE</span>
                                <?php endif; ?>
                            </div>
                            <!-- Mailpit Status -->
                            <div>
                                <span class="me-3 fw-medium text-secondary-light">Mailpit Web UI:</span> 
                                <?php if ($mailpitRunning): ?>
                                    <span class="badge bg-success-main text-white px-4 py-1 rounded-pill shadow-sm">CONNECTED</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-main text-white px-4 py-1 rounded-pill shadow-sm">OFFLINE (Port 8025)</span>
                                <?php endif; ?>
                            </div>
                        </div >
                    </div>
                </div >
                <!-- Action Button -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <?php if ($mailpitRunning): ?>
                        <a href="<?php echo htmlspecialchars($mailpitWebUrl); ?>" target="_blank" class="btn btn-primary-600 rounded-pill shadow-sm">
                            <iconify-icon icon="solar:link-bold" class="icon"></iconify-icon>
                            Open Mailpit UI
                        </a>
                    <?php endif; ?>
                </div>
            </div >
        </div>

        <!-- Main Content Grid (Sidebar and List/Detail) -->
        <div class="row g-3">
            
            <!-- Sidebar: Folder Navigation (Fixed width, sticky top behavior) -->
            <div class="col-lg-3 mail-sidebar">
                <div class="card p-0 radius-12 shadow-sm sticky-top" style="top: 1.5rem;">
                    <div class="card-body p-0 mail-folder-list-container">
                        <!-- Compose Button (Top) -->
                        <div class="p-3 border-bottom">
                            <button type="button" class="btn btn-primary-600 w-100 radius-8 px-20 py-11 d-flex align-items-center gap-2 mb-3 shadow-sm" id="compose-btn">
                                <iconify-icon icon="solar:pen-bold" class="text-xl"></iconify-icon>
                                <?php echo t_mailbox('compose', 'Compose'); ?>
                            </button>
                        </div>

                        <!-- Folders -->
                        <div class="p-3 mail-folder-list">
                            <ul class="list-unstyled mb-0" id="folder-list">
                                <li data-folder-name="inbox" class="mail-folder-item <?php echo $currentFolder === 'inbox' ? 'active bg-primary-50 text-primary-600 border-start border-3 border-primary-600 ps-2' : ''; ?>">
                                    <a href="index.php?page=mailbox&folder=inbox" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100">
                                        <iconify-icon icon="solar:inbox-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('inbox', 'Inbox'); ?></span>
                                        <span class="ms-auto badge mail-count" id="inbox-count">0</span>
                                    </a>
                                </li>
                                <li data-folder-name="mailpit_web" class="mail-folder-item">
                                    <a href="<?php echo htmlspecialchars($mailpitWebUrl); ?>" target="_blank" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 text-primary-600 mailpit-link">
                                        <iconify-icon icon="solar:external-link-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('open_mailpit_web', 'Open Mailpit Web'); ?></span>
                                    </a>
                                </li>
                                <li data-folder-name="starred" class="mail-folder-item <?php echo $currentFolder === 'starred' ? 'active bg-primary-50 text-primary-600 border-start border-3 border-primary-600 ps-2' : ''; ?>">
                                    <a href="index.php?page=mailbox&folder=starred" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100">
                                        <iconify-icon icon="solar:star-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('starred', 'Starred'); ?></span>
                                        <span class="ms-auto badge mail-count" id="starred-count">0</span>
                                    </a>
                                </li>
                                <li data-folder-name="sent" class="mail-folder-item <?php echo $currentFolder === 'sent' ? 'active bg-primary-50 text-primary-600 border-start border-3 border-primary-600 ps-2' : ''; ?>">
                                    <a href="index.php?page=mailbox&folder=sent" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100">
                                        <iconify-icon icon="solar:letter-opened-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('sent', 'Sent'); ?></span>
                                        <span class="ms-auto badge mail-count" id="sent-count">0</span>
                                    </a>
                                </li>
                                <li data-folder-name="draft" class="mail-folder-item <?php echo $currentFolder === 'draft' ? 'active bg-primary-50 text-primary-600 border-start border-3 border-primary-600 ps-2' : ''; ?>">
                                    <a href="index.php?page=mailbox&folder=draft" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100">
                                        <iconify-icon icon="solar:file-text-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('draft', 'Draft'); ?></span>
                                        <span class="ms-auto badge mail-count" id="draft-count">0</span>
                                    </a>
                                </li>
                                <li data-folder-name="spam" class="mail-folder-item <?php echo $currentFolder === 'spam' ? 'active bg-primary-50 text-primary-600 border-start border-3 border-primary-600 ps-2' : ''; ?>">
                                    <a href="index.php?page=mailbox&folder=spam" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100">
                                        <iconify-icon icon="solar:shield-warning-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('spam', 'Spam'); ?></span>
                                        <span class="ms-auto badge mail-count" id="spam-count">0</span>
                                    </a>
                                </li>
                                <li data-folder-name="bin" class="mail-folder-item <?php echo $currentFolder === 'bin' ? 'active bg-primary-50 text-primary-600 border-start border-3 border-primary-600 ps-2' : ''; ?>">
                                    <a href="index.php?page=mailbox&folder=bin" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100">
                                        <iconify-icon icon="solar:trash-bin-trash-bold" class="icon"></iconify-icon>
                                        <span><?php echo t_mailbox('bin', 'Bin'); ?></span>
                                        <span class="ms-auto badge mail-count" id="bin-count">0</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div >
                </div >
            </div >

            <!-- Main Content Area (List/Detail) -->
            <div class="col-lg-9">
                <?php if ($view === 'detail' && $emailId): ?>
                    <!-- Email Detail View Container -->
                    <div class="card p-0 radius-12 shadow-sm mb-4">
                        <div class="card-body p-4 lg:p-6" id="email-detail-view">
                            <!-- Loading Skeleton -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary-600" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-secondary-light">Loading email content...</p>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Email List View Container -->
                    <div class="card p-0 radius-12 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <!-- Filters Header (Paddings adjusted) -->
                            <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3 bg-light rounded-top">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <select class="form-select form-select-sm" id="filter-select" style="min-width: 150px;">
                                        <option value="all"><?php echo t_mailbox('all', 'All'); ?></option>
                                        <option value="none"><?php echo t_mailbox('none', 'None'); ?></option>
                                        <option value="read"><?php echo t_mailbox('read', 'Read'); ?></option>
                                        <option value="unread"><?php echo t_mailbox('unread', 'Unread'); ?></option>
                                        <option value="starred"><?php echo t_mailbox('starred', 'Starred'); ?></option>
                                        <option value="unstarred"><?php echo t_mailbox('unstarred', 'Unstarred'); ?></option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-primary-100 text-primary-600 border">
                                        <?php echo t_mailbox('mark_all_read', 'Mark all as read'); ?>
                                    </button>
                                </div >
                                <div class="text-secondary-light text-sm">
                                    <span id="email-range">0-0</span> of <span id="total-emails">0</span>
                                </div>
                            </div>

                            <!-- Email List Container -->
                            <div id="email-list" class="list-group list-group-flush mail-list-container">
                                <!-- Placeholder for emails. JS will populate this area. -->
                                <div class="text-center py-5 text-muted">
                                    No emails found in this folder yet, or please wait while the client loads data.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div >
        </div>
    </div>
</div >

<!-- Compose Modal (Structure preserved) -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold"><?php echo t_mailbox('compose', 'Compose'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div >
            <div class="modal-body">
                <form id="compose-form">
                    <!-- Fields with modern spacing -->
                    <div class="mb-3">
                        <label class="form-label fw-medium required"><?php echo t_mailbox('to', 'To'); ?></label>
                        <input type="email" class="form-control" id="compose-to" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?php echo t_mailbox('subject', 'Subject'); ?></label>
                        <input type="text" class="form-control" id="compose-subject">
                    </div>
                    <!-- Message area -->
                    <div class="mb-3">
                        <label class="form-label fw-medium"><?php echo t_mailbox('message', 'Message'); ?></label>
                        <textarea class="form-control resize-y" id="compose-message" rows="10"></textarea>
                    </div>
                </form>
            </div >
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo t_mailbox('cancel', 'Cancel'); ?></button>
                <button type="button" class="btn btn-primary-600" id="send-email">
                    <iconify-icon icon="solar:letter-opened-bold" class="text-xl"></iconify-icon>
                    <?php echo t_mailbox('send', 'Send'); ?>
                </button>
            </div>
        </div >
    </div >
</div >

<?php
// Store script for later inclusion (Keeping this mechanism intact)
$GLOBALS['mailboxScript'] = true;
$GLOBALS['mailpitApiUrl'] = $mailpitApiUrl;
$GLOBALS['currentFolder'] = $currentFolder;
$GLOBALS['emailId'] = $emailId;
$GLOBALS['view'] = $view;
?>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>