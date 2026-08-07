<?php
/**
 * Nucleus - Projects API
 * Description: API for project management operations (Refactored)
 * Version: 3.2.0
 */

// Load configuration and helper functions
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * ProjectApi Class
 * Manages all project-related API actions, such as ignoring projects.
 */
class ProjectApi {
    private $prefsFile;

    public function __construct() {
        $this->prefsFile = __DIR__ . '/../data/preferences.json';
    }

    /**
     * Get the current list of ignored project names from dashboard preferences.
     */
    private function getIgnoredProjects() {
        if (!function_exists('getDashboardPreferences')) {
            return [];
        }
        $prefs = getDashboardPreferences();
        return $prefs['ignored_projects'] ?? [];
    }

    /**
     * Save the provided list of ignored projects to the preferences file.
     */
    private function saveIgnoredProjects($ignoredProjects) {
        if (!function_exists('getDashboardPreferences')) {
            return false;
        }

        $prefs = getDashboardPreferences();
        $prefs['ignored_projects'] = $ignoredProjects;

        // Ensure the data directory exists
        $prefsDir = dirname($this->prefsFile);
        if (!is_dir($prefsDir)) {
            @mkdir($prefsDir, 0755, true);
        }
        
        // Save preferences with pretty printing for readability
        return @file_put_contents($this->prefsFile, json_encode($prefs, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * Handles ignoring a project.
     */
    public function ignoreProject($projectName) {
        if (empty($projectName)) {
            throw new Exception('Project name is required.');
        }
        
        $ignoredProjects = $this->getIgnoredProjects();
        
        // Add to ignored list if not already there
        if (!in_array($projectName, $ignoredProjects)) {
            $ignoredProjects[] = $projectName;
            
            if ($this->saveIgnoredProjects($ignoredProjects)) {
                return [
                    'success' => true,
                    'message' => 'Project ignored successfully',
                    'ignored_projects' => $ignoredProjects
                ];
            } else {
                throw new Exception('Failed to save ignored projects list.');
            }
        } else {
            return [
                'success' => true,
                'message' => 'Project is already ignored',
                'ignored_projects' => $ignoredProjects
            ];
        }
    }

    /**
     * Handles unignoring a project.
     */
    public function unignoreProject($projectName) {
        if (empty($projectName)) {
            throw new Exception('Project name is required.');
        }
        
        $ignoredProjects = $this->getIgnoredProjects();
        
        // Remove from ignored list while maintaining key order consistency
        $newIgnoredProjects = array_values(array_filter($ignoredProjects, function($item) use ($projectName) {
            return $item !== $projectName;
        }));

        if ($this->saveIgnoredProjects($newIgnoredProjects)) {
            return [
                'success' => true,
                'message' => 'Project unignored successfully',
                'ignored_projects' => $newIgnoredProjects
            ];
        } else {
            throw new Exception('Failed to save ignored projects list.');
        }
    }

    /**
     * Gets the current list of all ignored projects.
     */
    public function listIgnored() {
        $ignoredProjects = $this->getIgnoredProjects();
        return [
            'success' => true,
            'ignored_projects' => $ignoredProjects
        ];
    }

    /**
     * Main dispatcher method to handle API requests.
     */
    public function dispatch($action, $data) {
        switch ($action) {
            case 'ignore':
                return $this->ignoreProject($data['project'] ?? '');
            case 'unignore':
                return $this->unignoreProject($data['project'] ?? '');
            case 'list_ignored':
                return $this->listIgnored();
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

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$data = json_decode(file_get_contents("php://input"), true) ?: $_POST;


try {
    $api = new ProjectApi();
    $result = $api->dispatch($action, $data);
    
    // Output success response
    echo json_encode(['success' => true, 'data' => $result]);

} catch (Exception $e) {
    // Consolidated error handler for all API actions.
    http_response_code(500);
    
    // Log the detailed error including stack trace for debugging purposes.
    error_log("Project API: Request failed ({$action}). Error Details: " . get_class($e) . ": " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
    
    // Provide a user-friendly, non-specific error message.
    echo json_encode([
        'success' => false,
        'error' => 'An internal server error occurred during project management. Please check the logs for details.'
    ]);

} finally {
    ob_end_flush();
}
?>
</task_progress>
<write_to_file>
</write_to_file>

</final_file_content>

IMPORTANT: For any future changes to this file, use the final_file_content shown above as your reference. This content reflects the current state of the file, including any auto-formatting (e.g., if you used single quotes but the formatter converted them to double quotes). Always base your SEARCH/REPLACE operations on this final version to ensure accuracy.


<environment_details>
# Visual Studio Code Visible Files
api/tunnel.php

# Visual Studio Code Open Tabs
api/tunnel.php

# Current Time
8/5/2026, 12:46:37 PM (Asia/Beirut, UTC+3:00)

# Context Window Usage
21,900 / 32.768K tokens used (66%)

# Current Mode
ACT MODE
</environment_details>