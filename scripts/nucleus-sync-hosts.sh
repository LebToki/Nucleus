#!/usr/bin/env bash
# Nucleus - hosts / vhosts sync service
#
# Keeps /etc/hosts and Apache vhosts (HTTP + HTTPS) in sync with the actual
# project directories under the Nucleus web root. Run as root (systemd timer
# or manually). Idempotent: safe to run repeatedly.
#
#   - Adds a <name>.local hosts entry + HTTP/SSL vhost for each project dir
#   - Prunes vhosts and hosts entries for projects that were removed/renamed
#   - Never touches infrastructure dirs or existing service/proxy vhosts
#
# Install:
#   sudo install -m 0755 scripts/nucleus-sync-hosts.sh /usr/local/bin/
#   sudo cp scripts/nucleus-sync.service scripts/nucleus-sync.timer /etc/systemd/system/
#   sudo systemctl daemon-reload && sudo systemctl enable --now nucleus-sync.timer

set -euo pipefail

WEBROOT="/var/www/html"
CERT="/home/zorin/.2ti/config/ssl/2ti_local.crt"
KEY="/home/zorin/.2ti/config/ssl/2ti_local.key"
HOSTS_FILE="/etc/hosts"
VHOSTS_DIR="/etc/apache2/sites-enabled"
DOMAIN_SUFFIX=".local"

log() { echo "[nucleus-sync] $*"; }

# Directories that are Nucleus infrastructure, never treated as projects.
INFRA="laragon laragon-dashboard dashboard assets build includes partials pages api i18n cache data logs phpmyadmin adminer phppgadmin html nucleus-logo node_modules vendor .git backups temp admin filemanager demo divorce scripts"

# Service / explicit vhosts (proxy or custom) that this script must never
# create or remove.
SERVICES="dashboard projects zorin-dashboard 2ti-orchestrator 2tinteractive eventbus comfyui voxcpm demucs wan n8n ollama gitea"

# --- discover project dirs -------------------------------------------------
PROJECTS=()
for d in "$WEBROOT"/*; do
    [ -e "$d" ] || continue
    [ -d "$d" ] || continue
    name="$(basename "$d")"
    case "$name" in .*) continue ;; esac
    case " $INFRA " in *" $name "*) continue ;; esac
    # Reasonable blockage: only plain [a-z0-9][a-z0-9-]* names become
    # vhosts/domains; a dir already named "<x>.local" would become
    # "<x>.local.local" and is refused.
    case "$name" in
        *[!a-z0-9-]*) continue ;;
        *local|*test) case "$name" in *.local|*.test) continue ;; esac ;;
    esac
    case "$name" in
        [a-z0-9]) ;;
        [a-z0-9]*[a-z0-9]) ;;
        *) continue ;;
    esac
    PROJECTS+=("$name")
done

# --- helpers ----------------------------------------------------------------
docroot() {
    local name="$1"
    if [ -f "$WEBROOT/$name/artisan" ] && [ -d "$WEBROOT/$name/public" ]; then
        echo "$WEBROOT/$name/public"
    else
        echo "$WEBROOT/$name"
    fi
}

protect() { # $1 = name -> 0 if protected (infra/service), 1 otherwise
    local name="$1"
    case " $INFRA $SERVICES " in *" $name "*) return 0 ;; esac
    return 1
}

is_service() { # $1 = name -> 0 if an existing service/proxy host
    local name="$1"
    case " $SERVICES " in *" $name "*) return 0 ;; esac
    return 1
}

in_projects() { # $1 = name -> 0 if project exists
    local name="$1"
    for p in "${PROJECTS[@]}"; do
        [ "$p" = "$name" ] && return 0
    done
    return 1
}

ensure_host_entry() {
    local domain="$1"
    if ! grep -Eq "^[0-9.]+[[:space:]]+${domain}([[:space:]]|$)" "$HOSTS_FILE"; then
        printf '127.0.0.1\t%s\n' "$domain" >>"$HOSTS_FILE"
        log "hosts: + $domain"
    fi
}

write_http_vhost() {
    local name="$1"
    local domain="${name}${DOMAIN_SUFFIX}"
    local root; root="$(docroot "$name")"
    local file="$VHOSTS_DIR/${domain}.conf"
    cat >"$file" <<EOF
# Nucleus auto-generated vhost for ${name}
<VirtualHost *:80>
    ServerName ${domain}
    DocumentRoot "${root}"
    <Directory "${root}">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/${name}-error.log
    CustomLog \${APACHE_LOG_DIR}/${name}-access.log combined
</VirtualHost>
EOF
    log "vhost: + ${domain}.conf"
}

write_ssl_vhost() {
    local name="$1"
    local domain="${name}${DOMAIN_SUFFIX}"
    local root; root="$(docroot "$name")"
    local file="$VHOSTS_DIR/${domain}_ssl.conf"
    cat >"$file" <<EOF
# 2TI Auto-Generated HTTPS VirtualHost: ${domain}
<VirtualHost *:443>
    ServerName ${domain}
    ServerAlias www.${domain}
    DocumentRoot ${root}

    SSLEngine on
    SSLCertificateFile ${CERT}
    SSLCertificateKeyFile ${KEY}

    <Directory ${root}>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/${domain}_ssl_error.log
    CustomLog \${APACHE_LOG_DIR}/${domain}_ssl_access.log combined
</VirtualHost>
EOF
    log "vhost: + ${domain}_ssl.conf"
}

# --- 1. ensure hosts entries + vhosts for current projects ------------------
for name in "${PROJECTS[@]}"; do
    ensure_host_entry "${name}${DOMAIN_SUFFIX}"
    if is_service "$name"; then
        continue
    fi
    write_http_vhost "$name"
    write_ssl_vhost "$name"
done

# --- 2. prune stale vhosts --------------------------------------------------
changed=""
for file in "$VHOSTS_DIR"/*.conf; do
    [ -f "$file" ] || continue
    base="$(basename "$file" .conf)"
    # only auto-generated style names: <name>.local / <name>.local_ssl
    case "$base" in
        *_ssl) name="${base%_ssl}" ;;
        *)     name="$base" ;;
    esac
    case "$name" in *.local|*.test) ;; *) continue ;; esac
    name="${name%.local}"; name="${name%.test}"
    [ -z "$name" ] && continue
    if is_service "$name" || in_projects "$name"; then
        continue
    fi
    rm -f "$file"
    log "vhost: - $base.conf (project removed)"
    changed="yes"
done

# --- 3. prune stale hosts entries ------------------------------------------
tmp="$(mktemp)"
trap 'rm -f "$tmp"' EXIT
: >"$tmp"
while IFS= read -r line; do
    case "$line" in
        \#*) printf '%s\n' "$line" >>"$tmp" ;;
        *)
            host="$(printf '%s\n' "$line" | awk '{print $2}')"
            case "$host" in
                *.local|*.test)
                    name="${host%.local}"; name="${name%.test}"
                    if is_service "$name" || in_projects "$name"; then
                        printf '%s\n' "$line" >>"$tmp"
                    else
                        log "hosts: - $host"
                        changed="yes"
                    fi
                    ;;
                *) printf '%s\n' "$line" >>"$tmp" ;;
            esac
            ;;
    esac
done <"$HOSTS_FILE"
if [ -n "$changed" ]; then
    cat "$tmp" >"$HOSTS_FILE"
fi

# --- 4. reload apache --------------------------------------------------------
if systemctl is-active --quiet apache2; then
    if apache2ctl configtest >/dev/null 2>&1; then
        systemctl reload apache2
        log "apache: reloaded"
    else
        log "apache: configtest FAILED — not reloading"
        apache2ctl configtest 2>&1 || true
        exit 1
    fi
fi
log "done"
