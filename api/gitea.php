<?php
/**
 * Nucleus - Gitea Bridge API
 * Version: 1.0.0
 * Description: Links Nucleus projects to a self-hosted Gitea instance.
 *   - GET  ?action=status        -> per-project git health + Gitea link
 *   - GET  ?action=repos         -> list Gitea repos (requires GITEA_TOKEN)
 *   - POST ?action=create_repo   -> create a repo on Gitea (requires token + CSRF)
 *   - POST ?action=clone         -> clone a Gitea repo into the web root (CSRF)
 */

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (function_exists('check_auth')) {
    check_auth();
}

ob_clean();
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function giteaWwwPath(): string {
    return (class_exists('\Nucleus\Core\System') && method_exists('\Nucleus\Core\System', 'getWwwPath'))
        ? \Nucleus\Core\System::getWwwPath()
        : (defined('DOCUMENT_ROOT') ? DOCUMENT_ROOT : (defined('NUCLEUS_ROOT') ? NUCLEUS_ROOT . '/html' : '/var/www/html'));
}

try {
    $bridge = '\Nucleus\Core\Services\GiteaBridge';

    switch ($action) {
        case 'status':
            $www = rtrim(giteaWwwPath(), '/');
            $projects = [];
            $dirs = [$www];
            $children = @glob($www . '/*', GLOB_ONLYDIR);
            if (is_array($children)) {
                $dirs = array_merge($dirs, $children);
            }
            foreach ($dirs as $dir) {
                if (!is_dir($dir . '/.git')) continue;
                $name = basename($dir);
                $remote = $bridge::remoteFromPath($dir);
                $link = $bridge::repoFromRemote($remote);
                $projects[] = array_merge(
                    [
                        'name'     => $name,
                        'remote'   => $remote ?: null,
                        'linked'   => $link !== null,
                        'gitea_url' => $link['web_url'] ?? null,
                    ],
                    $bridge::gitHealth($dir)
                );
            }
            echo json_encode([
                'success'  => true,
                'enabled'  => $bridge::enabled(),
                'base_url' => $bridge::baseUrl(),
                'projects' => $projects,
            ]);
            break;

        case 'repos':
            $result = $bridge::listRepos();
            $repos = [];
            if ($result['success']) {
                foreach ($result['data'] as $repo) {
                    $repos[] = [
                        'name'     => $repo['name'] ?? '',
                        'full_name' => $repo['full_name'] ?? '',
                        'html_url' => $repo['html_url'] ?? '',
                        'clone_url' => $repo['clone_url'] ?? '',
                        'description' => $repo['description'] ?? '',
                        'private'  => (bool)($repo['private'] ?? false),
                        'default_branch' => $repo['default_branch'] ?? 'main',
                    ];
                }
            }
            echo json_encode([
                'success' => $result['success'],
                'repos' => $repos,
                'error' => $result['error'] ?? ($result['data']['message'] ?? ($result['success'] ? null : 'Gitea API request failed')),
            ]);
            break;

        case 'create_repo':
            $token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
            if (!verifyCSRFToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
                break;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }
            $name = trim((string)($input['name'] ?? ''));
            if ($name === '' || !preg_match('/^[a-zA-Z0-9_.-]+$/', $name)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Valid repository name required']);
                break;
            }
            $result = $bridge::createRepo(
                $name,
                trim((string)($input['description'] ?? '')),
                (bool)($input['private'] ?? false)
            );
            if ($result['success']) {
                echo json_encode(['success' => true, 'repo' => $result['data']['html_url'] ?? ($bridge::baseUrl() . '/' . $name)]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $result['data']['message'] ?? ($result['error'] ?? 'Gitea API request failed')]);
            }
            break;

        case 'clone':
            $token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
            if (!verifyCSRFToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
                break;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }
            $url = trim((string)($input['url'] ?? ''));
            $target = trim((string)($input['target'] ?? ''));
            if ($url === '' || $target === '' || !preg_match('/^[a-zA-Z0-9_.-]+$/', $target)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Valid clone url and target name required']);
                break;
            }
            $destination = rtrim(giteaWwwPath(), '/') . '/' . $target;
            if (file_exists($destination)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Destination already exists']);
                break;
            }
            $oldCwd = getcwd();
            @chdir(rtrim(giteaWwwPath(), '/'));
            @exec('git clone ' . escapeshellarg($url) . ' ' . escapeshellarg($target) . ' 2>&1', $output, $returnVar);
            @chdir($oldCwd);
            if ($returnVar === 0 && is_dir($destination)) {
                echo json_encode(['success' => true, 'target' => $target]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => implode("\n", $output) ?: 'Clone failed']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
