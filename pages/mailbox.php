<?php
/**
 * Laragon Dashboard - Mailbox Page (Mailpit Integration)
 * Version: 3.0.0
 * Description: Email management using Mailpit API, styled like WowDash
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

// Check if Mailpit is running
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

// Check Postfix status
function checkPostfixRunning() {
    $status = @shell_exec('systemctl is-active postfix 2>/dev/null');
    return trim((string)$status) === 'active';
}

$mailpitRunning = checkMailpitRunning();
$postfixRunning = checkPostfixRunning();

// Get current folder and email ID
$currentFolder = $_GET['folder'] ?? 'inbox';
$emailId = $_GET['id'] ?? null;
$view = $_GET['view'] ?? 'list'; // 'list' or 'detail'

// Load translations
$mailboxTranslations = [];
if (function_exists('load_translations')) {
    $mailboxTranslations = load_translations('mailbox');
}

function t_mailbox($key, $fallback = '') {
    global $mailboxTranslations;
    if (function_exists('t')) {
        $translated = t('mailbox.' . $key);
        if ($translated !== 'mailbox.' . $key) {
            return $translated;
        }
    }
    return $mailboxTranslations[$key] ?? ($fallback ?: $key);
}

include __DIR__ . '/../partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0"><?php echo t_mailbox('mailbox', 'Mailbox'); ?></h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        <?php echo t_mailbox('dashboard', 'Dashboard'); ?>
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium"><?php echo t_mailbox('mailbox', 'Mailbox'); ?></li>
            </ul>
        </div>

        <!-- System Mail & Postfix / Mailpit Service Banner -->
        <div class="card p-16 radius-12 mb-24">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-40-px h-40-px bg-primary-50 rounded-circle d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:letter-bold" class="text-primary-600 text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="text-md fw-semibold mb-2">Linux Mail Services (Postfix & Mailpit)</h6>
                        <div class="d-flex align-items-center gap-3 text-sm">
                            <span>
                                Postfix Daemon: 
                                <?php if ($postfixRunning): ?>
                                    <span class="badge bg-success-main text-white px-8 py-2">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-400 text-white px-8 py-2">INACTIVE</span>
                                <?php endif; ?>
                            </span>
                            <span>
                                Mailpit Web UI: 
                                <?php if ($mailpitRunning): ?>
                                    <span class="badge bg-success-main text-white px-8 py-2">CONNECTED</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-main text-white px-8 py-2">OFFLINE (Port 8025)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($mailpitRunning): ?>
                        <a href="<?php echo htmlspecialchars($mailpitWebUrl); ?>" target="_blank" class="btn btn-sm btn-primary-600">
                            <iconify-icon icon="solar:link-bold" class="icon"></iconify-icon>
                            Open Mailpit UI
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!$mailpitRunning && !$postfixRunning): ?>
            <div class="card p-0 radius-12">
                <div class="card-body p-24 text-center">
                    <iconify-icon icon="solar:letter-bold" class="text-secondary-light text-5xl mb-16"></iconify-icon>
                    <h6 class="mb-8">No Active Local Mail Service</h6>
                    <p class="text-secondary-light mb-16">Start Postfix service (`sudo systemctl start postfix`) or run Mailpit / Mailcatcher to capture outgoing development emails.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="card p-0 radius-12">
                        <div class="card-body p-0">
                            <!-- Compose Button -->
                            <div class="p-16 border-bottom">
                        <button type="button" class="btn btn-primary-600 w-100 radius-8 px-20 py-11 d-flex align-items-center gap-2" id="compose-btn">
                            <iconify-icon icon="solar:pen-bold" class="text-xl"></iconify-icon>
                            <?php echo t_mailbox('compose', 'Compose'); ?>
                        </button>
                            </div>

                            <!-- Folders -->
                            <div class="p-16">
                                <ul class="list-unstyled mb-0" id="folder-list">
                                    <li>
                                        <a href="index.php?page=mailbox&folder=inbox" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 <?php echo $currentFolder === 'inbox' ? 'bg-primary-50 text-primary-600' : ''; ?>">
                                            <iconify-icon icon="solar:inbox-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('inbox', 'Inbox'); ?></span>
                                            <span class="ms-auto badge bg-primary-600" id="inbox-count">0</span>
                                        </a>
                                    </li>
                                    <li class="mt-8">
                                        <a href="<?php echo htmlspecialchars($mailpitWebUrl); ?>" target="_blank" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 text-primary-600">
                                            <iconify-icon icon="solar:external-link-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('open_mailpit_web', 'Open Mailpit Web'); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?page=mailbox&folder=starred" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 <?php echo $currentFolder === 'starred' ? 'bg-primary-50 text-primary-600' : ''; ?>">
                                            <iconify-icon icon="solar:star-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('starred', 'Starred'); ?></span>
                                            <span class="ms-auto badge bg-warning-main" id="starred-count">0</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?page=mailbox&folder=sent" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 <?php echo $currentFolder === 'sent' ? 'bg-primary-50 text-primary-600' : ''; ?>">
                                            <iconify-icon icon="solar:letter-opened-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('sent', 'Sent'); ?></span>
                                            <span class="ms-auto badge bg-success-main" id="sent-count">0</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?page=mailbox&folder=draft" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 <?php echo $currentFolder === 'draft' ? 'bg-primary-50 text-primary-600' : ''; ?>">
                                            <iconify-icon icon="solar:file-text-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('draft', 'Draft'); ?></span>
                                            <span class="ms-auto badge bg-secondary-light" id="draft-count">0</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?page=mailbox&folder=spam" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 <?php echo $currentFolder === 'spam' ? 'bg-primary-50 text-primary-600' : ''; ?>">
                                            <iconify-icon icon="solar:shield-warning-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('spam', 'Spam'); ?></span>
                                            <span class="ms-auto badge bg-danger-main" id="spam-count">0</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?page=mailbox&folder=bin" class="d-flex align-items-center gap-2 p-8 radius-8 hover-bg-neutral-100 <?php echo $currentFolder === 'bin' ? 'bg-primary-50 text-primary-600' : ''; ?>">
                                            <iconify-icon icon="solar:trash-bin-trash-bold" class="icon"></iconify-icon>
                                            <span><?php echo t_mailbox('bin', 'Bin'); ?></span>
                                            <span class="ms-auto badge bg-neutral-400" id="bin-count">0</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <?php if ($view === 'detail' && $emailId): ?>
                        <!-- Email Detail View -->
                        <div class="card p-0 radius-12">
                            <div class="card-body p-0">
                                <div id="email-detail-view">
                                    <!-- Will be loaded via JavaScript -->
                                    <div class="p-24 text-center">
                                        <div class="spinner-border text-primary-600" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Email List View -->
                        <div class="card p-0 radius-12">
                            <div class="card-body p-0">
                                <!-- Filters -->
                                <div class="p-16 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <select class="form-select form-select-sm" id="filter-select" style="min-width: 120px;">
                                            <option value="all"><?php echo t_mailbox('all', 'All'); ?></option>
                                            <option value="none"><?php echo t_mailbox('none', 'None'); ?></option>
                                            <option value="read"><?php echo t_mailbox('read', 'Read'); ?></option>
                                            <option value="unread"><?php echo t_mailbox('unread', 'Unread'); ?></option>
                                            <option value="starred"><?php echo t_mailbox('starred', 'Starred'); ?></option>
                                            <option value="unstarred"><?php echo t_mailbox('unstarred', 'Unstarred'); ?></option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-primary-100 text-primary-600" id="mark-all-read">
                                            <?php echo t_mailbox('mark_all_read', 'Mark all as read'); ?>
                                        </button>
                                    </div>
                                    <div class="text-secondary-light text-sm">
                                        <span id="email-range">0-0</span> <?php echo t_mailbox('of', 'of'); ?> <span id="total-emails">0</span>
                                    </div>
                                </div>

                                <!-- Email List -->
                                <div id="email-list" class="list-group list-group-flush">
                                    <!-- Will be loaded via JavaScript -->
                                    <div class="p-24 text-center">
                                        <div class="spinner-border text-primary-600" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Compose Modal -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold"><?php echo t_mailbox('compose', 'Compose'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="compose-form">
                    <div class="mb-16">
                        <label class="form-label fw-medium"><?php echo t_mailbox('to', 'To'); ?></label>
                        <input type="email" class="form-control" id="compose-to" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-medium"><?php echo t_mailbox('subject', 'Subject'); ?></label>
                        <input type="text" class="form-control" id="compose-subject">
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-medium"><?php echo t_mailbox('message', 'Message'); ?></label>
                        <textarea class="form-control" id="compose-message" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-dismiss="modal"><?php echo t_mailbox('cancel', 'Cancel'); ?></button>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" id="send-email">
                    <iconify-icon icon="solar:letter-opened-bold" class="text-xl"></iconify-icon>
                    <?php echo t_mailbox('send', 'Send'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Store script for later inclusion
$GLOBALS['mailboxScript'] = true;
$GLOBALS['mailpitApiUrl'] = $mailpitApiUrl;
$GLOBALS['currentFolder'] = $currentFolder;
$GLOBALS['emailId'] = $emailId;
$GLOBALS['view'] = $view;
?>

<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>

