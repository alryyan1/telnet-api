# Quick Start Guide for Hostinger Deployment

## ⚠️ IMPORTANT: Current Structure Issue

You've uploaded the entire Laravel project to `public_html`. This exposes sensitive files like `.env` and your entire codebase.

## 🚨 SECURITY RISK

Having everything in `public_html` means:
- Your `.env` file with database credentials is accessible
- Your entire source code is exposed
- Configuration files are accessible

## ✅ RECOMMENDED: Restructure Files

### Step 1: Access Your Files

Via Hostinger File Manager or SSH:
- Go to: `domains/nova-suits.com/public_html`

### Step 2: Move Files Up One Level

**What to move UP (to parent directory):**
- `app/` folder
- `bootstrap/` folder
- `config/` folder
- `database/` folder
- `routes/` folder
- `storage/` folder
- `vendor/` folder
- `.env` file
- `artisan` file
- `composer.json` and `composer.lock`

**What stays in public_html:**
- Contents of `public/` folder (index.php, .htaccess, etc.)
- Delete the empty `public/` folder after moving its contents

### Step 3: Final Structure Should Be:

```
domains/nova-suits.com/
├── public_html/          ← Web root (only public files)
│   ├── index.php
│   ├── .htaccess
│   └── (other public assets)
├── app/                  ← Laravel app (protected)
├── bootstrap/
├── config/
├── database/
├── routes/
├── storage/
├── vendor/
├── .env                  ← Protected!
└── artisan
```

## 🔧 Configuration Steps

### 1. Update .env File

Create/update `.env` in the parent directory (NOT in public_html):

```env
APP_NAME="Morpho IoT API"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://nova-suits.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MORPHO_API_BASE_URL=https://alroomy.a.pinggy.link
```

### 2. Set Permissions (via SSH)

```bash
cd ~/domains/nova-suits.com
chmod -R 755 storage bootstrap/cache
chmod 644 .env
```

### 3. Install Dependencies (via SSH)

```bash
cd ~/domains/nova-suits.com
composer install --no-dev --optimize-autoloader
```

### 4. Generate Key & Run Migrations (via SSH)

```bash
cd ~/domains/nova-suits.com
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

## 🧪 Test Your API

After setup, test these endpoints:

1. **Authentication:**
   ```bash
   curl -X POST https://nova-suits.com/auth \
     -H "Content-Type: application/json"
   ```

2. **Device Status:**
   ```bash
   curl -X POST https://nova-suits.com/api/device/status \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"device_id": 7777, ...}'
   ```

## 📝 If You MUST Keep Everything in public_html

**⚠️ NOT RECOMMENDED - Security Risk!**

1. Rename `public/index.php` to `public/index_old.php`
2. Copy `public/index_hostinger_quickfix.php` to `public/index.php`
3. Update paths in the new index.php if needed

**But seriously, restructure your files properly!**

## 🆘 Troubleshooting

### 500 Error?
- Check file permissions
- Check `.env` file exists
- Check error logs: `storage/logs/laravel.log`

### Routes Not Working?
- Ensure `.htaccess` exists in `public_html`
- Clear cache: `php artisan route:clear`

### Database Error?
- Verify database credentials in `.env`
- Check database exists in Hostinger panel

## 📞 Need Help?

1. Check `HOSTINGER_DEPLOYMENT.md` for detailed guide
2. Check Hostinger error logs
3. Check Laravel logs: `storage/logs/laravel.log`

