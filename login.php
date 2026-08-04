<?php
/**
 * Nucleus - Login Page
 * Version: 1.0.1
 * Authentication page for Nucleus Dashboard
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/helpers.php';

// If already authenticated, redirect to dashboard
if (\Nucleus\Core\Security::isAuthenticated()) {
    header('Location: index.php');
    exit;
}

// If auth is disabled, redirect to dashboard
if (defined('AUTH_ENABLED') && !AUTH_ENABLED) {
    header('Location: index.php');
    exit;
}

// Mark as login page to suppress sidebar/navbar in layout
$GLOBALS['is_login_page'] = true;

$error = '';
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : '/assets';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!\Nucleus\Core\Security::verifyCSRFToken($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';

        if (empty($password)) {
            $error = 'Password is required.';
        } elseif (!defined('ADMIN_PASSWORD')) {
            $error = 'Authentication not configured.';
        } elseif (hash_equals(ADMIN_PASSWORD, $password)) {
            // Successful login
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['authenticated'] = true;
            $_SESSION['auth_source'] = 'password';
            $_SESSION['login_time'] = time();

            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);

            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid password.';
            \Nucleus\Core\Logger::info('Failed login attempt from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        }
    }
}

$csrfToken = \Nucleus\Core\Security::getCSRFToken();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nucleus — Login</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($assetsUrl); ?>/images/favicon/favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>/css/remixicon.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>/css/lib/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>/css/style.css">
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .login-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 32px;
            text-align: center;
            color: #fff;
        }
        .login-header img {
            max-width: 80px;
            margin-bottom: 12px;
        }
        .login-header h4 {
            margin: 0;
            font-weight: 700;
        }
        .login-header p {
            margin: 4px 0 0;
            opacity: 0.8;
            font-size: 14px;
        }
        .login-body {
            background: #fff;
            padding: 32px;
        }
        [data-theme="dark"] .login-body {
            background: #1a1a2e;
        }
        .login-footer {
            text-align: center;
            padding: 16px;
            background: #f8f9fa;
            font-size: 12px;
            color: #6c757d;
        }
        [data-theme="dark"] .login-footer {
            background: #16162a;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="<?php echo htmlspecialchars($assetsUrl); ?>/images/logo-icon.png" alt="Nucleus" onerror="this.style.display='none'">
                <h4>Nucleus</h4>
                <p>The Missing Dashboard for Linux Developers</p>
            </div>
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                        <iconify-icon icon="solar:danger-circle-bold" class="text-lg"></iconify-icon>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                    <div class="mb-20">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control py-11" id="password" name="password"
                                   placeholder="Enter your password" required autofocus
                                   autocomplete="current-password">
                            <button type="button" class="position-absolute top-50 end-0 translate-middle-y me-12 btn btn-link p-0 text-secondary-light"
                                    onclick="togglePassword()" tabindex="-1" aria-label="Toggle password visibility">
                                <iconify-icon icon="solar:eye-outline" id="toggle-icon" class="text-lg"></iconify-icon>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-600 w-100 py-11 fw-semibold">
                        <iconify-icon icon="solar:login-3-bold" class="text-lg me-2"></iconify-icon>
                        Sign In
                    </button>
                </form>

                <div class="mt-16 text-center">
                    <small class="text-secondary-light">
                        Set password via <code>NUCLEUS_PASSWORD</code> env var
                    </small>
                </div>
            </div>
            <div class="login-footer">
                &copy; <?php echo date('Y'); ?> Nucleus &mdash; 2TInteractive
            </div>
        </div>
    </div>

    <script src="<?php echo htmlspecialchars($assetsUrl); ?>/js/lib/jquery-3.7.1.min.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>/js/lib/bootstrap.bundle.min.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>/js/lib/iconify-icon.min.js"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('icon', 'solar:eye-closed-outline');
            } else {
                input.type = 'password';
                icon.setAttribute('icon', 'solar:eye-outline');
            }
        }

        // Theme support
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.querySelector('html').setAttribute('data-theme', theme);
        })();
    </script>
</body>
</html>
