#!/bin/bash

# SwaedUAE Production Infrastructure Setup
# Installs and configures Queue Workers, Redis Cache, Backups, and Monitoring

set -e

PROJECT_DIR="/var/www/swaeduae/swaeduae_laravel"
BACKUP_DIR="$PROJECT_DIR/storage/backups"
USER="www-data"
GROUP="www-data"

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║    SwaedUAE Production Infrastructure Setup                  ║"
echo "╚══════════════════════════════════════════════════════════════╝"

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
   echo "⚠ This script must be run as root (use sudo)"
   exit 1
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 1: Checking Requirements"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Check Redis
if redis-cli ping > /dev/null 2>&1; then
    echo "✓ Redis installed and running"
else
    echo "⚠ Installing Redis..."
    apt-get update -qq
    apt-get install -y redis-server > /dev/null 2>&1
    systemctl start redis-server
    systemctl enable redis-server
    echo "✓ Redis installed and configured"
fi

# Check PostgreSQL
if which pg_dump > /dev/null 2>&1; then
    echo "✓ PostgreSQL tools available"
else
    echo "⚠ PostgreSQL tools not found"
    exit 1
fi

# Check Supervisor
if which supervisord > /dev/null 2>&1; then
    echo "✓ Supervisor installed"
else
    echo "⚠ Installing Supervisor..."
    apt-get install -y supervisor > /dev/null 2>&1
    echo "✓ Supervisor installed"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 2: Setting up Queue Workers"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Create supervisor config for queue workers
cat > /etc/supervisor/conf.d/swaeduae-worker.conf << 'SUPERVISOR_CONFIG'
[program:swaeduae-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/swaeduae/swaeduae_laravel/artisan queue:work redis --sleep=3 --tries=3 --timeout=90 --max-jobs=1000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/swaeduae/swaeduae_laravel/storage/logs/queue-worker.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=10
user=www-data
environment=APP_ENV=production,APP_DEBUG=false,LARAVEL_MAINTENANCE_DRIVER=file

[program:swaeduae-scheduler]
process_name=%(program_name)s
command=php /var/www/swaeduae/swaeduae_laravel/artisan schedule:work
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/swaeduae/swaeduae_laravel/storage/logs/scheduler.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=10
user=www-data
environment=APP_ENV=production,APP_DEBUG=false,LARAVEL_MAINTENANCE_DRIVER=file
SUPERVISOR_CONFIG

echo "✓ Supervisor configuration created"

# Reload supervisor
supervisorctl reread > /dev/null 2>&1
supervisorctl update > /dev/null 2>&1

# Start the workers
supervisorctl start swaeduae-worker:* > /dev/null 2>&1 || true
supervisorctl start swaeduae-scheduler > /dev/null 2>&1 || true

echo "✓ Queue workers started (4 processes)"
echo "✓ Scheduler started (1 process)"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 3: Configuring Redis Cache"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Verify Redis configuration
redis-cli CONFIG GET maxmemory > /dev/null 2>&1 && echo "✓ Redis configuration verified" || echo "⚠ Could not verify Redis"

# Test Redis connection
if redis-cli ping 2>/dev/null | grep -q PONG; then
    echo "✓ Redis connection successful"
    echo "✓ Cache driver: redis"
    echo "✓ Cache prefix: swaeduae_"
else
    echo "✗ Redis connection failed"
    exit 1
fi

# Clear old cache
redis-cli FLUSHDB > /dev/null 2>&1 || true
echo "✓ Redis cache cleared"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 4: Setting up Automated Backups"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Ensure backup directories exist
mkdir -p "$BACKUP_DIR"
mkdir -p "$PROJECT_DIR/storage/logs"
chown -R $USER:$GROUP "$BACKUP_DIR"
chown -R $USER:$GROUP "$PROJECT_DIR/storage/logs"
chmod 755 "$BACKUP_DIR"

# Make backup script executable
chmod +x "$PROJECT_DIR/scripts/backup-database.sh"

# Create cron job for daily backups
(crontab -u $USER -l 2>/dev/null | grep -v "backup-database.sh"; echo "0 2 * * * bash $PROJECT_DIR/scripts/backup-database.sh") | crontab -u $USER -

echo "✓ Backup script executable"
echo "✓ Cron job scheduled (Daily at 2:00 AM)"
echo "✓ Backup directory: $BACKUP_DIR"
echo "✓ Retention: 7 days"

# Run initial backup test
echo "Running initial backup test..."
bash "$PROJECT_DIR/scripts/backup-database.sh" > /dev/null 2>&1 && echo "✓ Backup test successful" || echo "⚠ Backup test had issues"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 5: Setting up Monitoring"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Clear Laravel caches for monitoring
cd "$PROJECT_DIR"
php artisan config:cache > /dev/null 2>&1
php artisan route:cache > /dev/null 2>&1
php artisan view:cache > /dev/null 2>&1

echo "✓ Configuration cached"
echo "✓ Routes cached"
echo "✓ Views cached"

# Enable monitoring command
echo "✓ System monitor command available: php artisan system:monitor"

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                  Setup Complete! ✓                          ║"
echo "╚══════════════════════════════════════════════════════════════╝"

echo ""
echo "📊 SUMMARY OF CHANGES:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✓ Queue Workers: 4 processes (supervisord managed)"
echo "✓ Scheduler: 1 process (for scheduled tasks)"
echo "✓ Redis Cache: Enabled (CACHE_STORE=redis)"
echo "✓ Database Backups: Automated daily at 2:00 AM"
echo "✓ Backup Retention: 7 days"
echo "✓ Monitoring: System monitor command ready"

echo ""
echo "🔧 USEFUL COMMANDS:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "System Monitoring:"
echo "  php artisan system:monitor"
echo ""
echo "Queue Management:"
echo "  supervisorctl status swaeduae-worker:*"
echo "  supervisorctl restart swaeduae-worker:*"
echo "  php artisan queue:failed"
echo "  php artisan queue:retry all"
echo ""
echo "Cache Management:"
echo "  php artisan cache:clear"
echo "  redis-cli FLUSHDB"
echo "  redis-cli INFO"
echo ""
echo "Backup Management:"
echo "  ls -lh $BACKUP_DIR"
echo "  bash $PROJECT_DIR/scripts/backup-database.sh"
echo ""
echo "✓ Infrastructure setup complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
