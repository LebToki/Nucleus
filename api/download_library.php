<?php
/**
 * Nucleus - Download & Install Library API
 * Version: 1.1.0
 * Description: Downloads a pre-packaged platform library (e.g. wordpress.zip,
 * phpmyadmin.zip), extracts it, and maps it to a directory in the document root
 * based on the library name. Mirrors Laragon's "Quick Add" feature.
 *
 * Endpoints:
 *   GET  ?action=info&key=<libraryKey>  -> returns library metadata for preview
 *   POST action=download&key=&target=   -> downloads, extracts & maps library
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/libraries.php';

// Enforce authentication
if (function_exists('check_auth')) {
    check_auth();
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ---------------------------------------------------------------------------
// ACTION: info (GET) - return library metadata for the wizard preview card
// ---------------------------------------------------------------------------
if ($action === 'info') {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $libraryKey = $_GET['key'] ?? '';
    $library = getDownloadableLibrary($libraryKey);

    if (!$library) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown library: ' . htmlspecialchars($libraryKey)]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'library' => [
            'key'         => $library['key'],
            'name'        => $library['name'],
            'description' => $library['description'] ?? '',
            'icon'        => $library['icon'] ?? '',
            'color'       => $library['color'] ?? '#0d6efd',
            'category'    => $library['category'] ?? 'Other',
            'dir'         => $library['dir'] ?? $library['key'],
            'requires_db' => !empty($library['requires_db']),
            'size'        => $library['size'] ?? null,
        ],
    ]);
    exit;
}

// ---------------------------------------------------------------------------
// ACTION: download (POST) - download, extract & map the library
// ---------------------------------------------------------------------------
if ($action === 'download') {
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // CSRF check (FormData POST)
    $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!validate_csrf_token($csrfToken)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
        exit;
    }

    $libraryKey = $_POST['key'] ?? '';
    $targetName = $_POST['target'] ?? '';

    // Validate library key
    $library = getDownloadableLibrary($libraryKey);
    if (!$library) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown library: ' . htmlspecialchars($libraryKey)]);
        exit;
    }

    // Determine target directory name
    // If a custom name is provided, use it; otherwise map based on the library's dir name
    if (empty($targetName)) {
        $targetName = $library['dir'];
    }

    // Validate target name (prevent directory traversal)
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $targetName) ||
        strpos($targetName, '..') !== false ||
        strpos($targetName, '/') !== false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid directory name. Only letters, numbers, underscores, and hyphens allowed.']);
        exit;
    }

    // Block dangerous names
    $blockedNames = [
        '.', '..', 'dashboard', 'api', 'includes', 'config', 'laragon', 'www',
        'assets', 'build', 'pages', 'partials', 'i18n', 'cache', 'data', 'logs'
    ];

    if (in_array(strtolower($targetName), $blockedNames, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid directory name. This name is reserved or blocked.']);
        exit;
    }

    // Get document root
    $laraconfig = getLaragonConfig();
    $documentRoot = (class_exists('\Nucleus\Core\System') && method_exists('\Nucleus\Core\System', 'getWwwPath'))
        ? \Nucleus\Core\System::getWwwPath()
        : (defined('NUCLEUS_ROOT') ? NUCLEUS_ROOT . '/html' : '/var/www/html');

    // Fallback to platform-aware www path
    if (empty($documentRoot) || !is_dir($documentRoot)) {
        if (class_exists('\Nucleus\Core\System') && method_exists('\Nucleus\Core\System', 'getWwwPath')) {
            $documentRoot = \Nucleus\Core\System::getWwwPath();
        } else {
            $laragonRoot = getLaragonRoot();
            $documentRoot = $laragonRoot . '/html';
        }
    }

    if (empty($documentRoot) || !is_dir($documentRoot)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Document root not found']);
        exit;
    }

    // Normalize and validate target path
    $targetPath = realpath(rtrim($documentRoot, '/')) . DIRECTORY_SEPARATOR . $targetName;

    // Ensure path is under document root
    if (strpos($targetPath, realpath($documentRoot)) !== 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid target path']);
        exit;
    }

    // Check if target already exists
    if (is_dir($targetPath) || file_exists($targetPath)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'A directory or file named "' . $targetName . '" already exists']);
        exit;
    }

    // Create progress log file
    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = 'download_' . $targetName . '.log';
    $logPath = $logDir . '/' . $logFile;
    file_put_contents($logPath, "Starting download & install of {$library['name']}...\n");

    try {
        // Special case: phpinfo is generated locally (no download needed)
        if ($libraryKey === 'phpinfo') {
            file_put_contents($logPath, "Generating PHP info page...\n", FILE_APPEND);
            if (!mkdir($targetPath, 0755, true)) {
                throw new Exception('Failed to create directory');
            }
            $phpinfoContent = "<?php\n/**\n * PHP Info - generated by Nucleus\n */\nphpinfo();\n";
            file_put_contents($targetPath . '/index.php', $phpinfoContent);
            file_put_contents($logPath, "PHP info page created.\n", FILE_APPEND);
            file_put_contents($logPath, "Download & install completed successfully!\n", FILE_APPEND);
            echo json_encode([
                'success' => true,
                'message' => $library['name'] . ' installed successfully',
                'directory' => $targetName,
                'path' => $targetPath,
                'url' => 'http://' . $targetName . (defined('DOMAIN_SUFFIX') ? DOMAIN_SUFFIX : '.local'),
                'log_file' => $logFile
            ]);
            exit;
        }

        // Special case: Adminer is a single PHP file
        if ($libraryKey === 'adminer') {
            file_put_contents($logPath, "Downloading Adminer...\n", FILE_APPEND);
            if (!mkdir($targetPath, 0755, true)) {
                throw new Exception('Failed to create directory');
            }
            $adminerContent = @file_get_contents($library['url']);
            if ($adminerContent === false || empty($adminerContent)) {
                throw new Exception('Failed to download Adminer');
            }
            file_put_contents($targetPath . '/adminer.php', $adminerContent);
            file_put_contents($logPath, "Adminer downloaded and installed.\n", FILE_APPEND);
            file_put_contents($logPath, "Download & install completed successfully!\n", FILE_APPEND);
            echo json_encode([
                'success' => true,
                'message' => $library['name'] . ' installed successfully',
                'directory' => $targetName,
                'path' => $targetPath,
                'url' => 'http://' . $targetName . (defined('DOMAIN_SUFFIX') ? DOMAIN_SUFFIX : '.local'),
                'log_file' => $logFile
            ]);
            exit;
        }

        // Validate download URL
        if (empty($library['url'])) {
            throw new Exception('No download URL configured for this library');
        }

        // Download the archive
        file_put_contents($logPath, "Downloading from {$library['url']}...\n", FILE_APPEND);

        $tempDir = sys_get_temp_dir() . '/nucleus_download_' . uniqid();
        if (!mkdir($tempDir, 0755, true)) {
            throw new Exception('Failed to create temporary directory');
        }

        $archiveName = basename(parse_url($library['url'], PHP_URL_PATH));
        if (empty($archiveName) || strpos($archiveName, '.') === false) {
            $archiveName = $library['archive'] ?: $libraryKey . '.zip';
        }
        $archivePath = $tempDir . '/' . $archiveName;

        // Download using cURL if available, otherwise file_get_contents
        $downloaded = false;
        if (function_exists('curl_init')) {
            $ch = curl_init($library['url']);
            $fp = fopen($archivePath, 'wb');
            if ($ch && $fp) {
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Nucleus-Dashboard/1.0');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                fclose($fp);
                if ($result !== false && $httpCode >= 200 && $httpCode < 300) {
                    $downloaded = true;
                }
            }
        }

        if (!$downloaded) {
            // Fallback to file_get_contents
            $content = @file_get_contents($library['url']);
            if ($content === false) {
                throw new Exception('Failed to download the archive. Check your internet connection.');
            }
            file_put_contents($archivePath, $content);
            $downloaded = true;
        }

        if (!file_exists($archivePath) || filesize($archivePath) < 100) {
            throw new Exception('Downloaded file is empty or invalid');
        }

        file_put_contents($logPath, "Download complete (" . round(filesize($archivePath) / 1048576, 2) . " MB). Extracting...\n", FILE_APPEND);

        // Extract the archive
        $extractDir = $tempDir . '/extracted';
        if (!mkdir($extractDir, 0755, true)) {
            throw new Exception('Failed to create extraction directory');
        }

        $extracted = extractArchive($archivePath, $extractDir);
        if (!$extracted) {
            throw new Exception('Failed to extract the archive. The file may be corrupted or not a valid zip.');
        }

        // Determine the source directory to copy from
        $sourceDir = $extractDir;
        $strip = (int)($library['strip'] ?? 0);

        // If strip > 0, find the top-level directory and use it as source
        if ($strip > 0) {
            $entries = array_diff(scandir($extractDir), ['.', '..']);
            if (count($entries) === 1) {
                $single = $extractDir . '/' . reset($entries);
                if (is_dir($single)) {
                    $sourceDir = $single;
                }
            }
        }

        // Create target directory and copy files
        if (!mkdir($targetPath, 0755, true)) {
            throw new Exception('Failed to create target directory');
        }

        file_put_contents($logPath, "Installing to {$targetPath}...\n", FILE_APPEND);
        copyDirectory($sourceDir, $targetPath);

        // Clean up temp files
        @rmdir_recursive($tempDir);

        file_put_contents($logPath, "Download & install completed successfully!\n", FILE_APPEND);

        echo json_encode([
            'success' => true,
            'message' => $library['name'] . ' installed successfully',
            'directory' => $targetName,
            'path' => $targetPath,
            'url' => 'http://' . $targetName . (defined('DOMAIN_SUFFIX') ? DOMAIN_SUFFIX : '.local'),
            'log_file' => $logFile
        ]);

    } catch (Exception $e) {
        // Clean up temp dir on error
        if (isset($tempDir) && is_dir($tempDir)) {
            @rmdir_recursive($tempDir);
        }
        // Clean up partial target
        if (isset($targetPath) && is_dir($targetPath)) {
            @rmdir_recursive($targetPath);
        }
        if (isset($logPath)) {
            file_put_contents($logPath, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage(), 'log_file' => $logFile ?? null]);
    }
    exit;
}

// ---------------------------------------------------------------------------
// Unknown action
// ---------------------------------------------------------------------------
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unknown action. Expected "info" or "download".']);
exit;

/**
 * Extract a zip archive to a directory
 */
function extractArchive($archivePath, $destDir) {
    // Try ZipArchive (PHP extension)
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($archivePath) === true) {
            $zip->extractTo($destDir);
            $zip->close();
            return true;
        }
    }

    // Try unzip command
    $output = @shell_exec('unzip -o ' . escapeshellarg($archivePath) . ' -d ' . escapeshellarg($destDir) . ' 2>&1');
    if ($output !== null && is_dir($destDir) && count(array_diff(scandir($destDir), ['.', '..'])) > 0) {
        return true;
    }

    // Try tar (for .tar.gz archives)
    $output = @shell_exec('tar -xzf ' . escapeshellarg($archivePath) . ' -C ' . escapeshellarg($destDir) . ' 2>&1');
    if ($output !== null && is_dir($destDir) && count(array_diff(scandir($destDir), ['.', '..'])) > 0) {
        return true;
    }

    return false;
}

/**
 * Recursively copy a directory
 */
function copyDirectory($source, $dest) {
    if (!is_dir($source)) {
        return;
    }
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }
    $items = scandir($source);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $srcPath = $source . '/' . $item;
        $dstPath = $dest . '/' . $item;
        if (is_dir($srcPath)) {
            copyDirectory($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
}

/**
 * Recursively remove a directory
 */
function rmdir_recursive($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        is_dir($path) ? rmdir_recursive($path) : @unlink($path);
    }
    return @rmdir($dir);
}
