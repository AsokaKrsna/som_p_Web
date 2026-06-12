# Deployment Guide

This guide covers local development, shared hosting, VPS deployment, and security hardening.

## 🛠 Prerequisites

- **PHP 7.4+** (tested on 8.0, 8.1, 8.2, 8.3)
- **Extensions:** `mbstring`, `json`, `fileinfo` (usually enabled by default)
- **Web Server:** Apache, Nginx, or LiteSpeed

---

## 🖥 Local Development

```bash
git clone <repo-url> som_portfolio
cd som_portfolio
php -S localhost:8000
```

Open `http://localhost:8000` in your browser. The admin panel is at `http://localhost:8000/admin`.

> **Note:** PHP's built-in server does not process `.htaccess` files. The `data/.htaccess` protection only applies on Apache.

---

## 🚀 Scenario 1: Standard Shared Hosting (CPanel/Plesk)

1. **Upload via File Manager/FTP:**
   - Upload the entire folder content to `public_html/` or a subdirectory.

2. **Permissions:**
   - Change directory permissions for `/data`, `/events`, `/images`, and `/pics` to `775` or `755`.
   - Ensure the PHP user (e.g., `www-data` or the cPanel account username) owns these folders.

3. **Verify:**
   - Visit `your-domain.com/admin` to confirm the login page loads.
   - The `data/.htaccess` file ships with the repo and blocks direct JSON access automatically on Apache.

---

## 💻 Scenario 2: VPS / Linux Server (SSH)

### 1. Clone & Set Ownership
```bash
cd /var/www/html
git clone <your-repo-url> som_portfolio
cd som_portfolio
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
```

### 2. Write Access for Data Directories
Only specific folders need write access:
```bash
sudo chmod -R 775 data events images pics
```

### 3. Apache Configuration
The `data/.htaccess` file is already included in the repo with:
```apache
Order Deny,Allow
Deny from all
```
Ensure `AllowOverride All` is set in your Apache virtual host for this to work.

### 4. Nginx Configuration
If using Nginx, add this to your server block (Nginx ignores `.htaccess` files):
```nginx
# Block direct access to JSON data files
location /som_portfolio/data/ {
    deny all;
    return 403;
}

# (Optional) Restrict admin to internal IPs
location /som_portfolio/admin/ {
    allow 10.0.0.0/8;
    allow 172.16.0.0/12;
    allow 192.168.0.0/16;
    deny all;
}

location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
}
```

---

## 🔒 Security Hardening

### 1. Data File Protection

**Apache:** Already handled by `data/.htaccess` (ships with the repo).

**Nginx:** Add the `deny all` location block shown above.

**Verification:**
```bash
curl -I https://your-domain.com/data/publications.json
# Should return 403 Forbidden
```

### 2. IP-Whitelisting the Admin Panel

For internal deployments, restrict `/admin` access by IP:

**Apache** (add to `.htaccess` in `/admin`):
```apache
Order Deny,Allow
Deny from all
Allow from 10.0.0.0/8
Allow from 192.168.0.0/16
```

**Nginx** (see the example in Section 4 above).

### 3. Password Management

Passwords are hashed using **bcrypt** (`PASSWORD_DEFAULT`) out of the box.

**Change password via UI:**
- Login → Dashboard → System Security → Change Password

**Reset a lost password via CLI:**
```bash
php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
```
Paste the output into `$ADMIN_HASH` in `admin/config.php`.

### 4. Built-in Security Features

These are active by default with no additional configuration:

| Feature | Details |
|---|---|
| CSRF tokens | Session-based, validated on all POST operations |
| Rate limiting | 5 login attempts max, 15-minute lockout |
| Session hardening | `session_regenerate_id()`, `HttpOnly`, `SameSite=Strict` cookies |
| Upload validation | Extension whitelist + MIME check + 10MB size limit |
| Output encoding | `htmlspecialchars()` on all rendered user data |

### 5. SSL (HTTPS)

Always deploy with an SSL certificate (e.g., Let's Encrypt). This protects admin credentials from being intercepted over the network.

```bash
# Certbot (Let's Encrypt) for Apache
sudo certbot --apache -d your-domain.com

# For Nginx
sudo certbot --nginx -d your-domain.com
```

---

## 🧪 Verification Commands

Run these after deployment to confirm everything works:

```bash
# Check PHP version (must be 7.4+)
php -v

# Check required extensions
php -m | grep -E "json|mbstring|fileinfo"

# Check write permissions
for dir in data events images pics; do
  [ -w "$dir" ] && echo "$dir: Writable ✓" || echo "$dir: NOT Writable ✗"
done

# Test data protection
curl -s -o /dev/null -w "%{http_code}" https://your-domain.com/data/publications.json
# Expected: 403

# Test admin page loads
curl -s -o /dev/null -w "%{http_code}" https://your-domain.com/admin/
# Expected: 200
```

---

## 🔄 Post-Deployment Checklist

- [ ] Change the default admin password (`admin123`) immediately
- [ ] Verify data directory is not accessible via browser (`403 Forbidden`)
- [ ] Confirm file upload works (Dashboard → Upload Files)
- [ ] Test dark mode toggle persists across page reloads
- [ ] Set up SSL certificate if on a public domain
- [ ] (Optional) Set up IP whitelisting for `/admin`
- [ ] (Optional) Set up automated backups of the `/data` directory
