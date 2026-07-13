#!/usr/bin/env bash

set -euo pipefail

REPO_DIR="${REPO_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
BRANCH="${DEPLOY_BRANCH:-main}"
LOG_FILE="${DEPLOY_LOG_FILE:-$REPO_DIR/core/storage/logs/auto-deploy.log}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

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

if [[ "$CURRENT_SHA" == "$TARGET_SHA" ]]; then
    echo "No new commit. Current: $CURRENT_SHA"
    exit 0
fi

echo "Updating $CURRENT_SHA -> $TARGET_SHA"
git reset --hard "$TARGET_SHA"

rm -rf "$REPO_DIR/install"

"$COMPOSER_BIN" install \
    --working-dir="$REPO_DIR/core" \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

mkdir -p \
    "$REPO_DIR/core/storage/framework/cache" \
    "$REPO_DIR/core/storage/framework/sessions" \
    "$REPO_DIR/core/storage/framework/testing" \
    "$REPO_DIR/core/storage/framework/views" \
    "$REPO_DIR/core/storage/logs" \
    "$REPO_DIR/core/bootstrap/cache"

find "$REPO_DIR/core/storage" -type d -exec chmod 775 {} +
find "$REPO_DIR/core/storage" -type f -exec chmod 664 {} +
find "$REPO_DIR/core/bootstrap/cache" -type d -exec chmod 775 {} +
find "$REPO_DIR/core/bootstrap/cache" -type f -exec chmod 664 {} +

"$PHP_BIN" "$REPO_DIR/core/artisan" migrate --force
"$PHP_BIN" "$REPO_DIR/core/artisan" optimize:clear

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deploy completed at $TARGET_SHA"
