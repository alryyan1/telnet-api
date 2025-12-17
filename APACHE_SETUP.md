# Apache Configuration Guide

## Step 1: Configure Apache Virtual Host

### Option A: Edit httpd-vhosts.conf

1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add the virtual host configuration (see `apache_vhost_example.conf`)
3. Make sure `httpd-vhosts.conf` is included in `httpd.conf`:
   ```apache
   # In C:\xampp\apache\conf\httpd.conf, ensure this line is uncommented:
   Include conf/extra/httpd-vhosts.conf
   ```

### Option B: Edit httpd.conf directly

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find the `<Directory "C:/xampp/htdocs">` section
3. Change DocumentRoot to point to your Laravel public folder:
   ```apache
   DocumentRoot "C:/xampp/htdocs/telnet/public"
   
   <Directory "C:/xampp/htdocs/telnet/public">
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

## Step 2: Update Windows Hosts File (Optional)

If you want to use a custom domain instead of `127.0.0.1`:

1. Open `C:\Windows\System32\drivers\etc\hosts` as Administrator
2. Add this line:
   ```
   127.0.0.1    your-domain.local
   ```

## Step 3: Restart Apache

1. Open XAMPP Control Panel
2. Stop Apache
3. Start Apache

## Step 4: Test Your Routes

After configuration, your routes will be accessible at:

- **Before:** `https://127.0.0.1/telnet/public/api/morpho/auth`
- **After:** `https://127.0.0.1/morpho/auth` (or `https://your-domain.local/morpho/auth`)

## Available Routes

- `POST /morpho/auth` - Authentication
- `POST /morpho/device/status` - Device status
- `POST /morpho/device/logs` - Device logs
- `GET /morpho/device/config` - Get device config
- `POST /morpho/device/config` - Update device config
- `GET /morpho/device/reboot` - Get reboot command

## Troubleshooting

### If you get 403 Forbidden:
- Check that `AllowOverride All` is set in Apache config
- Ensure `.htaccess` file exists in `public` folder
- Check file permissions

### If routes don't work:
- Clear Laravel route cache: `php artisan route:clear`
- Clear config cache: `php artisan config:clear`
- Check Apache error logs: `C:\xampp\apache\logs\error.log`

