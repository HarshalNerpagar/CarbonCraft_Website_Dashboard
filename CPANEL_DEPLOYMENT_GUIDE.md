# CarbonCraft Dashboard - Complete cPanel Deployment Guide

**Last Updated**: 2025-10-08
**Dashboard URL**: https://dashboard.carboncraft.in
**Database**: harshaln_carboncraft_dashboard

---

## 📋 Table of Contents

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Step 1: Prepare Files Locally](#step-1-prepare-files-locally)
3. [Step 2: Setup Subdomain in cPanel](#step-2-setup-subdomain-in-cpanel)
4. [Step 3: Upload Files to cPanel](#step-3-upload-files-to-cpanel)
5. [Step 4: Configure File Permissions](#step-4-configure-file-permissions)
6. [Step 5: Run Database Migrations](#step-5-run-database-migrations)
7. [Step 6: Optimize for Production](#step-6-optimize-for-production)
8. [Step 7: Configure SSL Certificate](#step-7-configure-ssl-certificate)
9. [Step 8: Test the Dashboard](#step-8-test-the-dashboard)
10. [Troubleshooting](#troubleshooting)

---

## Pre-Deployment Checklist

Before starting, ensure you have:

- ✅ **cPanel Login**: Username and password
- ✅ **Database Created**: `harshaln_carboncraft_dashboard`
- ✅ **Database User**: `harshaln_carboncraft_dashboard_user`
- ✅ **Database Password**: `Carboncraft@333`
- ✅ **Domain/Subdomain**: `dashboard.carboncraft.in`
- ✅ **SSH Access**: (Recommended for migrations)
- ✅ **FTP/File Manager Access**: For uploading files
- ✅ **.env file**: Already configured with database credentials

---

## STEP 1: Prepare Files Locally

### 1.1 Clean Up Unnecessary Files

Delete these files/folders before uploading (they're not needed on production):

```
❌ node_modules/          (Will regenerate on server)
❌ .git/                  (Git repository)
❌ __MACOSX/              (Mac system files)
❌ .DS_Store              (Mac system files)
❌ Install.zip            (Installation package)
❌ Update.zip             (Update package)
❌ tests/                 (Testing files - optional to keep)
❌ .env.example           (Keep .env only)
❌ .gitignore             (Not needed)
❌ .gitattributes         (Not needed)
❌ storage/logs/*.log     (Old log files)
```

**How to clean:**

**Option A: Manually** (Use Finder on Mac)
1. Navigate to `/Users/king/Documents/Website_Dashboard/Laravel_Dashboard/Source Code/`
2. Delete the folders/files listed above
3. Keep only essential Laravel files

**Option B: Using Terminal** (Faster)
```bash
cd "/Users/king/Documents/Website_Dashboard/Laravel_Dashboard/Source Code"

# Remove unnecessary files
rm -rf node_modules
rm -rf .git
rm -rf __MACOSX
rm -f .DS_Store
rm -f Install.zip
rm -f Update.zip
rm -f .gitignore
rm -f .gitattributes
rm -f storage/logs/*.log

# Clear cache files
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
```

### 1.2 Create ZIP Archive

**Option A: Using Terminal** (Recommended)
```bash
cd "/Users/king/Documents/Website_Dashboard/Laravel_Dashboard"

# Create a clean zip file
zip -r carboncraft-dashboard.zip "Source Code" -x "*.DS_Store" "__MACOSX/*"
```

**Option B: Using Finder**
1. Right-click on "Source Code" folder
2. Select "Compress Source Code"
3. Rename the ZIP to `carboncraft-dashboard.zip`

This will create a ZIP file ready for upload to cPanel.

---

## STEP 2: Setup Subdomain in cPanel

### 2.1 Create Subdomain

1. **Login to cPanel**
   - URL: Usually `https://yourdomain.com:2083` or `https://yourdomain.com/cpanel`
   - Enter your cPanel username and password

2. **Navigate to Domains Section**
   - Find and click **"Domains"** or **"Subdomains"**

3. **Create New Subdomain**
   - **Subdomain**: `dashboard`
   - **Domain**: Select `carboncraft.in` from dropdown
   - **Document Root**: It will auto-suggest `public_html/dashboard`

   ⚠️ **IMPORTANT**: Change this to:
   ```
   public_html/dashboard_public
   ```

   Why? We'll upload Laravel files to a private folder, and only expose the `public` directory.

4. **Click Create**

5. **Verify Subdomain Created**
   - You should see `dashboard.carboncraft.in` in the subdomain list

### 2.2 Note Your cPanel File Structure

After creating subdomain, your cPanel directory structure will look like:

```
/home/harshaln/                          ← Your cPanel username
├── public_html/                         ← Main website root
│   ├── dashboard_public/                ← Subdomain public folder (created)
│   └── index.html                       ← Main site (if any)
│
├── dashboard/                           ← We'll create this (Laravel root - PRIVATE)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                          ← Laravel's public folder
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
│
└── other folders...
```

---

## STEP 3: Upload Files to cPanel

### 3.1 Access File Manager

1. **Login to cPanel**
2. Click **"File Manager"**
3. You'll see your home directory (usually `/home/your_username/`)

### 3.2 Create Laravel Root Directory

1. In File Manager, navigate to your **home directory** (root, not public_html)
2. Click **"+ Folder"** button
3. Create folder named: `dashboard`
4. Click **"Create New Folder"**

### 3.3 Upload ZIP File

**Method 1: Using File Manager (Recommended for most users)**

1. Navigate to the `dashboard` folder you just created
2. Click **"Upload"** button (top menu)
3. Click **"Select File"**
4. Browse and select `carboncraft-dashboard.zip` from your computer
5. Wait for upload to complete (may take 5-15 minutes depending on size)
6. Once uploaded, close the upload window

**Method 2: Using FTP Client (FileZilla)**

1. Download FileZilla: https://filezilla-project.org/
2. Connect using:
   - **Host**: `ftp.carboncraft.in` or your server IP
   - **Username**: Your cPanel username
   - **Password**: Your cPanel password
   - **Port**: 21
3. Navigate to `/home/your_username/dashboard/`
4. Upload `carboncraft-dashboard.zip`

### 3.4 Extract ZIP File

1. Back in File Manager, navigate to `/home/your_username/dashboard/`
2. **Right-click** on `carboncraft-dashboard.zip`
3. Select **"Extract"**
4. A popup will show: "Extract to: /home/your_username/dashboard/"
5. Click **"Extract File(s)"**
6. Wait for extraction (1-3 minutes)

### 3.5 Move Files from Subfolder

After extraction, you'll likely have:
```
/home/your_username/dashboard/
└── Source Code/
    ├── app/
    ├── public/
    └── ...
```

We need to move everything inside "Source Code" directly to `dashboard/`:

1. Navigate to `/home/your_username/dashboard/Source Code/`
2. Click **"Select All"** (checkbox at top)
3. Click **"Move"** button
4. In the popup, change path to:
   ```
   /home/your_username/dashboard/
   ```
5. Click **"Move File(s)"**
6. Go back to `/home/your_username/dashboard/`
7. Delete the now-empty `Source Code` folder
8. Delete `carboncraft-dashboard.zip` (no longer needed)

### 3.6 Setup Public Folder

Now we need to move Laravel's `public` folder contents to the subdomain's public directory:

1. Navigate to `/home/your_username/dashboard/public/`
2. **Select All** files inside (index.php, .htaccess, css, js, etc.)
3. Click **"Copy"** or **"Move"**
4. Set destination to:
   ```
   /home/your_username/public_html/dashboard_public/
   ```
5. Click **"Copy File(s)"** or **"Move File(s)"**

**Your final structure should be:**
```
/home/your_username/
├── dashboard/                    ← Laravel root (PRIVATE)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                   ← Original public folder (can keep or delete)
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
│
└── public_html/
    └── dashboard_public/         ← Publicly accessible
        ├── index.php             ← Laravel entry point
        ├── .htaccess
        ├── css/
        ├── js/
        └── assets/
```

---

## STEP 4: Configure File Permissions

### 4.1 Set Correct Permissions

**Using File Manager:**

1. Navigate to `/home/your_username/dashboard/storage/`
2. **Right-click** on `storage` folder
3. Select **"Change Permissions"**
4. Set to: **755** (or check: Read, Write, Execute for Owner; Read, Execute for Group and Public)
5. **✓ Check**: "Recurse into subdirectories"
6. Click **"Change Permissions"**

7. Repeat for `/home/your_username/dashboard/bootstrap/cache/`:
   - Right-click `cache` folder
   - Change Permissions → 755
   - Recurse into subdirectories

**Using SSH (Recommended if you have access):**

```bash
cd /home/your_username/dashboard

# Set storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Set ownership (replace 'your_username' with your actual cPanel username)
chown -R your_username:your_username storage
chown -R your_username:your_username bootstrap/cache
```

### 4.2 Update index.php Path

We need to update the `index.php` file in the public directory to point to our private Laravel installation:

1. Navigate to `/home/your_username/public_html/dashboard_public/`
2. **Right-click** on `index.php`
3. Select **"Edit"** or **"Code Editor"**
4. Find these lines (around line 17-18):

```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

5. **Change them to:**

```php
require __DIR__.'/../../dashboard/vendor/autoload.php';
$app = require_once __DIR__.'/../../dashboard/bootstrap/app.php';
```

6. Click **"Save Changes"**

### 4.3 Update .htaccess (if needed)

Check `/home/your_username/public_html/dashboard_public/.htaccess`:

It should have:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

If it's missing or different, create/update it with the above content.

---

## STEP 5: Run Database Migrations

### 5.1 Verify .env Configuration

1. Navigate to `/home/your_username/dashboard/`
2. Right-click `.env` file → **Edit**
3. Verify these settings:

```env
APP_NAME="CarbonCraft Dashboard"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dashboard.carboncraft.in

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=harshaln_carboncraft_dashboard
DB_USERNAME=harshaln_carboncraft_dashboard_user
DB_PASSWORD=Carboncraft@333
```

4. Save if you made changes

### 5.2 Run Migrations via SSH

**Option A: Using Terminal/SSH (Recommended)**

1. **Connect via SSH**:
   ```bash
   ssh your_username@carboncraft.in
   # Or use the server IP:
   # ssh your_username@your.server.ip.address
   ```

2. **Navigate to Laravel directory**:
   ```bash
   cd ~/dashboard
   # Or full path:
   # cd /home/your_username/dashboard
   ```

3. **Test database connection**:
   ```bash
   php artisan migrate:status
   ```

   If you get an error, check your .env settings.

4. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

   The `--force` flag is needed for production environments.

5. **Seed initial data** (if needed):
   ```bash
   php artisan db:seed --force
   ```

**Option B: Using cPanel Terminal (if available)**

1. In cPanel, find **"Terminal"** under "Advanced" section
2. Click to open terminal
3. Run the same commands as Option A above

**Option C: Manual Migration via phpMyAdmin (Last Resort)**

If you don't have SSH access:

1. In cPanel, open **"phpMyAdmin"**
2. Select database: `harshaln_carboncraft_dashboard`
3. Click **"Import"**
4. You'll need to export the SQL from your local environment first

**To export SQL locally:**
```bash
cd "/Users/king/Documents/Website_Dashboard/Laravel_Dashboard/Source Code"

# Run migrations locally first
php artisan migrate

# Export the database structure
# This requires local MySQL/SQLite access
```

---

## STEP 6: Optimize for Production

### 6.1 Clear and Cache Configuration

**Via SSH:**

```bash
cd ~/dashboard

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

**If composer not found:**
```bash
# Check if composer is installed
which composer

# If not found, you may need to use full path or install composer
curl -sS https://getcomposer.org/installer | php
php composer.phar install --optimize-autoloader --no-dev
```

### 6.2 Set Production Environment

Verify `.env` has:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dashboard.carboncraft.in
```

### 6.3 Generate Application Key (if needed)

If your `.env` doesn't have `APP_KEY` or it's empty:

```bash
php artisan key:generate --force
```

---

## STEP 7: Configure SSL Certificate

### 7.1 Install SSL via cPanel

1. **Login to cPanel**
2. Find **"SSL/TLS Status"** or **"Let's Encrypt SSL"**
3. Look for `dashboard.carboncraft.in` in the domain list
4. Click **"Run AutoSSL"** or **"Issue"** button
5. Wait for certificate to be issued (1-5 minutes)
6. Verify status shows **"Certificate Installed"**

### 7.2 Force HTTPS Redirect

Update `.htaccess` in `/home/your_username/public_html/dashboard_public/`:

Add this at the **TOP** of the file (before existing code):

```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Rest of your existing .htaccess code below...
```

---

## STEP 8: Test the Dashboard

### 8.1 Access the Dashboard

Open your browser and navigate to:
```
https://dashboard.carboncraft.in
```

### 8.2 Expected Results

✅ **Success**: You should see the Laravel application login page or dashboard

❌ **If you see errors**, check the [Troubleshooting](#troubleshooting) section below

### 8.3 Create Admin User (if needed)

If you need to create an admin user:

**Via SSH:**
```bash
cd ~/dashboard
php artisan tinker
```

Then in tinker:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@carboncraft.in';
$user->password = bcrypt('YourSecurePassword123');
$user->save();
exit
```

Or use database seeder if you have one.

### 8.4 Test Key Features

1. ✅ **Login**: Try logging in with admin credentials
2. ✅ **Navigation**: Click through all menu items
3. ✅ **Database**: Create a test order to verify database works
4. ✅ **File Upload**: Test file upload functionality
5. ✅ **Reports**: Generate a sample report

---

## Troubleshooting

### Error: "500 Internal Server Error"

**Cause**: Usually permissions or .env configuration

**Fix**:
1. Check storage permissions: `chmod -R 755 storage bootstrap/cache`
2. Check `.env` file exists and is readable
3. Check error logs in cPanel or `/home/your_username/dashboard/storage/logs/laravel.log`

**View Laravel Logs:**
```bash
tail -50 ~/dashboard/storage/logs/laravel.log
```

---

### Error: "403 Forbidden"

**Cause**: Directory permissions or missing index.php

**Fix**:
1. Verify `index.php` exists in `/public_html/dashboard_public/`
2. Check folder permissions: should be 755
3. Check `.htaccess` file exists

---

### Error: "Database Connection Failed"

**Cause**: Wrong credentials or database not created

**Fix**:
1. Verify database exists in **cPanel → MySQL Databases**
2. Check database user has privileges
3. Test connection:
   ```bash
   cd ~/dashboard
   php artisan migrate:status
   ```
4. Update `.env` with correct credentials

---

### Error: "Base table or view not found"

**Cause**: Migrations not run

**Fix**:
```bash
cd ~/dashboard
php artisan migrate --force
```

---

### Blank Page / White Screen

**Cause**: PHP errors with display_errors off

**Fix**:
1. Temporarily enable debug mode in `.env`:
   ```env
   APP_DEBUG=true
   ```
2. Refresh page to see actual error
3. Fix the error
4. Set `APP_DEBUG=false` again

---

### CSS/JS Not Loading

**Cause**: Asset paths incorrect or permissions

**Fix**:
1. Check `/public_html/dashboard_public/css/` and `/public_html/dashboard_public/js/` exist
2. Run: `php artisan storage:link`
3. Clear cache: `php artisan cache:clear`

---

### "419 Page Expired" on Forms

**Cause**: Session configuration issue

**Fix**:
1. Check `.env`:
   ```env
   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   ```
2. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```

---

## Post-Deployment Checklist

After successful deployment:

- [ ] Dashboard accessible at https://dashboard.carboncraft.in
- [ ] SSL certificate installed and active
- [ ] Login working
- [ ] Database operations working
- [ ] File uploads working
- [ ] All pages loading correctly
- [ ] No console errors (check browser DevTools)
- [ ] APP_DEBUG=false in .env
- [ ] Backed up .env file securely
- [ ] Documented admin credentials securely
- [ ] Setup regular backups (cPanel → Backups)

---

## Regular Maintenance

### Daily
- Monitor error logs: `tail -f ~/dashboard/storage/logs/laravel.log`

### Weekly
- Backup database (cPanel → phpMyAdmin → Export)
- Check storage usage

### Monthly
- Update Laravel dependencies: `composer update`
- Review user access logs
- Check SSL certificate expiry

---

## Support Resources

### Laravel Documentation
- https://laravel.com/docs

### cPanel Documentation
- Your hosting provider's knowledge base

### Emergency Rollback
If something breaks:
1. Restore database backup from cPanel
2. Restore files from cPanel backup
3. Contact your hosting support

---

## Summary of File Paths

| Description | cPanel Path |
|-------------|------------|
| Laravel Root (Private) | `/home/your_username/dashboard/` |
| Public Web Root | `/home/your_username/public_html/dashboard_public/` |
| Environment Config | `/home/your_username/dashboard/.env` |
| Laravel Logs | `/home/your_username/dashboard/storage/logs/` |
| Uploaded Files | `/home/your_username/dashboard/storage/app/` |

---

## Security Recommendations

1. ✅ Never commit `.env` to version control
2. ✅ Use strong database password
3. ✅ Keep `APP_DEBUG=false` in production
4. ✅ Regularly update Laravel and dependencies
5. ✅ Setup automated backups
6. ✅ Enable 2FA for cPanel access
7. ✅ Monitor error logs for suspicious activity
8. ✅ Use HTTPS only (force redirect)
9. ✅ Limit file upload sizes
10. ✅ Keep storage/ and bootstrap/cache/ private

---

**Deployment Guide Complete! 🎉**

If you encounter any issues not covered here, check Laravel logs or contact your hosting support.
