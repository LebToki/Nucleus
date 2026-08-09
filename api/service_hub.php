<?php
/**
 * Nucleus - Service Hub API
 * Version: 1.0.0
 * Description: Centralized service registry with live port detection.
 *   - GET  ?action=list            -> registry merged with ss -tlnp live status
 *   - GET  ?action=ports           -> raw live port/process list
 *   - POST ?action=save            -> create or update a registry entry
 *   - POST ?action=delete&id=...   -> remove a registry entry
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

$REGISTRY_FILE = __DIR__ . '/../data/services_registry.json';

/**
 * Load the service registry (seeds from defaults if missing).
 */
function loadRegistry(): array {
    global $REGISTRY_FILE;
    $data = [];
    if (file_exists($REGISTRY_FILE)) {
        $raw = @file_get_contents($REGISTRY_FILE);
        $decoded = @json_decode($raw, true);
        if (is_array($decoded) && isset($decoded['services'])) {
            $data = $decoded;
        }
    }
    if (!isset($data['services']) || !is_array($data['services'])) {
        $data = defaultRegistry();
        saveRegistry($data);
    }
    return $data;
}

/**
 * Built-in defaults used on the first run (before any user edits).
 */
function defaultRegistry(): array {
    return [
        'version' => 1,
        'services' => [
            ['id' => 'gitea', 'name' => 'Gitea', 'icon' => 'simple-icons:gitee', 'color' => 'primary', 'port' => 3000, 'ssl_port' => null, 'vhost' => 'gitea.local', 'schema' => 'https', 'webui' => true, 'description' => 'Self-hosted Git service'],
            ['id' => 'n8n', 'name' => 'n8n', 'icon' => 'simple-icons:n8n', 'color' => 'danger', 'port' => 5678, 'ssl_port' => null, 'vhost' => 'n8n.local', 'schema' => 'https', 'webui' => true, 'description' => 'Workflow automation'],
            ['id' => 'comfyui', 'name' => 'ComfyUI', 'icon' => 'simple-icons:comfyui', 'color' => 'primary', 'port' => 8188, 'ssl_port' => null, 'vhost' => 'comfyui.local', 'schema' => 'https', 'webui' => true, 'description' => 'Node-based Stable Diffusion UI'],
            ['id' => 'ollama', 'name' => 'Ollama', 'icon' => 'simple-icons:ollama', 'color' => 'info', 'port' => 11434, 'ssl_port' => null, 'vhost' => 'ollama.local', 'schema' => 'https', 'webui' => false, 'description' => 'Local LLM runtime (API only)'],
            ['id' => '2ti-orchestrator', 'name' => '2TI Orchestrator', 'icon' => 'tabler:device-desktop-analytics', 'color' => 'primary', 'port' => 8000, 'ssl_port' => null, 'vhost' => '2ti-orchestrator.local', 'schema' => 'https', 'webui' => true, 'description' => '2TI platform orchestration'],
            ['id' => '2tinteractive', 'name' => '2TIInteractive', 'icon' => 'solar:planet-bold', 'color' => 'primary', 'port' => 3001, 'ssl_port' => null, 'vhost' => '2tinteractive.local', 'schema' => 'https', 'webui' => true, 'description' => '2TI interactive site'],
            ['id' => 'eventbus', 'name' => 'EventBus', 'icon' => 'ph:bus', 'color' => 'success', 'port' => 8200, 'ssl_port' => null, 'vhost' => 'eventbus.local', 'schema' => 'https', 'webui' => true, 'description' => 'Event streaming service'],
            ['id' => 'demucs', 'name' => 'Demucs', 'icon' => 'lucide:audio-waveform', 'color' => 'warning', 'port' => 8215, 'ssl_port' => null, 'vhost' => 'demucs.local', 'schema' => 'https', 'webui' => true, 'description' => 'Audio source separation'],
            ['id' => 'voxcpm', 'name' => 'VoxCPM', 'icon' => 'ph:microphone-bold', 'color' => 'info', 'port' => 8210, 'ssl_port' => null, 'vhost' => 'voxcpm.local', 'schema' => 'https', 'webui' => true, 'description' => 'TTS / voice generation'],
            ['id' => 'wan', 'name' => 'Wan', 'icon' => 'lucide:video', 'color' => 'danger', 'port' => 8220, 'ssl_port' => null, 'vhost' => 'wan.local', 'schema' => 'https', 'webui' => true, 'description' => 'Video generation service'],
            ['id' => 'mailpit', 'name' => 'Mailpit', 'icon' => 'lucide:mail-search', 'color' => 'primary', 'port' => 8025, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Development mail catcher'],
            ['id' => 'mysql', 'name' => 'MySQL', 'icon' => 'tabler:brand-mysql', 'color' => 'info', 'port' => 3306, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'SQL database'],
        ],
    ];
}

/**
 * Snapshot listening ports via `ss -tlnp` -> map port => [processes].
 */
function getLivePorts(): array {
    $ports = [];
    $out = [];
    @exec('ss -tlnp 2>/dev/null', $out);
    foreach ($out as $line) {
        if (!preg_match('/LISTEN/i', $line)) continue;
        // Match both 0.0.0.0:80 and *:443 and [::]:5678
        if (!preg_match('/(?:[\d.]+|\*|\[::\]):(\d+)\s+\S+/', $line, $m)) continue;
        $port = (int)$m[1];
        if ($port < 1 || $port > 65535) continue;
        $procs = [];
        if (preg_match_all('/"([^"]+)"/', $line, $pm)) {
            $procs = array_values(array_unique($pm[1]));
        }
        if (!isset($ports[$port])) {
            $ports[$port] = $procs;
        } else {
            $ports[$port] = array_values(array_unique(array_merge($ports[$port], $procs)));
        }
    }
    return $ports;
}

/**
 * Convert a registry entry into a list row (with live status).
 */
function enrichEntry(array $entry, array $livePorts): array {
    $port = (int)($entry['port'] ?? 0);
    $sslPort = isset($entry['ssl_port']) ? (int)$entry['ssl_port'] : null;
    $running = array_key_exists($port, $livePorts);
    $procs = $running ? $livePorts[$port] : [];
    $url = '';
    if (!empty($entry['vhost'])) {
        $schema = $entry['schema'] ?? 'https';
        $url = $schema . '://' . $entry['vhost'] . '/';
    }
    return [
        'id' => $entry['id'] ?? '',
        'name' => $entry['name'] ?? 'Untitled',
        'icon' => $entry['icon'] ?? 'tabler:flame',
        'color' => $entry['color'] ?? 'primary',
        'port' => $port,
        'ssl_port' => $sslPort,
        'vhost' => $entry['vhost'] ?? null,
        'schema' => $entry['schema'] ?? 'https',
        'webui' => (bool)($entry['webui'] ?? true),
        'description' => $entry['description'] ?? '',
        'url' => $url,
        'status' => $running ? 'running' : 'stopped',
        'processes' => $procs,
    ];
}

/**
 * Persist the registry (atomically via tmp file).
 */
function saveRegistry(array $data): bool {
    global $REGISTRY_FILE;
    $tmp = $REGISTRY_FILE . '.tmp';
    $content = @json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($content === false) return false;
    if (@file_put_contents($tmp, $content . "\n") === false) return false;
    @chmod($tmp, 0664);
    return @rename($tmp, $REGISTRY_FILE);
}

try {
    $action = $_GET['action'] ?? 'list';

    switch ($action) {
        case 'list':
            $registry = loadRegistry();
            $live = getLivePorts();
            $rows = [];
            foreach ($registry['services'] as $svc) {
                $rows[] = enrichEntry($svc, $live);
            }
            echo json_encode([
                'success' => true,
                'data' => $rows,
                'live_ports' => $live,
            ]);
            break;

        case 'ports':
            echo json_encode(['success' => true, 'data' => getLivePorts()]);
            break;

        case 'save':
            // CSRF guard
            $token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
            if (!verifyCSRFToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
                break;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input) || empty($input['name'])) {
                echo json_encode(['success' => false, 'error' => 'Name is required']);
                break;
            }

            $registry = loadRegistry();
            $id = $input['id'] ?? '';
            $slug = strtolower(trim($input['name'] ?? ''));
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
            $slug = trim($slug, '-');
            if ($id === '') {
                $id = $slug !== '' ? $slug : ('svc-' . uniqid());
            }

            $entry = [
                'id' => $id,
                'name' => trim($input['name']),
                'icon' => trim($input['icon'] ?? 'tabler:flame') ?: 'tabler:flame',
                'color' => trim($input['color'] ?? 'primary') ?: 'primary',
                'port' => (int)($input['port'] ?? 0),
                'ssl_port' => isset($input['ssl_port']) && $input['ssl_port'] !== '' ? (int)$input['ssl_port'] : null,
                'vhost' => trim($input['vhost'] ?? '') ?: null,
                'schema' => in_array($input['schema'] ?? '', ['http', 'https']) ? $input['schema'] : 'https',
                'webui' => (bool)($input['webui'] ?? true),
                'description' => trim($input['description'] ?? ''),
            ];

            $replaced = false;
            foreach ($registry['services'] as $i => $svc) {
                if (($svc['id'] ?? '') === $id) {
                    $registry['services'][$i] = $entry;
                    $replaced = true;
                    break;
                }
            }
            if (!$replaced) {
                $registry['services'][] = $entry;
            }

            if (saveRegistry($registry)) {
                echo json_encode(['success' => true, 'data' => $entry]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Could not write registry']);
            }
            break;

        case 'delete':
            $token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
            if (!verifyCSRFToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
                break;
            }
            $id = $_GET['id'] ?? ($_GET['service'] ?? '');
            if ($id === '') {
                echo json_encode(['success' => false, 'error' => 'Service id required']);
                break;
            }
            $registry = loadRegistry();
            $before = count($registry['services']);
            $registry['services'] = array_values(array_filter($registry['services'], function ($svc) use ($id) {
                return ($svc['id'] ?? '') !== $id;
            }));
            if (count($registry['services']) === $before) {
                echo json_encode(['success' => false, 'error' => 'Service not found']);
                break;
            }
            if (saveRegistry($registry)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Could not write registry']);
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