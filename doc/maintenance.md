# Maintenance & Troubleshooting Guide

## 🛠 Routine CMS Maintenance

### 1. Manual Backups (CLI)
If you have SSH access, the fastest way to backup your entire site data and media:
```bash
# Backup just the data and media
tar -czvf som_backup_$(date +%F).tar.gz data/ events/ images/ pics/
```

### 2. Content Audit
Periodically check the `data/` directory. Each `.json` file stores a specific section. 
- `publications.json`: Stores nested arrays for Journals, Conferences, and Books.
- `research_group.json`: Stores Ph.D., M.Tech, and Intern status.

---

## 🆘 Troubleshooting Scenarios

### Scenario A: "Permission Denied" Error in Admin Panel
**Problem:** You try to save an entry, but the JSON editor says it can't write, or the file upload fails.
**Solution:**
1. Check folder permissions: `ls -ld data events images pics`
2. Fix ownership: `sudo chown -R www-data:www-data data events images pics` (On Ubuntu/Debian).

### Scenario B: "Syntax Error" in Visual Editor
**Problem:** The visual editor shows a warning and stays empty.
**Solution:**
1. You likely accidentally deleted a comma `,` or a bracket `]` in the **Raw JSON Editor** on the right.
2. Every entry MUST have a comma after it, except for the very last one in an array.
3. Every `[` must have a matching `]`.

### Scenario C: Images/PDFs are not appearing
**Problem:** You uploaded a file, but the link is broken.
**Solution:**
1. Verify the file case (e.g., `image.JPG` vs `image.jpg`). Linux servers are case-sensitive.
2. Check the path. In the JSON, the path should usually omit the leading slash (e.g., `events/brochure.pdf`).

---

## 🔄 Updating the Core System
If you want to add a completely new page (e.g., "Gallery"):
1. Create `gallery.json` in `data/`.
2. Create `gallery.php` (copy-paste logic from `teaching.php`).
3. Add a link in `admin/dashboard.php`.

## 🛡 Security Monitoring
- Review `admin/raw_editor.php` logic to ensure only authenticated users can access it.
- Check server logs (`/var/log/apache2/error.log`) if the Admin panel white-screens.
