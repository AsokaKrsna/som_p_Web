# Technical Specifications

## 🏗 System Architecture

The Dr. Somanath Tripathy Academic Portfolio uses a **Modular PHP Flat-File architecture**. This is a stateless design where the server does not need to maintain a database connection.

### 1. Data Layer (JSON Flat-Files)

Content is stored as standard JSON in the `/data` directory.

- **Encoding:** UTF-8 (strictly enforced).
- **Validation:** Visual Editor uses `JSON.parse()` in JavaScript for client-side validation. Server-side validation uses `json_decode()` + `json_last_error()`.
- **Save Hook:** `admin/raw_editor.php` and `admin/ajax_save.php` use `file_put_contents()` after verifying the user session and CSRF token. A `.bak` backup is created before each save.
- **Error Handling:** All JSON reads use `loadJsonData()` helper (in `index.php`) or explicit `file_exists()` + `json_last_error()` checks, with `?? []` fallbacks so a malformed file never crashes the public page.

| File | Structure | Sections |
|---|---|---|
| `publications.json` | Object with arrays | `authored_books`, `books`, `journals`, `conferences`, `preprints` |
| `projects.json` | Object with arrays | `ongoing`, `completed` |
| `research_group.json` | Object with arrays | `phd`, `mtech`, `interns`, `past_phd`, `past_mtech` |
| `lab_content.json` | Configuration Object | `hero`, `about`, `research_areas`, `join_us`, `open_resources`, `gallery` |
| `profile_content.json` | Configuration Object | `hero`, `about` (used on index.php) |
| `announcements.json`| Flat array | Scrolling marquee items |
| All others | Flat arrays | `patents`, `teaching`, `seminars`, `memberships`, `editorships`, `awards` |

### 2. Rendering Layer (PHP 7.4+ Templating)

The system uses a manual "MVC-light" approach:
- **Model:** The `.json` files in `/data`.
- **View:** Root `.php` files (`index.php`, `research_group.php`).
- **Controller:** The `admin/` logic (CRUD via `raw_editor.php`, `ajax_save.php`).

Shared PHP helpers:
- `loadJsonData($file)` — Safe JSON loader with `file_exists()`, `@file_get_contents()`, and `json_last_error()` validation.
- `renderPublicationTable($items)` — Reusable renderer for publication entries across 4 tabs.
- `renderMemberRow($member)` — Reusable renderer for research group member cards.

### 3. Component Architecture

Global elements are centralized in `/components`:

| File | Purpose |
|---|---|
| `header.php` | `<head>`, meta tags (SEO + OG), favicon, CSS links, navbar markup, side-nav, `<main>` landmark |
| `footer.php` | `</main>`, footer content, Bootstrap JS, `navbar.js` script |
| `navbar.js` | Scroll-spy, side-nav timeline fill, dark mode toggle + localStorage persistence, back-to-top |

### 4. Admin Architecture

Admin files are centralized in `/admin`:

| File | Purpose |
|---|---|
| `admin.css` | Shared admin styles (~300 lines, consolidated from inline blocks) |
| `admin-common.js` | Shared dark mode toggle + localStorage persistence |
| `config.php` | Admin username and bcrypt password hash |
| `index.php` | Login page with CSRF, rate limiting, session hardening |
| `dashboard.php` | Dashboard with data links, file upload, password change |
| `raw_editor.php` | Dual-pane editor (Visual Form + Ace JSON), schema ordering, object-based config support, boolean dropdowns |
| `ajax_save.php` | AJAX POST endpoint for JSON saves (CSRF validated) |
| `ajax_fetch.php` | AJAX GET endpoint for JSON reads (session gated) |
| `upload_file.php` | File upload (CSRF, MIME check, size limit) |
| `update_password.php` | Password change (CSRF, bcrypt) |
| `logout.php` | Proper session cleanup (clear data, expire cookie, destroy) |

---

## 🎨 Design System: Frosted Pearl

The UI is a custom implementation of **Glassmorphism 2.0** with complete dark mode support.

### CSS Custom Properties

| Variable | Light Mode | Dark Mode |
|---|---|---|
| `--bg-color` | `#f8fafc` | `#080c18` |
| `--text-main` | `#0f172a` | `#e2e8f0` |
| `--text-muted` | `#475569` | `#94a3b8` |
| `--accent-cyan` | `#0891b2` | `#22d3ee` |
| `--accent-blue` | `#2563eb` | `#60a5fa` |
| `--glass-bg` | `rgba(255,255,255,0.65)` | `rgba(10,14,26,0.6)` |
| `--glass-border` | `rgba(255,255,255,0.8)` | `rgba(255,255,255,0.06)` |

### Key Visual Elements

| Element | CSS Target | Key Stylings |
|---|---|---|
| Background | `:root` / `body::before/after` | Off-white + blurred radial gradient orbs |
| Glass Panels | `.glass-panel` | `backdrop-filter: blur(12px)`, semi-transparent bg |
| Typography | `body`, `h1-h6` | **Inter** font family, 300–800 weight range |
| Navbar | `.custom-navbar` | Floating pill, `backdrop-filter: blur(16px)`, rim light borders |
| Side Nav | `.nav-side` | Timeline track with gradient fill, pulsing active dots |
| Dark Mode | `body.dark-mode` | CSS variable overrides + `.dark-mode-transitioning` class for smooth switch |

### Utility Classes

| Class | Purpose |
|---|---|
| `.glass-panel` | Standard glassmorphism card |
| `.section-list-item` | Consistent list item border styling |
| `.entry-text` | Muted text for list entries |
| `.impact-factor-badge` | Blue accent for impact factor labels |
| `.active-entry-link` | Cyan link with text-shadow glow |

### CSS Organization

The main stylesheet (`style/custom.css`, ~2350 lines) is organized into clearly commented sections:

1. **Variables & Base** — CSS custom properties, body, typography
2. **Navbar** — Floating pill navbar, desktop nav, dropdown, hamburger, overlay menu
3. **Hero Section** — Glow orbs, grid layout, avatar
4. **Content Sections** — Bio, tables, publications, member cards
5. **Scroll Components** — Progress bar, back-to-top, side nav, scroll indicator
6. **Dark Mode** — Complete variable overrides + component-specific adjustments
7. **Floating Action Bar** — Fixed top-right utility buttons

---

## 🛠 Backend API Reference

### `admin/ajax_save.php` (POST)
Saves JSON data to a file.
- **Auth:** Session check + CSRF token validation (POST field or `X-CSRF-TOKEN` header)
- **Input:** `file` (filename), `content` (JSON string)
- **Validation:** Filename whitelist, `json_decode()` validation
- **Output:** `application/json` with `success` boolean and `message`

### `admin/ajax_fetch.php` (GET)
Reads JSON data from a file.
- **Auth:** Session check
- **Input:** `?file=filename.json`
- **Validation:** Filename whitelist, `file_exists()` check
- **Output:** `application/json` with `success` boolean and `content`

### `admin/upload_file.php` (POST)
Handles multi-part form data for file uploads.
- **Auth:** Session check + CSRF token validation
- **Security:** Target folder whitelist (`events`, `images`, `pics`), extension whitelist, MIME type verification via `finfo`, 10MB size limit
- **Sanitization:** `basename()` + `preg_replace("/[^a-zA-Z0-9\._-]/", "", $file_name)`

### `admin/update_password.php` (POST)
Updates the admin password.
- **Auth:** Session check + CSRF token + current password verification
- **Action:** Generates new bcrypt hash, rewrites `config.php`, destroys session (forces re-login)

---

## 🔐 Security Architecture

### Authentication Flow
1. User submits username/password with CSRF token
2. Rate limiter checks: max 5 attempts, 15-minute lockout
3. `password_verify()` against bcrypt hash in `config.php`
4. On success: `session_regenerate_id(true)`, new CSRF token, redirect to dashboard
5. On failure: increment attempt counter, show generic error

### CSRF Protection
- Token generated: `bin2hex(random_bytes(32))` stored in `$_SESSION['csrf_token']`
- Validated on all POST operations with `hash_equals()`
- Supports both form fields (`csrf_token`) and AJAX headers (`X-CSRF-TOKEN`)

### Session Security
- `session_regenerate_id(true)` after login (prevents session fixation)
- `session.cookie_httponly = 1` (prevents XSS cookie theft)
- `session.cookie_samesite = Strict` (prevents CSRF via cross-site requests)
- Logout: clears `$_SESSION`, expires cookie, calls `session_destroy()`

---

## 📈 Performance

- **Page Load:** < 500ms on standard connections
- **First Contentful Paint (FCP):** ~0.2s
- **No build step:** Zero compilation, no bundler, no node_modules
- **Browser-cached assets:** `admin.css` and `admin-common.js` are external files (not inline)
- **SEO:** Optimized H1 structure, meta descriptions, Open Graph tags, semantic HTML5 landmarks
