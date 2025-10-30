#!/bin/bash

# SwaedUAE Database Backup Script
# Automated daily database backups with 7-day retention

BACKUP_DIR="/var/www/swaeduae/swaeduae_laravel/storage/backups"
LOG_FILE="/var/www/swaeduae/swaeduae_laravel/storage/logs/backup.log"
DB_HOST="127.0.0.1"
DB_PORT="5432"
DB_USER="swaeduae_user"
DB_NAME="swaeduae_laravel"
RETENTION_DAYS=7

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"
mkdir -p "$(dirname "$LOG_FILE")"

# Date format for backup filename
DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_FILE="$BACKUP_DIR/swaeduae_laravel_$DATE.sql.gz"

# Function to log messages
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Start backup
log_message "Starting database backup..."

# Create database dump
export PGPASSWORD="SwaedUAE2024"
if pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" "$DB_NAME" 2>/dev/null | gzip > "$BACKUP_FILE"; then
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log_message "✓ Backup completed successfully: $BACKUP_FILE (Size: $BACKUP_SIZE)"
    
    # Send notification email
    echo "Database backup completed successfully.
    
Backup File: $BACKUP_FILE
Size: $BACKUP_SIZE
Date: $(date)
Server: $(hostname)
" | mail -s "SwaedUAE Database Backup - SUCCESS" admin@swaeduae.ae 2>/dev/null || true
else
    log_message "✗ Backup FAILED!"
    exit 1
fi

# Remove old backups (older than RETENTION_DAYS)
log_message "Cleaning up old backups (keeping last $RETENTION_DAYS days)..."
find "$BACKUP_DIR" -type f -name "swaeduae_laravel_*.sql.gz" -mtime +"$RETENTION_DAYS" -delete

# Log cleanup statistics
BACKUP_COUNT=$(find "$BACKUP_DIR" -type f -name "swaeduae_laravel_*.sql.gz" | wc -l)
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)
log_message "✓ Cleanup completed. Current backups: $BACKUP_COUNT, Total size: $TOTAL_SIZE"

log_message "Backup process completed successfully"
exit 0
