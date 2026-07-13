#!/usr/bin/env bash

set -euo pipefail

LIVE_DIR="${LIVE_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
APP_DIR="${APP_DIR:-$LIVE_DIR/core}"
BACKUP_BASE_DEFAULT="$(cd "$LIVE_DIR/.." && pwd)"

if [[ "$(basename "$BACKUP_BASE_DEFAULT")" == "www" ]]; then
    BACKUP_BASE_DEFAULT="$(cd "$BACKUP_BASE_DEFAULT/.." && pwd)"
fi

BACKUP_ROOT="${BACKUP_ROOT:-$BACKUP_BASE_DEFAULT/backups/$(basename "$LIVE_DIR")}"
FILES_RETENTION_DAYS="${FILES_RETENTION_DAYS:-7}"
DB_RETENTION_DAYS="${DB_RETENTION_DAYS:-30}"
LOG_RETENTION_DAYS="${LOG_RETENTION_DAYS:-30}"
TIMESTAMP="$(date '+%Y%m%d-%H%M%S')"
DATE_DIR="$(date '+%Y/%m')"

LOG_DIR="$BACKUP_ROOT/logs"
DB_DIR="$BACKUP_ROOT/database/$DATE_DIR"
FILES_DIR="$BACKUP_ROOT/files/$DATE_DIR"
TMP_DIR="$BACKUP_ROOT/tmp"
LOG_FILE="$LOG_DIR/backup.log"

mkdir -p "$LOG_DIR" "$DB_DIR" "$FILES_DIR" "$TMP_DIR"

exec >>"$LOG_FILE" 2>&1

echo
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Starting backup"

if [[ ! -f "$APP_DIR/.env" ]]; then
    echo "Missing env file: $APP_DIR/.env"
    exit 1
fi

env_value() {
    local key="$1"
    local line

    line="$(grep -E "^${key}=" "$APP_DIR/.env" | tail -n 1 || true)"
    line="${line#*=}"
    line="${line%\"}"
    line="${line#\"}"
    line="${line%\'}"
    line="${line#\'}"
    printf '%s' "$line"
}

DB_HOST="$(env_value DB_HOST)"
DB_PORT="$(env_value DB_PORT)"
DB_DATABASE="$(env_value DB_DATABASE)"
DB_USERNAME="$(env_value DB_USERNAME)"
DB_PASSWORD="$(env_value DB_PASSWORD)"

if [[ -z "$DB_HOST" || -z "$DB_PORT" || -z "$DB_DATABASE" || -z "$DB_USERNAME" ]]; then
    echo "Database credentials are incomplete in $APP_DIR/.env"
    exit 1
fi

DB_FILE="$DB_DIR/${DB_DATABASE}-${TIMESTAMP}.sql.gz"
FILES_FILE="$FILES_DIR/site-files-${TIMESTAMP}.tar.gz"

MYSQL_PWD="$DB_PASSWORD" /usr/bin/mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --single-transaction \
    --quick \
    --lock-tables=false \
    "$DB_DATABASE" | gzip -9 > "$DB_FILE"

tar \
    --exclude='./core/storage/logs/*' \
    --exclude='./core/storage/framework/cache/*' \
    --exclude='./core/storage/framework/sessions/*' \
    --exclude='./core/storage/framework/testing/*' \
    --exclude='./core/storage/framework/views/*' \
    --exclude='./backups/*' \
    -czf "$FILES_FILE" \
    -C "$LIVE_DIR" \
    .

find "$BACKUP_ROOT/database" -type f -name '*.sql.gz' -mtime +"$DB_RETENTION_DAYS" -delete
find "$BACKUP_ROOT/files" -type f -name '*.tar.gz' -mtime +"$FILES_RETENTION_DAYS" -delete
find "$BACKUP_ROOT/logs" -type f -name '*.log' -mtime +"$LOG_RETENTION_DAYS" -delete
find "$TMP_DIR" -mindepth 1 -mtime +1 -delete

echo "Database backup: $DB_FILE"
echo "Files backup: $FILES_FILE"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Backup completed"
