# Dr. Somanath Tripathy | Academic Portfolio & CMS

A premium academic portfolio for **Dr. Somanath Tripathy**, Professor in the Department of Computer Science & Engineering at IIT Patna. Features a custom-built **PHP Flat-File CMS** with a dual-pane editor, inline frontend editing, glassmorphism UI, and full dark mode — all without a database.

---

## ✨ Features

| Feature | Description |
|---|---|
| **Glassmorphism UI** | "Frosted Pearl" light/dark theme with ambient orbs, subtle animations, and Inter typography |
| **Dark Mode** | Full dark mode with smooth transitions, persisted via `localStorage` |
| **Side Navigation** | Timeline-style scroll-spy with gradient fill, animated dots, and visited states |
| **Dual-Pane Editor** | Visual Form Editor + Ace JSON Editor with live sync and drag-to-reorder |
| **File Uploads** | Integrated portal for PDFs, images, and avatars with MIME validation |
| **SEO Ready** | Meta descriptions, Open Graph tags, semantic HTML5, `<main>` landmark |
| **Security** | CSRF tokens, bcrypt passwords, rate limiting, session hardening |
| **Zero Dependencies** | Pure PHP 7.4+, HTML, CSS, JS. No frameworks, no build tools, no database |

---

## 📂 Project Structure

```
som_p_Web/
├── index.php                  # Main portfolio page (all sections)
├── research_group.php         # Research group directory page
├── README.md
│
├── admin/                     # CMS Backend
│   ├── index.php              # Login page (CSRF, rate limiting)
│   ├── dashboard.php          # Admin dashboard
│   ├── raw_editor.php         # Dual-pane JSON editor
│   ├── config.php             # Admin credentials (bcrypt hash)
│   ├── admin.css              # Shared admin styles
│   ├── admin-common.js        # Shared dark mode toggle + localStorage
│   ├── ajax_save.php          # AJAX endpoint: save JSON data
│   ├── ajax_fetch.php         # AJAX endpoint: fetch JSON data
│   ├── upload_file.php        # File upload handler (10MB limit)
│   ├── update_password.php    # Password change handler
│   └── logout.php             # Session cleanup
│
├── components/                # Shared UI Components
│   ├── header.php             # <head>, navbar, side-nav, meta tags
│   ├── footer.php             # Footer, closing scripts
│   └── navbar.js              # Scroll-spy, side-nav, dark mode toggle
│
├── data/                      # JSON Data ("Database")
│   ├── .htaccess              # Blocks direct HTTP access
│   ├── publications.json
│   ├── patents.json
│   ├── projects.json
│   ├── teaching.json
│   ├── seminars.json
│   ├── memberships.json
│   ├── editorships.json
│   ├── awards.json
│   └── research_group.json
│
├── style/
│   └── custom.css             # Design system (2300+ lines, full dark mode)
│
├── css/
│   └── bootstrap.min.css      # Bootstrap 5 (local copy)
│
├── doc/                       # Documentation
│   ├── specifications.md      # Architecture & API reference
│   ├── deployment.md          # Server setup & security
│   └── maintenance.md         # Troubleshooting & content guide
│
├── events/                    # Uploaded PDFs & brochures
├── images/                    # Site images (profile photo, etc.)
└── pics/                      # Member avatars
```

---

## 🚀 Quick Start

### Local Development
```bash
git clone <repo-url> som_portfolio
cd som_portfolio
php -S localhost:8000
```
Open `http://localhost:8000` in your browser.

### Production
1. Upload all files to your web server.
2. Set write permissions on `/data`, `/events`, `/images`, `/pics`.
3. Access the admin panel at `/admin`.

> See [doc/deployment.md](doc/deployment.md) for detailed Apache/Nginx/shared hosting instructions.

---

## 🛠 Admin CMS

| | |
|---|---|
| **URL** | `/admin` |
| **Default Username** | `admin` |
| **Default Password** | `admin123` |

> Change credentials immediately from the Admin Dashboard → System Security.

### Content Management
- **Dashboard**: Quick links to edit any data section with item counts
- **Visual Editor**: Add, edit, delete, and reorder entries via a form interface
- **Raw Editor**: Direct JSON editing with syntax highlighting (Ace Editor)
- **File Upload**: Upload PDFs, images, or avatars — select target folder and upload

---

## 🔒 Security

| Feature | Implementation |
|---|---|
| Password Hashing | bcrypt via `password_hash()` / `password_verify()` |
| CSRF Protection | Session-based tokens on all forms and AJAX calls |
| Login Rate Limiting | 5 attempts max, 15-minute lockout (session-based) |
| Session Hardening | `session_regenerate_id()`, `HttpOnly`, `SameSite=Strict` cookies |
| File Upload Validation | Extension whitelist + MIME type check via `finfo` + 10MB size limit |
| Filename Sanitization | `basename()` + `preg_replace()` stripping |
| Data File Protection | `.htaccess` (Apache) / `deny all` (Nginx) on `/data` |
| Output Encoding | `htmlspecialchars()` on all user-rendered content |

> The admin panel is designed for internal/IP-whitelisted deployment. For public-facing servers, deploy behind a reverse proxy with additional access controls.

---

## 📚 Documentation

| Document | Contents |
|---|---|
| [specifications.md](doc/specifications.md) | Architecture, design system, component map, API reference |
| [deployment.md](doc/deployment.md) | Apache/Nginx/shared hosting setup, permissions, SSL |
| [maintenance.md](doc/maintenance.md) | Backups, troubleshooting, adding new pages |

---

## ⚖ License

Created for academic use at IIT Patna. All documentation and maintenance guides are included in `doc/`.
