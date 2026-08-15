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
        'version' => 2,
        'services' => [
            ['id' => '2ti-orchestrator', 'name' => '2TI Orchestrator', 'icon' => 'tabler:device-desktop-analytics', 'color' => 'primary', 'port' => 80, 'ssl_port' => 443, 'vhost' => 'localhost', 'schema' => 'http', 'webui' => true, 'description' => 'Media engine — 80 services, Laravel 11, video production pipeline'],
            ['id' => 'foundry', 'name' => 'FOUNDRY', 'icon' => 'fluent:fireplace-24-regular', 'color' => 'danger', 'port' => 7861, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'AI Production Engine — director pipeline, video strategy, GPU engine'],
            ['id' => 'agency', 'name' => 'AGENCY', 'icon' => 'tabler:brain', 'color' => 'info', 'port' => 7863, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Autonomous tech delivery — missions, systems, nerve center'],
            ['id' => 'comfyui', 'name' => 'ComfyUI', 'icon' => 'selfhst:comfyui-dark', 'color' => 'primary', 'port' => 8188, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Node-based image/video generation — 860 nodes, 18 workflow templates'],
            ['id' => 'llama-server', 'name' => 'llama-server', 'icon' => 'simple-icons:llama', 'color' => 'success', 'port' => 8900, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Local LLM inference — 19 models, OpenAI-compatible API, Blackwell CUDA'],
            ['id' => 'hermes-serve', 'name' => 'Hermes Agent', 'icon' => 'game-icons:brain-tentacle', 'color' => 'warning', 'port' => 9119, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Swarm mind — backlog owner, memory hub, 12 skill categories'],
            ['id' => 'hermes-dashboard', 'name' => 'Hermes Dashboard', 'icon' => 'game-icons:brain-tentacle', 'color' => 'warning', 'port' => 9120, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Hermes web UI — session management, agent monitoring'],
            ['id' => 'swarmclaw', 'name' => 'SwarmClaw', 'icon' => 'mdi:spider', 'color' => 'danger', 'port' => 3456, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Multi-agent orchestration — coding, production, mission control'],
            ['id' => 'mem0', 'name' => 'Mem0', 'icon' => 'mdi:memory', 'color' => 'info', 'port' => 3500, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Universal memory layer — shared memory bus for all agents'],
            ['id' => 'chromadb', 'name' => 'ChromaDB', 'icon' => 'simple-icons:chromadb', 'color' => 'info', 'port' => 8001, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Vector database — entity store for characters, voices, attire'],
            ['id' => 'n8n', 'name' => 'n8n', 'icon' => 'simple-icons:n8n', 'color' => 'danger', 'port' => 5678, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Workflow automation — bridge between Business and Media engines'],
            ['id' => 'shadow-downloader', 'name' => 'Shadow Downloader', 'icon' => 'mdi:download-circle', 'color' => 'success', 'port' => 7870, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Power-cut resilient download manager — aria2 backend'],
            ['id' => 'gitea', 'name' => 'Gitea', 'icon' => 'devicon:gitea', 'color' => 'primary', 'port' => 3000, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Self-hosted Git — universal revision control'],
            ['id' => 'aria2', 'name' => 'aria2 RPC', 'icon' => 'mdi:download-network', 'color' => 'secondary', 'port' => 6800, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Download engine RPC — managed by Shadow Downloader'],
            ['id' => 'ollama', 'name' => 'Ollama', 'icon' => 'simple-icons:ollama', 'color' => 'info', 'port' => 11434, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Local LLM runtime — embeddings (nomic-embed-text)'],
            ['id' => 'mysql', 'name' => 'MySQL', 'icon' => 'tabler:brand-mysql', 'color' => 'info', 'port' => 3306, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => false, 'description' => 'Primary database — orchestrator, karaoke, content'],
            ['id' => 'mailpit', 'name' => 'Mailpit', 'icon' => 'lucide:mail-search', 'color' => 'primary', 'port' => 8025, 'ssl_port' => null, 'vhost' => null, 'schema' => 'http', 'webui' => true, 'description' => 'Development mail catcher'],
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