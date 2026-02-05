#!/bin/bash
# ===========================================
# Database Backup Script
# ===========================================
# Run on production server to backup MySQL database
#
# Usage:
#   ./scripts/backup.sh
#
# Cron example (daily at 3am):
#   0 3 * * * cd /opt/apps/workouts && ./scripts/backup.sh >> ./backups/backup.log 2>&1
#
# ===========================================

set -e

# Configuration
APP_PATH="${APP_PATH:-/opt/apps/workouts}"
BACKUP_DIR="${APP_PATH}/backups"
COMPOSE_FILE="${APP_PATH}/docker-compose.prod.yml"
ENV_FILE="${APP_PATH}/.env.docker.prod"
RETENTION_DAYS=7

# Load environment variables
if [ -f "$ENV_FILE" ]; then
    export $(grep -v '^#' "$ENV_FILE" | xargs)
fi

# Defaults if not set
DB_DATABASE="${DB_DATABASE:-workouts}"
DB_USERNAME="${DB_USERNAME:-workouts}"

# Timestamp for filename
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/${DB_DATABASE}_${TIMESTAMP}.sql.gz"

# Ensure backup directory exists
mkdir -p "$BACKUP_DIR"

echo "[$(date)] Starting backup of database: ${DB_DATABASE}"

# Create backup using docker-compose exec (uses service name, not container name)
docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T mysql \
    mysqldump \
    --user="${DB_USERNAME}" \
    --password="${DB_PASSWORD}" \
    --single-transaction \
    --routines \
    --triggers \
    --no-tablespaces \
    "${DB_DATABASE}" | gzip > "$BACKUP_FILE"

# Check if backup was created successfully
if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
    FILESIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "[$(date)] Backup created successfully: ${BACKUP_FILE} (${FILESIZE})"
else
    echo "[$(date)] ERROR: Backup failed!"
    exit 1
fi

# Remove old backups (older than RETENTION_DAYS)
echo "[$(date)] Cleaning up backups older than ${RETENTION_DAYS} days..."
find "$BACKUP_DIR" -name "*.sql.gz" -type f -mtime +${RETENTION_DAYS} -delete

# List remaining backups
echo "[$(date)] Current backups:"
ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null || echo "No backups found"

echo "[$(date)] Backup complete!"
