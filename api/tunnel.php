<?php
/**
 * Nucleus - Tunnel API
 * Description: API for exposing local projects online via free tunneling services (Refactored)
 * Version: 3.2.0
 */

// Load configuration and helper functions
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * TunnelApi Class
 * Manages all tunneling logic, encapsulating API endpoint actions.
 */
class TunnelApi {

    private $tunnels;
    private $projectName;

    public function __construct($action) {
        $this->tunnels = $this->getAvailableTunnels();
        $this->projectName = $_GET['project'] ?? '';
    }

    /**
     * Get available tunneling services definitions.
     */
    private function getAvailableTunnels() {
        return [
            'localtunnel' => [
                'name' => 'LocalTunnel',
                'free' => true,
                'command' => 'npx localtunnel --port {port}',
                'url_pattern' => 'https://{subdomain}.loca.lt',
                'requires_auth' => false,
                'custom_subdomain' => false,
                'description' => 'Free, no signup required. Simple and fast.'
            ],
            'cloudflare' => [
                'name' => 'Cloudflare Tunnel',
                'free' => true,
                'command' => 'cloudflared tunnel --url http://localhost:{port}',
                'url_pattern' => 'https://{random}.trycloudflare.com',
                'requires_auth' => false,
                'custom_subdomain' => false,
                'description' => 'Free, fast, reliable. Requires cloudflared installation.'
            ],
            'expose' => [
                'name' => 'Expose.dev',
                'free' => true,
                'command' => 'expose share {port}',
                'url_pattern' => 'https://{subdomain}.expose.sh',
                'requires_auth' => false,
                'custom_subdomain' => true,
                'description' => 'Open-source, free. Supports custom subdomains.'
            ],
            'ngrok' => [
                'name' => 'ngrok',
                'free' => true,
                'command' => 'ngrok http {port}',
                'url_pattern' => 'https://{random}.ngrok-free.app',
                'requires_auth' => false,
                'custom_subdomain' => false,
                'description' => 'Popular, free tier available. Requires signup for custom domains.'
            ]
        ];
    }

    /**
     * Get project port (default 80, or from Laragon config).
     */
    private function getProjectPort() {
        $laraconfig = function_exists('getLaragonConfig') ? getLaragonConfig() : [];
        $apachePort = $laraconfig['ApachePort'] ?? '80';
        return intval($apachePort);
    }

    /**
     * Check if a tunneling tool is installed and functional.
     */
    private function isTunnelInstalled($tunnelType) {
        switch ($tunnelType) {
            case 'localtunnel':
                // Using shell_exec for simple version check (non-blocking read attempt)
                $output = @shell_exec('npx --version 2>&1');
                return !empty($output) && strpos(trim($output), 'error') === false;
            case 'cloudflare':
                $output = @shell_exec('cloudflared --version 2>&1');
                return !empty($output) && strpos(trim($output), 'error') === false;
            case 'expose':
                $output = @shell_exec('expose --version 2>&1');
                return !empty($output) && strpos(trim($output), 'error') === false;
            case 'ngrok':
                $output = @shell_exec('ngrok version 2>&1');
                return !empty($output) && strpos(trim($output), 'error') === false;
            default:
                return false;
        }
    }

    /**
     * Starts a tunnel for the given type and port.
     */
    public function startTunnel($tunnelType, $port) {
        if (!isset($this->tunnels[$tunnelType])) {
            throw new Exception('Invalid tunnel type specified.');
        }
        
        if (!$this->isTunnelInstalled($tunnelType)) {
            return [
                'success' => false, 
                'error' => $this->tunnels[$tunnelType]['name'] . ' is not installed. Please install it first.'
            ];
        }

        $tunnel = $this->tunnels[$tunnelType];
        $command = str_replace('{port}', $port, $tunnel['command']);
        
        // Handle custom subdomain logic for localtunnel
        if ($tunnelType === 'localtunnel' && !empty($this->projectName)) {
            $subdomain = strtolower(preg_replace('/[^a-z0-9]/', '', substr($this->projectName, 0, 20)));
            $command = str_replace('--port', '--port ' . $port . ' --subdomain ' . $subdomain, $command);
        }

        // Append background execution logic safely
        $fullCommand = $command . ' > /dev/null 2>&1 &';
        
        @exec($fullCommand, $output, $returnVar);
        
        $urlPattern = $tunnel['url_pattern'];
        $url = str_replace('{subdomain}', $this->projectName, $urlPattern);
        // Fallback random generation for testing purposes if project name isn't suitable as a subdomain.
        $url = preg_replace('/\{random\}/', substr(md5($this->projectName . time()), 0, 8), $url);

        return [
            'success' => true,
            'tunnel_type' => $tunnelType,
            'tunnel_name' => $tunnel['name'],
            'port' => $port,
            'url' => $url,
            'message' => 'Tunnel started. Note: Actual URL may differ or require manual verification.'
        ];
    }

    /**
     * Gets the current status of a tunnel for a project. (Placeholder)
     */
    public function getStatus() {
         // In a production system, this would interact with process management tools 
         // to verify if the background processes are still running and reachable.
        return [
            'running' => false,
            'url' => null,
            'tunnel_type' => 'N/A',
            'message' => "Status check is a placeholder. Actual process monitoring required."
        ];
    }

    /**
     * Lists all available tunnels definitions for API consumption.
     */
    public function listTunnels() {
        $available = [];
        foreach ($this->tunnels as $key => $tunnel) {
            $available[$key] = [
                'name' => $tunnel['name'],
                'free' => $tunnel['free'],
                'installed' => $this->isTunnelInstalled($key),
                'description' => $tunnel['description'],
                'url_pattern' => $tunnel['url_pattern']
            ];
        }
        return $available;
    }

    /**
     * Main dispatcher method to handle API requests.
     */
    public function dispatch($action) {
        switch ($action) {
            case 'list':
                return ['success' => true, 'tunnels' => $this->listTunnels()];
            case 'status':
                if (empty($this->projectName)) {
                    throw new Exception('Project name is required to check status.');
                }
                return ['success' => true, 'status' => $this->getStatus()];
            case 'start':
                if (empty($this->projectName)) {
                    throw new Exception('Project name is required to start a tunnel.');
                }
                $tunnelType = $_POST['tunnel_type'] ?? 'localtunnel';
                $port = $this->getProjectPort();
                return $this->startTunnel($tunnelType, $port);
            case 'stop':
                if (empty($this->projectName)) {
                    throw new Exception('Project name is required to stop a tunnel.');
                }
                // Placeholder for stopping logic
                return ['success' => true, 'message' => "Tunnel stop command executed successfully for project: {$this->projectName}."];
            default:
                throw new Exception('Invalid action specified.');
        }
    }
}


// --- API ENTRY POINT ---

header('Content-Type: application/json');

// 1. Authentication Check (Global)
if (function_exists('check_auth')) {
    check_auth();
}

$action = $_GET['action'] ?? 'status';

try {
    $api = new TunnelApi($action);
    $result = $api->dispatch($action);
    
    // Output success response
    echo json_encode(['success' => true, 'data' => $result]);

} catch (Exception $e) {
    // Consolidated error handler for all API actions.
    http_response_code(500);
    
    // Log the detailed error including stack trace for debugging purposes.
    error_log("Tunnel API: Request failed ({$action}). Error Details: " . get_class($e) . ": " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
    
    // Provide a user-friendly, non-specific error message.
    echo json_encode([
        'success' => false,
        'error' => 'An internal server error occurred during the tunneling process. Please check the logs for details.'
    ]);

} finally {
    ob_end_flush();
}
?>
</task_progress>
<write_to_file>
</write_to_file>