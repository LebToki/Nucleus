<?php

namespace Nucleus\Core\Services;

/**
 * GiteaBridge Class
 * Version: 1.0.0
 * Bridges Nucleus projects to a self-hosted Gitea instance.
 *   - Maps project git remotes to Gitea web URLs (no token required)
 *   - Queries the Gitea API for repo/PR data (requires GITEA_TOKEN)
 *   - Computes local ahead/behind vs origin (read-only, no fetch)
 */
class GiteaBridge {

    /**
     * Whether API-backed features are available.
     */
    public static function enabled(): bool {
        return defined('GITEA_TOKEN') && GITEA_TOKEN !== '';
    }

    /**
     * Base web URL of the configured Gitea instance.
     */
    public static function baseUrl(): string {
        return rtrim(defined('GITEA_URL') && GITEA_URL !== '' ? GITEA_URL : 'http://localhost:3000', '/');
    }

    /**
     * Call the Gitea API (read-only unless $method is not GET).
     */
    public static function api(string $path, string $method = 'GET', array $body = []): array {
        if (!self::enabled()) {
            return ['success' => false, 'error' => 'GITEA_TOKEN not configured'];
        }
        $url = self::baseUrl() . '/api/v1' . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Authorization: token ' . GITEA_TOKEN,
                'Accept: application/json',
            ],
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        $data = json_decode((string)$resp, true);
        if ($resp === false) {
            return ['success' => false, 'code' => 0, 'data' => [], 'error' => 'Gitea API unreachable: ' . ($curlErr ?: 'connection failed')];
        }
        return [
            'success' => $code >= 200 && $code < 300,
            'code'    => $code,
            'data'    => is_array($data) ? $data : [],
        ];
    }

    /**
     * Extract the hostname (no port, no IPv6 brackets) from an authority string.
     * Used to decide whether a git remote points at the configured Gitea host —
     * SSH and HTTP naturally use different ports on the same host, so only the
     * hostname is compared.
     */
    private static function authorityHost(string $authority): string {
        if (preg_match('/^\[([^\]]+)\]/', trim($authority), $m)) {
            return strtolower($m[1]);
        }
        return strtolower(trim(strtok(trim($authority), ':')));
    }

    /**
     * Parse a git remote URL (http/https, git@host:, ssh://git@host:).
     * Returns [owner, repo, web_url] only when the host matches GITEA_URL.
     */
    public static function repoFromRemote(?string $remote): ?array {
        if (!$remote) return null;
        $r = trim($remote);
        if ($r === '') return null;
        $expected = self::authorityHost((string)parse_url(self::baseUrl(), PHP_URL_HOST));

        $patterns = [
            '~^(?:https?://)(?:[^/@]+@)?(?<host>[^/:]+)(?::\d+)?/(?<owner>[^/]+)/(?<repo>[^/]+?)(?:\.git)?/?$~i',
            '~^git@(?<host>[^:]+):\d+/(?<owner>[^/]+)/(?<repo>[^/]+?)(?:\.git)?/?$~',
            '~^git@(?<host>[^:]+):(?<owner>[^/]+)/(?<repo>[^/]+?)(?:\.git)?/?$~',
            '~^ssh://git@(?<host>[^/]+)(?::\d+)?/(?<owner>[^/]+)/(?<repo>[^/]+?)(?:\.git)?/?$~i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $r, $m)) {
                if (self::authorityHost($m['host']) !== $expected) return null;
                return [
                    'owner'   => $m['owner'],
                    'repo'    => $m['repo'],
                    'web_url' => self::baseUrl() . '/' . $m['owner'] . '/' . $m['repo'],
                ];
            }
        }
        return null;
    }

    /**
     * Extract the bare hostname from any supported git remote URL form.
     */
    private static function remoteHost(string $remote): ?string {
        $r = trim($remote);
        if ($r === '') return null;
        if (preg_match('~^(?:ssh://|https?://)(?:[^/@]+@)?([^/:]+)~i', $r, $m)) return strtolower($m[1]);
        if (preg_match('~^git@([^:]+)~', $r, $m)) return strtolower($m[1]);
        return null;
    }

    /**
     * Extract the origin remote URL from a project's .git/config.
     * Prefers a remote that points at the configured Gitea host (so a project
     * with multiple remotes links correctly), then origin, then any remote.
     */
    public static function remoteFromPath(string $path): ?string {
        $configFile = $path . '/.git/config';
        if (!is_file($configFile)) return null;
        $config = @file_get_contents($configFile);
        if ($config === false) return null;
        if (!preg_match_all('/\[remote "([^"]+)"\][^\[]*?url\s*=\s*([^\n]+)/s', $config, $m, PREG_SET_ORDER)) {
            return null;
        }
        $expected = self::authorityHost((string)parse_url(self::baseUrl(), PHP_URL_HOST));
        $origin = null;
        $first = null;
        foreach ($m as $rm) {
            $name = trim($rm[1]);
            $url = trim($rm[2]);
            if ($first === null) $first = $url;
            if ($name === 'origin') $origin = $url;
            if (self::remoteHost($url) === $expected) return $url;
        }
        return $origin ?? $first;
    }

    /**
     * Branch + ahead/behind vs origin computed from LOCAL refs only.
     * No network, no fetch — non-destructive.
     */
    public static function gitHealth(string $path): array {
        $branch = 'unknown';
        $headFile = $path . '/.git/HEAD';
        if (is_file($headFile)) {
            $head = trim((string)@file_get_contents($headFile));
            if (str_starts_with($head, 'ref: refs/heads/')) {
                $branch = substr($head, 16);
            }
        }
        $ahead = 0;
        $behind = 0;
        $ref = 'origin/' . $branch;
        if (str_starts_with($branch, 'refs/')) {
            $ref = $branch;
        }
        $cmd = 'cd ' . escapeshellarg($path)
             . ' && git rev-list --left-right --count ' . escapeshellarg($ref) . '...HEAD 2>/dev/null';
        $out = trim((string)@shell_exec($cmd));
        if (preg_match('/^(\d+)\s+(\d+)$/', $out, $m)) {
            $behind = (int)$m[1];
            $ahead  = (int)$m[2];
        }
        return ['branch' => $branch, 'ahead' => $ahead, 'behind' => $behind];
    }

    /**
     * List repositories accessible via the configured token.
     */
    public static function listRepos(): array {
        return self::api('/user/repos?limit=50&sort=updated&order=desc');
    }

    /**
     * Open pull requests for a repo (owner/repo).
     */
    public static function openPullRequests(string $owner, string $repo): array {
        return self::api('/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo) . '/pulls?state=open&limit=20');
    }

    /**
     * Create a repository on Gitea.
     */
    public static function createRepo(string $name, string $description = '', bool $private = false, string $autoInit = 'README.md'): array {
        return self::api('/user/repos', 'POST', [
            'name'        => $name,
            'description' => $description,
            'private'     => $private,
            'auto_init'   => $autoInit,
        ]);
    }
}
