#!/usr/bin/env bash

set -euo pipefail

REPO_DIR="${REPO_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
LIVE_DIR="${LIVE_DIR:-$REPO_DIR}"
BRANCH="${DEPLOY_BRANCH:-main}"
LOG_BASE_DIR="$LIVE_DIR"

if [[ ! -d "$LOG_BASE_DIR/core/storage/logs" ]]; then
    LOG_BASE_DIR="$REPO_DIR"
fi

LOG_FILE="${DEPLOY_LOG_FILE:-$LOG_BASE_DIR/core/storage/logs/auto-deploy.log}"
DEPLOY_STATE_FILE="${DEPLOY_STATE_FILE:-$LIVE_DIR/.last_deployed_commit}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
RSYNC_BIN="${RSYNC_BIN:-rsync}"

mkdir -p "$(dirname "$LOG_FILE")"

exec >>"$LOG_FILE" 2>&1

echo
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Starting auto deploy"

cd "$REPO_DIR"

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    echo "Repository is not initialized in $REPO_DIR"
    exit 1
fi

git fetch origin "$BRANCH"

CURRENT_SHA="$(git rev-parse HEAD)"
TARGET_SHA="$(git rev-parse "origin/$BRANCH")"
LAST_DEPLOYED_SHA=""

if [[ -f "$DEPLOY_STATE_FILE" ]]; then
    LAST_DEPLOYED_SHA="$(tr -d '[:space:]' < "$DEPLOY_STATE_FILE")"
fi

if [[ "$CURRENT_SHA" == "$TARGET_SHA" ]]; then
    if [[ "$LIVE_DIR" == "$REPO_DIR" ]] || [[ "$LAST_DEPLOYED_SHA" == "$TARGET_SHA" && -f "$LIVE_DIR/core/artisan" ]]; then
        echo "No new commit. Current: $CURRENT_SHA"
        exit 0
    fi

    echo "No new git commit, but live sync is required for $TARGET_SHA"
else
    echo "Updating $CURRENT_SHA -> $TARGET_SHA"
    git reset --hard "$TARGET_SHA"
fi

if [[ "$LIVE_DIR" != "$REPO_DIR" ]]; then
    mkdir -p "$LIVE_DIR"

    "$RSYNC_BIN" -a \
        --exclude='.git/' \
        --exclude='.github/' \
        --exclude='install/' \
        --exclude='core/.env' \
        --exclude='core/vendor/' \
        --exclude='core/node_modules/' \
        --exclude='assets/admin/push_config.json' \
        "$REPO_DIR"/ "$LIVE_DIR"/
fi

rm -rf "$LIVE_DIR/install"

"$COMPOSER_BIN" install \
    --working-dir="$LIVE_DIR/core" \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

mkdir -p \
    "$LIVE_DIR/core/storage/framework/cache" \
    "$LIVE_DIR/core/storage/framework/sessions" \
    "$LIVE_DIR/core/storage/framework/testing" \
    "$LIVE_DIR/core/storage/framework/views" \
    "$LIVE_DIR/core/storage/logs" \
    "$LIVE_DIR/core/bootstrap/cache"

find "$LIVE_DIR/core/storage" -type d -exec chmod 775 {} +
find "$LIVE_DIR/core/storage" -type f -exec chmod 664 {} +
find "$LIVE_DIR/core/bootstrap/cache" -type d -exec chmod 775 {} +
find "$LIVE_DIR/core/bootstrap/cache" -type f -exec chmod 664 {} +

"$PHP_BIN" "$LIVE_DIR/core/artisan" migrate --force
"$PHP_BIN" "$LIVE_DIR/core/artisan" optimize:clear

printf '%s\n' "$TARGET_SHA" > "$DEPLOY_STATE_FILE"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deploy completed at $TARGET_SHA"
