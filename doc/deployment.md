# Deployment Guide (Advanced)

This guide covers everything from basic FTP uploads to advanced CLI-based deployments and server optimization.

## 🛠 Prerequisites
- **PHP 7.4+** (Works on 8.0, 8.1, 8.2, and 8.3).
- **Extensions:** `mbstring`, `json`, `fileinfo` (usually enabled by default).
- **Web Server:** Optimized for Apache, Nginx, or LiteSpeed.

---

## 🚀 Scenario 1: Standard Shared Hosting (CPanel/Plesk)

1. **Upload via File Manager/FTP:**
   - Upload the entire folder content to `public_html/` or a subdirectory.
2. **Permissions Logic:**
   - Change directory permissions for `/data`, `/events`, `/images`, and `/pics` to `775` or `755`.
   - Ensure the **PHP User** (e.g., `www-data` or the account username) owns these folders.

---

## 💻 Scenario 2: VPS / Linux Server (SSH)

If you have terminal access, follow these optimized steps:

### 1. Clone & Set Ownership
```bash
cd /var/www/html
git clone <your-repo-url> _som
cd _som
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
```

### 2. High-Security Write Access
Only specific folders need write access. Lock down the rest:
```bash
sudo chmod -R 755 data events images pics
```

### 3. Nginx Configuration Fragment
If using Nginx, add this to your server block:
```nginx
location /_som/data/ {
    deny all;
    return 403;
}

location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
}
```

---

## 🔒 Security Hardening

### 1. Protecting Data Files (.htaccess)
For Apache servers, ensure an `.htaccess` file exists inside the `/data` folder:
```apache
# /data/.htaccess
Order Deny,Allow
Deny from all
```

### 2. Password Hashing
The default password in `admin/index.php` is stored in plain text for initial setup. For production, update the check to use `password_verify()`:
1. Generate a hash: `php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"`
2. Update `admin/index.php` to compare against this hash.

### 3. SSL (HTTPS)
Always deploy with an SSL certificate (e.g., Let's Encrypt). This protects the Admin login credentials from être intercepted over the network.

---

## 🧪 Verification Commands

Run these to ensure your environment is ready:
- **Check PHP version:** `php -v`
- **Check Write Permissions:** `[ -w data ] && echo "Writable" || echo "Not Writable"`
- **Test JSON extension:** `php -m | grep json`
