#!/bin/bash

#############################################
# CarbonCraft Dashboard - cPanel Deployment Script
# This script should be placed on your cPanel server
# Location: /home/harshaln/deploy-to-cpanel.sh
#############################################

set -e  # Exit on any error

echo "================================"
echo "🚀 Starting CarbonCraft Dashboard Deployment"
echo "================================"
echo ""

# Configuration
REPO_PATH="/home/harshaln/repositories/carboncraft-dashboard"
APP_PATH="/home/harshaln/dashboard"
BACKUP_PATH="/home/harshaln/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print colored messages
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Step 1: Create backup
echo "📦 Step 1: Creating backup..."
mkdir -p "$BACKUP_PATH"
if [ -d "$APP_PATH" ]; then
    tar -czf "$BACKUP_PATH/dashboard_backup_$TIMESTAMP.tar.gz" \
        --exclude="$APP_PATH/vendor" \
        --exclude="$APP_PATH/node_modules" \
        --exclude="$APP_PATH/storage/logs" \
        "$APP_PATH" 2>/dev/null || print_warning "Backup creation had warnings"
    print_success "Backup created: dashboard_backup_$TIMESTAMP.tar.gz"
else
    print_warning "App path doesn't exist yet, skipping backup"
fi
echo ""

# Step 2: Navigate to repository and pull latest changes
echo "📥 Step 2: Pulling latest changes from repository..."
cd "$REPO_PATH"
git fetch origin
git reset --hard origin/main
print_success "Repository updated to latest version"
echo ""

# Step 3: Sync files to application directory (excluding sensitive files)
echo "🔄 Step 3: Syncing files to application directory..."
rsync -av --delete \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='database/database.sqlite' \
    "$REPO_PATH/" "$APP_PATH/"
print_success "Files synced successfully"
echo ""

# Step 4: Install/Update Composer dependencies
echo "📦 Step 4: Installing Composer dependencies..."
cd "$APP_PATH"
if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev --no-interaction
    print_success "Composer dependencies installed"
else
    print_warning "Composer not found, skipping dependency installation"
fi
echo ""

# Step 5: Run database migrations (optional - uncomment if needed)
echo "🗄️  Step 5: Running database migrations..."
# Uncomment the next line if you want to run migrations automatically
# php artisan migrate --force
print_warning "Migration step skipped (uncomment in script to enable)"
echo ""

# Step 6: Clear and rebuild caches
echo "🧹 Step 6: Clearing and rebuilding caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Caches cleared and rebuilt"
echo ""

# Step 7: Set proper permissions
echo "🔐 Step 7: Setting file permissions..."
chmod -R 755 "$APP_PATH/storage"
chmod -R 755 "$APP_PATH/bootstrap/cache"
chmod -R 755 "$APP_PATH/public/order_attachments"
print_success "Permissions set correctly"
echo ""

# Step 8: Verify deployment
echo "✨ Step 8: Verifying deployment..."
if [ -f "$APP_PATH/artisan" ]; then
    print_success "Deployment verified successfully"
else
    print_error "Deployment verification failed!"
    exit 1
fi
echo ""

# Clean up old backups (keep last 5)
echo "🗑️  Cleaning up old backups..."
cd "$BACKUP_PATH"
ls -t dashboard_backup_*.tar.gz 2>/dev/null | tail -n +6 | xargs -r rm --
print_success "Old backups cleaned up"
echo ""

echo "================================"
echo "✅ Deployment completed successfully!"
echo "================================"
echo ""
echo "📊 Deployment Summary:"
echo "   Repository: $REPO_PATH"
echo "   Application: $APP_PATH"
echo "   Backup: $BACKUP_PATH/dashboard_backup_$TIMESTAMP.tar.gz"
echo "   Time: $(date)"
echo ""
echo "🔗 Access your dashboard at: https://harshal-nerpagar.store/dashboard"
echo ""
