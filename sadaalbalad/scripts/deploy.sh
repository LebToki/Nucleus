#!/bin/bash
# ============================================================================
# deploy.sh — Sync local project to remote production server
#
# Uses rsync over SSH: only changed files are transferred (like an intelligent
# FTP that never misses a file).  Environment-specific files (.env, vendor/,
# etc.) are excluded so the remote keeps its own configuration.
#
# Usage:
#   ./scripts/deploy.sh            # sync code only
#   ./scripts/deploy.sh --migrate  # sync code + run migrations
#   ./scripts/deploy.sh --help     # show configuration
#
# Configuration via environment variables:
#   DEPLOY_HOST   Remote host (default: fr-int-web1582)
#   DEPLOY_USER   Remote user (default: u562928360)
#   DEPLOY_PATH   Remote project path (default: ~/public_html/demo/sadaalbalad)
#   DEPLOY_EXTRA  Extra rsync flags (optional)
# ============================================================================

set -euo pipefail

# --- Configuration (override with env vars) ----------------------------------
# The 2tinteractive host alias is defined in ~/.ssh/config with the correct
# hostname (92.113.24.9), port (65002), and key (~/.ssh/2tinteractive_ed25519).
# Using the alias means rsync inherits all those SSH settings automatically.
REMOTE_HOST="${DEPLOY_HOST:-2tinteractive}"
REMOTE_USER="${DEPLOY_USER:-u562928360}"
REMOTE_PATH="${DEPLOY_PATH:-/home/u562928360/domains/2tinteractive.com/public_html/demo/sadaalbalad}"
EXTRA_FLAGS="${DEPLOY_EXTRA:-}"

# --- Show config and exit ----------------------------------------------------
if [[ "${1:-}" == "--help" ]]; then
    cat <<EOF
Deploy configuration:
  Host:     $REMOTE_HOST
  User:     $REMOTE_USER
  Path:     $REMOTE_PATH

Usage:
  ./scripts/deploy.sh            # sync code only
  ./scripts/deploy.sh --migrate  # sync code + run migrations
  DEPLOY_HOST=other ./scripts/deploy.sh  # override host

Excluded (never synced):
  .env*, vendor/, node_modules/, storage/framework/cache/**, *.log
EOF
    exit 0
fi

RUN_MIGRATE=false
if [[ "${1:-}" == "--migrate" ]]; then
    RUN_MIGRATE=true
fi

# --- Resolve script directory ------------------------------------------------
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

echo "🚀 Deploying from $PROJECT_ROOT"
echo "   → $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH"

# --- Sync with rsync (only changed files) ------------------------------------
# Key flags:
#   -a  archive mode (preserves permissions, timestamps, etc.)
#   -v  verbose (show what's synced)
#   -z  compress during transfer
#   --delete  remove files on remote that no longer exist locally
#   --exclude  protect environment-specific files

rsync -avz -e ssh --delete $EXTRA_FLAGS \
    --exclude="vendor/" \
    --exclude="node_modules/" \
    --exclude=".env" \
    --exclude=".env.*" \
    --exclude=".env.example" \
    --exclude=".env.production" \
    --exclude=".env.backup" \
    --exclude=".phpunit.result.cache" \
    --exclude=".phpunit.cache/" \
    --exclude=".phpactor.json" \
        --exclude=".codex/" \
    --exclude=".cursor/" \
    --exclude=".idea/" \
    --exclude=".vscode/" \
    --exclude=".zed/" \
    --exclude=".kilo/" \
    --exclude=".claude/" \
    --exclude=".agents/" \
    --exclude="__pycache__/" \
    --exclude="*.pyc" \
    --exclude="*.log" \
    --exclude=".DS_Store" \
    --exclude="Thumbs.db" \
    --exclude=".git/" \
    --exclude="scripts/deploy.sh" \
    --exclude="_deploy_upload_*" \
    --exclude="_local_build_" \
    --exclude="_local_package_" \
    --exclude="storage/framework/cache/data/**" \
    --exclude="storage/framework/cache/views/**" \
    --exclude="storage/framework/sessions/**" \
    --exclude="storage/framework/logs/*.log" \
    --exclude="storage/app/public/" \
    --exclude="storage/app/public/*" \
    --exclude="bootstrap/cache/*.php" \
    "$PROJECT_ROOT/" \
    "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/"

echo "✅ Code sync complete."

# --- Post-deploy on remote ---------------------------------------------------
# Clear caches so the remote picks up new config/views/routes immediately.
# These are safe to run every deploy — Laravel regenerates them from source.

echo "🔧 Clearing remote caches..."
ssh "$REMOTE_USER@$REMOTE_HOST" "
    cd '$REMOTE_PATH' &&
    php artisan config:clear 2>/dev/null || true &&
    php artisan optimize:clear 2>/dev/null || true &&
    php artisan view:clear 2>/dev/null || true &&
    php artisan route:clear 2>/dev/null || true
"

if [[ "$RUN_MIGRATE" == "true" ]]; then
    echo "📊 Running migrations on remote..."
    ssh "$REMOTE_USER@$REMOTE_HOST" "
        cd '$REMOTE_PATH' &&
        php artisan migrate --force
    "
    echo "✅ Migrations complete."
fi

echo "🎉 Deploy finished! Check $REMOTE_PATH"
