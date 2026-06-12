<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Helper to safely load JSON Count
function getCount($file) {
    $path = "../data/" . $file;
    if (file_exists($path)) {
        $data = json_decode(file_get_contents($path), true);
        if (is_array($data)) return count($data);
    }
    return 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Tripathy Portfolio CMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <style>
        body { background-color: var(--bg-color); color: var(--text-main); }
        .admin-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            padding: 0.8rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        body.dark-mode .admin-header {
            background: rgba(10, 14, 26, 0.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .admin-header h4 {
            margin: 0;
            font-weight: 700;
            background: linear-gradient(90deg, var(--text-main), var(--accent-cyan));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .admin-header .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .admin-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            height: 100%;
            box-shadow: var(--glass-shadow);
        }
        .admin-card h5 {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .admin-card h6 {
            color: var(--text-main);
            font-weight: 600;
        }
        .admin-card p.text-muted {
            color: var(--text-muted) !important;
        }
        .logout-btn {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }
        .admin-card .btn-outline-primary {
            text-align: left;
            border: 1px solid rgba(8, 145, 178, 0.2);
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        body.dark-mode .admin-card .btn-outline-primary {
            background: rgba(10, 14, 26, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
        }
        .admin-card .btn-outline-primary:hover {
            background: var(--accent-cyan);
            border-color: var(--accent-cyan);
            color: #fff;
            transform: translateX(5px);
        }
        .admin-card .btn-outline-secondary {
            text-align: left;
            border: 1px solid rgba(71, 85, 105, 0.15);
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        body.dark-mode .admin-card .btn-outline-secondary {
            background: rgba(10, 14, 26, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.04);
            color: #94a3b8;
        }
        .admin-card .btn-outline-secondary:hover {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #fff;
            transform: translateX(5px);
        }
        .admin-card .form-control,
        .admin-card .form-select {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            color: var(--text-main);
        }
        body.dark-mode .admin-card .form-control,
        body.dark-mode .admin-card .form-select {
            background: rgba(10, 14, 26, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .admin-card .form-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
        }
        .admin-card .border-end {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        body.dark-mode .admin-card .border-end {
            border-color: rgba(255, 255, 255, 0.04) !important;
        }
        .admin-card .btn-dark {
            background: var(--text-main);
            border: none;
            border-radius: 8px;
            padding: 0.4rem 1.2rem;
            font-weight: 500;
        }
        body.dark-mode .admin-card .btn-dark {
            background: #334155;
        }
        .admin-card .btn-dark:hover {
            background: var(--accent-cyan);
        }
        .page-title {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }
        .dark-mode-toggle-admin {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.1rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .dark-mode-toggle-admin:hover {
            color: var(--accent-cyan);
            background: rgba(8, 145, 178, 0.06);
            transform: scale(1.1);
        }
        .dashboard-content {
            padding-top: 5rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        body.dark-mode .alert-success {
            background: rgba(34, 197, 94, 0.08);
            color: #4ade80;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        body.dark-mode .alert-danger {
            background: rgba(239, 68, 68, 0.08);
            color: #f87171;
        }
        hr {
            display: block;
            border: none;
            height: 1px;
            background: rgba(255, 255, 255, 0.06);
            margin: 1rem 0;
            opacity: 0.5;
        }
        body.dark-mode hr {
            background: rgba(255, 255, 255, 0.03);
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h4>Tripathy Portfolio CMS</h4>
        <div class="header-actions">
            <button class="dark-mode-toggle-admin" id="adminDarkModeToggle" aria-label="Toggle dark mode">
                <i class="fa fa-moon-o" id="adminDarkModeIcon"></i>
            </button>
            <a href="logout.php" class="logout-btn"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </header>

    <div class="container dashboard-content mt-4">
        <h2 class="section-title">Dashboard</h2>
        <p class="page-title text-center">Manage your academic portfolio data</p>

        <div class="row">
            <!-- Main Datasets -->
            <div class="col-md-6 mb-4">
                <div class="admin-card">
                    <h5><i class="fa fa-database" style="color: var(--accent-cyan);"></i> Primary Datasets</h5>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="raw_editor.php?file=publications.json" class="btn btn-outline-primary text-start"><i class="fa fa-book"></i> Publications (<?= getCount('publications.json') ?>)</a>
                        <a href="raw_editor.php?file=projects.json" class="btn btn-outline-primary text-start"><i class="fa fa-flask"></i> Projects (<?= getCount('projects.json') ?>)</a>
                        <a href="raw_editor.php?file=research_group.json" class="btn btn-outline-primary text-start"><i class="fa fa-users"></i> Research Group (<?= getCount('research_group.json') ?>)</a>
                        <a href="raw_editor.php?file=patents.json" class="btn btn-outline-primary text-start"><i class="fa fa-file-text"></i> Patents (<?= getCount('patents.json') ?>)</a>
                    </div>
                </div>
            </div>

            <!-- Secondary / Single List Datasets -->
            <div class="col-md-6 mb-4">
                <div class="admin-card">
                    <h5><i class="fa fa-graduation-cap" style="color: var(--accent-blue);"></i> Academic Details</h5>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="raw_editor.php?file=teaching.json" class="btn btn-outline-secondary text-start"><i class="fa fa-chalkboard"></i> Teaching (<?= getCount('teaching.json') ?>)</a>
                        <a href="raw_editor.php?file=seminars.json" class="btn btn-outline-secondary text-start"><i class="fa fa-microphone"></i> Seminars/Talks (<?= getCount('seminars.json') ?>)</a>
                        <a href="raw_editor.php?file=memberships.json" class="btn btn-outline-secondary text-start"><i class="fa fa-id-badge"></i> Memberships (<?= getCount('memberships.json') ?>)</a>
                        <a href="raw_editor.php?file=editorships.json" class="btn btn-outline-secondary text-start"><i class="fa fa-pencil-square-o"></i> Editorships (<?= getCount('editorships.json') ?>)</a>
                        <a href="raw_editor.php?file=awards.json" class="btn btn-outline-secondary text-start"><i class="fa fa-trophy"></i> Awards (<?= getCount('awards.json') ?>)</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload System -->
        <div class="admin-card">
            <h5><i class="fa fa-upload" style="color: var(--accent-cyan);"></i> Upload Files</h5>
            <p class="text-muted small">Upload PDFs, images, or avatars to link in your JSON data (e.g. <code>events/filename.pdf</code>).</p>
            <hr>
            <form action="upload_file.php" method="POST" enctype="multipart/form-data" class="row align-items-end g-2">
                <div class="col-md-5">
                    <label class="small fw-bold mb-1" style="color: var(--text-muted);">Select File</label>
                    <input type="file" name="upload_file" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold mb-1" style="color: var(--text-muted);">Target Folder</label>
                    <select name="target_folder" class="form-select form-select-sm">
                        <option value="events">events/ (PDFs/Brochures)</option>
                        <option value="images">images/ (Photos)</option>
                        <option value="pics">pics/ (Avatars)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-custom btn-sm w-100">Upload File</button>
                </div>
                <?php
                if (isset($_GET['upload'])) {
                    if ($_GET['upload'] == 'success') echo "<div class='text-success small mt-2 w-100'><i class='fa fa-check-circle'></i> File uploaded successfully!</div>";
                    if ($_GET['upload'] == 'error') echo "<div class='text-danger small mt-2 w-100'><i class='fa fa-exclamation-circle'></i> Upload failed. Check permissions.</div>";
                }
                ?>
            </form>
        </div>

        <!-- System Settings -->
        <div class="row">
            <div class="col-md-12">
                <div class="admin-card">
                    <h5><i class="fa fa-cog" style="color: var(--text-muted);"></i> System Security</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Change Password</h6>
                            <form action="update_password.php" method="POST">
                                <div class="mb-2">
                                    <label class="small fw-bold" style="color: var(--text-muted);">Current Password</label>
                                    <input type="password" name="current_password" class="form-control form-control-sm" required>
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold" style="color: var(--text-muted);">New Password</label>
                                    <input type="password" name="new_password" class="form-control form-control-sm" required>
                                </div>
                                <button type="submit" class="btn btn-dark btn-sm mt-2">Update Credentials</button>
                            </form>
                            <?php
                            if (isset($_GET['pass_update'])) {
                                if ($_GET['pass_update'] == 'success') echo "<div class='text-success small mt-2'><i class='fa fa-check-circle'></i> Password updated!</div>";
                                if ($_GET['pass_update'] == 'invalid') echo "<div class='text-danger small mt-2'><i class='fa fa-exclamation-circle'></i> Invalid current password!</div>";
                                if ($_GET['pass_update'] == 'error') echo "<div class='text-danger small mt-2'><i class='fa fa-exclamation-circle'></i> Failed to update config.</div>";
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3">
                                <h6 class="text-muted"><i class="fa fa-shield"></i> Security Advice</h6>
                                <p class="small" style="color: var(--text-muted);">Passwords are hashed using <strong>BCRYPT</strong>. Use a strong password with letters, numbers, and symbols.</p>
                                <p class="small" style="color: var(--text-muted);">You will be logged out automatically after changing your password.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('adminDarkModeToggle');
        const icon = document.getElementById('adminDarkModeIcon');
        if (toggle && icon) {
            if (document.body.classList.contains('dark-mode')) {
                icon.className = 'fa fa-sun-o';
            }
            toggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                icon.className = isDark ? 'fa fa-sun-o' : 'fa fa-moon-o';
            });
        }
    });
    </script>
</body>
</html>
