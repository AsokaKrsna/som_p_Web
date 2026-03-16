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
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <style>
        body { background-color: var(--bg-color); color: var(--text-main); }
        .dashboard-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            height: 100%;
        }
        .logout-btn { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<header class="dashboard-header">
    <h4 class="m-0">Tripathy Portfolio CMS</h4>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<div class="container mt-5">
    
    <div class="row">
        <!-- Main Datasets -->
        <div class="col-md-6 mb-4">
            <div class="admin-card">
                <h5>Primary Datasets</h5>
                <hr>
                <div class="d-grid gap-2">
                    <a href="raw_editor.php?file=publications.json" class="btn btn-outline-primary text-start">📚 Publications</a>
                    <a href="raw_editor.php?file=projects.json" class="btn btn-outline-primary text-start">🔬 Projects</a>
                    <a href="raw_editor.php?file=research_group.json" class="btn btn-outline-primary text-start">👥 Research Group</a>
                    <a href="raw_editor.php?file=patents.json" class="btn btn-outline-primary text-start">📜 Patents (<?= getCount('patents.json') ?>)</a>
                </div>
            </div>
        </div>
        
        <!-- Secondary / Single List Datasets -->
        <div class="col-md-6 mb-4">
            <div class="admin-card">
                <h5>Academic Details</h5>
                <hr>
                <div class="d-grid gap-2">
                    <a href="raw_editor.php?file=teaching.json" class="btn btn-outline-secondary text-start">🎓 Teaching (<?= getCount('teaching.json') ?>)</a>
                    <a href="raw_editor.php?file=seminars.json" class="btn btn-outline-secondary text-start">🎤 Seminars/Talks (<?= getCount('seminars.json') ?>)</a>
                    <a href="raw_editor.php?file=memberships.json" class="btn btn-outline-secondary text-start">🤝 Memberships (<?= getCount('memberships.json') ?>)</a>
                    <a href="raw_editor.php?file=editorships.json" class="btn btn-outline-secondary text-start">📝 Editorships (<?= getCount('editorships.json') ?>)</a>
                    <a href="raw_editor.php?file=awards.json" class="btn btn-outline-secondary text-start">🏆 Awards (<?= getCount('awards.json') ?>)</a>
                </div>
            </div>
        </div>
    </div>

    <!-- File Upload System -->
    <div class="admin-card">
        <h5>Upload Files (PDFs, Images)</h5>
        <p class="text-muted small">Upload files here to link them directly in your JSON data (e.g. <code>events/filename.pdf</code>).</p>
        <hr>
        <form action="upload_file.php" method="POST" enctype="multipart/form-data" class="row align-items-center">
            <div class="col-md-5">
                <input type="file" name="upload_file" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <select name="target_folder" class="form-select form-select-sm">
                    <option value="events">Save to: events/ (PDFs/Brochures)</option>
                    <option value="images">Save to: images/ (General Photos)</option>
                    <option value="pics">Save to: pics/ (Student Avatars)</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-custom btn-sm w-100">Upload File</button>
            </div>
            <?php
            if (isset($_GET['upload'])) {
                if ($_GET['upload'] == 'success') echo "<div class='text-success small mt-2'>File uploaded perfectly!</div>";
                if ($_GET['upload'] == 'error') echo "<div class='text-danger small mt-2'>Upload failed. Check permissions.</div>";
            }
            ?>
        </form>
    </div>

    <!-- System Settings -->
    <div class="row">
        <div class="col-md-12">
            <div class="admin-card">
                <h5>System Security & Settings</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6>Change Password</h6>
                        <form action="update_password.php" method="POST">
                            <div class="mb-2">
                                <label class="small fw-bold">Current Password</label>
                                <input type="password" name="current_password" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">New Password</label>
                                <input type="password" name="new_password" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm mt-2">Update Credentials</button>
                        </form>
                        <?php
                        if (isset($_GET['pass_update'])) {
                            if ($_GET['pass_update'] == 'success') echo "<div class='text-success small mt-2'>Password updated successfully!</div>";
                            if ($_GET['pass_update'] == 'invalid') echo "<div class='text-danger small mt-2'>Invalid current password!</div>";
                            if ($_GET['pass_update'] == 'error') echo "<div class='text-danger small mt-2'>Failed to update. Check config permissions.</div>";
                        }
                        ?>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3">
                            <h6 class="text-muted"><i class="fas fa-info-circle"></i> Security Advice</h6>
                            <p class="small text-muted">Passwords are hashed using <strong>BCRYPT</strong>. Ensure you use a strong password mixed with letters, numbers, and symbols.</p>
                            <p class="small text-muted">You will be logged out automatically after changing your password.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
