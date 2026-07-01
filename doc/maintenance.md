# Maintenance & Troubleshooting Guide

## 🛠 Routine Maintenance

### 1. Backups

**CLI (SSH access):**
```bash
# Backup data and media files
tar -czvf som_backup_$(date +%F).tar.gz data/ events/ images/ pics/

# Restore from backup
tar -xzvf som_backup_2026-06-12.tar.gz
```

**Manual:** Download the `/data`, `/events`, `/images`, and `/pics` folders via FTP or File Manager.

> The raw editor automatically creates `.bak` files before each save. These can serve as a quick rollback:
> ```bash
> cp data/publications.json.bak data/publications.json
> ```

### 2. Content Audit

Periodically check the `/data` directory. Each `.json` file stores a specific section:

| File | Structure | Notes |
|---|---|---|
| `publications.json` | Object → `journals`, `conferences`, `preprints`, `books` | Fields: `title`, `author`, `link`, `published_at`, `doi`, `impact_factor` |
| `projects.json` | Object → `ongoing`, `completed` | Fields: `title`, `role`, `funding_agency`, `duration`, `amount` |
| `research_group.json` | Object → `phd`, `mtech`, `interns`, `past_phd`, `past_mtech` | Card layout. Fields: `name`, `email`, `research_area`, `image`, etc. |
| `patents.json` | Flat array | Fields: `title`, `authors`, `filed_year`, `application_no`, `patent_no` |
| `teaching.json` | Flat array | Fields: `course`, `link` |
| `seminars.json` | Flat array | Fields: `title`, `location`, `date`, `link` |
| `memberships.json` | Flat array | Fields: `role`, `organization` |
| `editorships.json` | Flat array | Fields: `role`, `journal`, `duration` |
| `awards.json` | Flat array | Fields: `title`, `event`, `location` |
| `profile_content.json` | Configuration Object | General text/config data for index.php |
| `lab_content.json` | Configuration Object | General text/config data for cybersecurity-lab.php |
| `announcements.json` | Flat array | Marquee fields: `text`, `link`, `badge` |

### 3. Dark Mode

Dark mode preference is now persisted via `localStorage`. If you need to reset it:
- Clear `localStorage.removeItem('darkMode')` in browser console, or
- Toggle the moon/sun icon in the navbar or admin panel

---

## 🆘 Troubleshooting

### Scenario A: "Permission Denied" Error in Admin Panel

**Problem:** Saving data or uploading files fails.

**Solution:**
```bash
# Check current permissions
ls -ld data events images pics

# Fix ownership (Ubuntu/Debian)
sudo chown -R www-data:www-data data events images pics
sudo chmod -R 775 data events images pics
```

### Scenario B: "Syntax Error" in Visual Editor

**Problem:** The visual editor shows a warning and stays empty.

**Solution:**
1. The raw JSON on the right pane has a syntax error.
2. Common mistakes:
   - Missing comma `,` between entries
   - Missing closing bracket `]` or brace `}`
   - Trailing comma after the last entry (not allowed in JSON)
3. If the JSON is badly corrupted, restore from the `.bak` file:
   ```bash
   cp data/publications.json.bak data/publications.json
   ```

### Scenario C: Images/PDFs Not Appearing

**Problem:** Uploaded file link is broken.

**Solution:**
1. **Case sensitivity:** Linux servers are case-sensitive (`Image.JPG` ≠ `image.jpg`).
2. **Path format:** In JSON data, use relative paths without leading slash: `events/brochure.pdf`, `pics/avatar.jpg`.
3. **Verify the file exists:**
   ```bash
   ls -la events/brochure.pdf
   ```

### Scenario D: CSRF Token Error on Save

**Problem:** "Invalid request" or "Invalid CSRF token" error when saving.

**Solution:**
1. Your session likely expired. Refresh the page and try again.
2. Clear browser cookies and re-login.

### Scenario E: Locked Out After Too Many Login Attempts

**Problem:** "Too many failed attempts" message.

**Solution:**
1. Wait 15 minutes for the lockout to expire.
2. Or restart the PHP server/process (this clears sessions):
   ```bash
   sudo systemctl restart php8.1-fpm  # For Nginx
   sudo systemctl restart apache2      # For Apache
   ```

### Scenario F: Admin Page Shows Blank White Screen

**Problem:** The admin panel loads but shows nothing.

**Solution:**
1. Check PHP error logs:
   ```bash
   tail -f /var/log/apache2/error.log   # Apache
   tail -f /var/log/nginx/error.log     # Nginx
   ```
2. Enable error display temporarily:
   ```php
   // Add at the top of admin/index.php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

---

## 🔄 Adding New Content Sections

To add a completely new page (e.g., "Gallery"):

### 1. Create the data file
```bash
echo '[]' > data/gallery.json
```

### 2. Create the page
Create `gallery.php` in the root. Use this template:
```php
<?php
$page_title = "Gallery | Dr. Somanath Tripathy";
include 'components/header.php';

// Load data safely
$gallery = json_decode(@file_get_contents('data/gallery.json'), true) ?? [];
?>

<section id="gallery" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Gallery</h2>
        <!-- Your content here -->
    </div>
</section>

<?php include 'components/footer.php'; ?>
```

### 3. Register in admin
Add the file to the whitelist in these files:
- `admin/ajax_save.php` → `$allowed_files` array
- `admin/ajax_fetch.php` → `$allowed_files` array
- `admin/dashboard.php` → Add a button linking to `raw_editor.php?file=gallery.json`

### 4. Add navigation
Update `components/header.php` to add a nav link for the new page.

---

## 🛡 Security Monitoring

- **Login attempts:** Rate limiting is automatic (5 attempts, 15-min lockout).
- **Session management:** Sessions are regenerated on login and properly destroyed on logout.
- **CSRF tokens:** All forms and AJAX calls include and validate CSRF tokens.
- **Server logs:** Monitor for unusual access patterns:
  ```bash
  # Check for repeated login attempts
  grep "admin/index.php" /var/log/apache2/access.log | tail -20
  ```
