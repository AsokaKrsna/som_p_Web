<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper to safely load JSON Count
function getCount($file) {
    $path = "../data/" . $file;
    if (file_exists($path)) {
        $data = json_decode(@file_get_contents($path), true);
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
    <link href="admin.css" rel="stylesheet"/>
</head>
<body class="admin-page">
    <!-- Floating Action Bar -->
    <div class="floating-action-bar">
        <a href="../index.php" class="floating-btn" target="_blank" title="View Site">
            <i class="fa fa-external-link"></i> <span class="d-none d-md-inline">View Site</span>
        </a>
        <button class="floating-btn dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">
            <i class="fa fa-moon-o" id="darkModeIcon"></i>
        </button>
        <a href="logout.php" class="floating-btn" style="color: #ef4444;" title="Logout">
            <i class="fa fa-sign-out"></i> <span class="d-none d-md-inline">Logout</span>
        </a>
    </div>

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
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
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
                    if ($_GET['upload'] == 'too_large') echo "<div class='text-danger small mt-2 w-100'><i class='fa fa-exclamation-circle'></i> File too large (max 10 MB).</div>";
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
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
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

    <script src="admin-common.js"></script>
</body>
</html>
