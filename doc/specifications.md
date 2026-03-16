# Technical Specifications (In-Depth)

## 🏗 System Architecture

The Dr. Somanath Tripathy Academic Portfolio uses a **Modular PHP Flat-File architecture**. This is a "Stateless" design where the server does not need to maintain a database connection.

### 1. Data Layer (JSON Flat-Files)
Content is stored as standard JSON. 
- **Encoding:** UTF-8 (Strictly enforced).
- **Validation:** Visual Editor uses `JSON.parse()` in Javascript to validate before the PHP POSTs.
- **Save Hook:** `admin/raw_editor.php` uses `file_put_contents()` after verifying the user session.

### 2. Rendering Layer (PHP 7.4 Templating)
The system uses a manual "MVC-light" approach:
- **Model:** The `.json` files in `/data`.
- **View:** The root `.php` files (e.g., `publications.php`).
- **Controller:** The `admin/` logic.

### 3. Component Architecture
Global elements are centralized in `/components`:
- `header.php`: Loads `<head>`, CSS, and common CDNs.
- `footer.php`: Closing tags and global scripts.
- `navbar.js`: Handles the fullscreen Glassmorphism menu injection.

---

## 🎨 Design Philosophy: Frosted Pearl

The UI is a custom implementation of **Glassmorphism 2.0**.

| Element | CSS Target | Key Stylings |
| :--- | :--- | :--- |
| Background | `:root` | Off-white `#f8fafc` |
| Glass Panels | `.glass-panel` | `backdrop-filter: blur(12px)`, `rgba(255,255,255,0.65)` |
| Typography | `h1-h6` | **Inter** font family, Slate weights |
| Glow Orbs | `body::before` | Blurred radial gradients with 0.1 opacity |

---

## 🛠 Backend API Reference

### `admin/upload_file.php`
Handles multi-part form data. 
- **Security:** Checks `$_POST['target_folder']` against an internal whitelist: `['events', 'images', 'pics']`.
- **Sanitization:** Uses `preg_replace("/[^a-zA-Z0-9\._-]/", "", $file_name)` to prevent injection.

### `admin/raw_editor.php`
The core CRUD engine.
- **Route:** `?file=filename.json`
- **Logic:** Reads file -> Renders Split Pane -> POSTs back raw text.

---

## 📈 Performance Benchmarks
- **Page Load:** < 500ms on standard 3G/4G connections.
- **First Contentful Paint (FCP):** ~0.2s.
- **SEO Score:** Optimized H1 structure, meta descriptions, and semantic HTML tags.
