# Hostinger Deployment Guide

## Project Structure on Hostinger

Your Laravel project is located at:
```
public_html/
└── telnet/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/          ← This should be the web root
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    └── .htaccess        ← Create this file (see below)
```

## Step 1: Create .htaccess in telnet folder

Create a file named `.htaccess` in `public_html/telnet/` with the following content:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

This will redirect all requests to the `public` folder.

## Step 2: Set File Permissions

Set proper permissions for storage and cache directories:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## Step 3: Configure .env File

Create/update `.env` file in `public_html/telnet/` with:

```env
APP_NAME="Morpho IoT"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://nova-suits.com/telnet

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

MORPHO_API_BASE_URL=https://alroomy.a.pinggy.link
MORPHO_TOKEN_CACHE_DURATION=60
MORPHO_JWT_EXPIRATION=60
```

**Important:** 
- Get your database credentials from Hostinger hPanel
- Generate APP_KEY: Run `php artisan key:generate` via SSH or use Hostinger's File Manager terminal

## Step 4: Run Migrations

Via SSH (if available):
```bash
cd public_html/telnet
php artisan migrate --force
```

Or via Hostinger File Manager Terminal:
```bash
cd telnet
php artisan migrate --force
```

## Step 5: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Step 6: Test Your API Endpoints

After deployment, your API will be accessible at:

- **Authentication:** `https://nova-suits.com/telnet/auth`
- **Device Status:** `https://nova-suits.com/telnet/api/device/status`
- **Device Logs:** `https://nova-suits.com/telnet/api/device/logs`
- **Device Config:** `https://nova-suits.com/telnet/api/device/config`
- **Device Reboot:** `https://nova-suits.com/telnet/api/device/reboot`

## Alternative: Move Public Folder Contents

If the `.htaccess` redirect doesn't work, you can move the contents of `public` folder directly to `telnet` folder:

1. Move all files from `public_html/telnet/public/` to `public_html/telnet/`
2. Update `public/index.php` paths:
   - Change `__DIR__.'/../vendor/autoload.php'` to `__DIR__.'/vendor/autoload.php'`
   - Change `__DIR__.'/../bootstrap/app.php'` to `__DIR__.'/bootstrap/app.php'`
   - Change `__DIR__.'/../storage/framework/maintenance.php'` to `__DIR__.'/storage/framework/maintenance.php'`

## Troubleshooting

### 500 Internal Server Error
- Check file permissions (storage, bootstrap/cache should be 755)
- Check `.htaccess` file exists and is correct
- Check error logs in Hostinger hPanel

### Database Connection Error
- Verify database credentials in `.env`
- Ensure database exists in Hostinger hPanel
- Check database user has proper permissions

### Routes Not Working
- Clear route cache: `php artisan route:clear`
- Check `.htaccess` is redirecting to `public` folder
- Verify `APP_URL` in `.env` matches your domain

### Permission Denied
- Set storage permissions: `chmod -R 755 storage`
- Set bootstrap/cache permissions: `chmod -R 755 bootstrap/cache`

## Security Notes

1. **Never expose sensitive files:** Ensure `.env`, `composer.json`, `package.json` are not publicly accessible
2. **Set proper permissions:** Storage and cache folders should be writable but not executable
3. **Use HTTPS:** Configure SSL certificate in Hostinger hPanel
4. **Hide server info:** Set `APP_DEBUG=false` in production

