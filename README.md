# Dr. Somanath Tripathy | Academic Portfolio & CMS

A high-end, premium academic portfolio for Dr. Somanath Tripathy, Professor at IIT Patna. This project features a custom-built **PHP Flat-File CMS** that allows for 100% dynamic content management without the complexity of a database.

## ✨ Key Features
- **Modern Glassmorphism:** Elegant "Frosted Pearl" light theme with subtle glowing orbs and refined typography.
- **No-Code Management:** Intuitive Admin Dashboard with a side-by-side Visual Form Editor and Raw JSON Editor.
- **File Upload System:** Integrated portal to upload PDFs, brochures, and images directly to the server.
- **Zero-Dependency Core:** Pure PHP, HTML, CSS, and JS. Lightweight and blazing fast.

---

## 🚀 Installation & Deployment

### Local Development
```bash
# Clone the repository
git clone <repo-url> _som
cd _som

# Start the PHP development server
php -S localhost:8000
```
Open `http://localhost:8000` in your browser.

### Production Deployment
1. Upload all files to your server.
2. Ensure the `/data`, `/events`, `/images`, and `/pics` directories are **writable** by the web server.
3. Access the admin panel at `/admin` and login with default credentials.

> See [Deployment Guide](doc/deployment.md) for detailed Linux/Shared Hosting instructions.

---

## 🛠 Admin CMS Usage

- **Default Username:** `admin`
- **Default Password:** `som123!`

### Managing Content
- **Edit Data:** Navigate to a section and use the Visual Form Editor. Add, update, or delete entries seamlessly.
- **Upload Files:** Use the "Upload Files" section to add PDFs for events or new images. Link them in your data files using paths like `events/filename.pdf`.

---

## 📂 Project Highlights

| Folder | Description |
| :--- | :--- |
| `/admin` | Backend logic and CRM interface |
| `/data` | JSON storage files (Your "Database") |
| `/doc` | **Extensive Docs** (Read these for troubleshooting!) |
| `/style` | Custom CSS design tokens and core UI styling |

## ⚖ License
Created for academic use. Documentation and maintenance guides included in the `doc/` subdirectory.
