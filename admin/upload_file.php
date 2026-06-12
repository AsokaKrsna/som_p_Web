<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_file'])) {
    
    // CSRF validation
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        header("Location: dashboard.php?upload=error");
        exit;
    }

    // File size limit (10 MB)
    if ($_FILES['upload_file']['size'] > 10 * 1024 * 1024) {
        header("Location: dashboard.php?upload=too_large");
        exit;
    }

    // Whitelist allowed target folders
    $allowed_folders = ['events', 'images', 'pics'];
    $target_folder = $_POST['target_folder'] ?? '';
    
    if (!in_array($target_folder, $allowed_folders)) {
        header("Location: dashboard.php?upload=error");
        exit;
    }
    
    // Directory is relative to the root _som folder (not the admin folder)
    $target_dir = "../" . $target_folder . "/";
    
    // Sanitize filename
    $file_name = basename($_FILES["upload_file"]["name"]);
    $file_name = preg_replace("/[^a-zA-Z0-9\._-]/", "", $file_name); // Strip weird characters
    
    $target_file = $target_dir . $file_name;
    
    // Allow certain file formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_extensions = ["jpg", "jpeg", "png", "gif", "pdf"];
    
    // Secure MIME type verification
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES["upload_file"]["tmp_name"]);
    finfo_close($finfo);
    
    $allowed_mimes = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "application/pdf"
    ];
    
    if (in_array($imageFileType, $allowed_extensions) && in_array($mime, $allowed_mimes)) {
        if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file)) {
            header("Location: dashboard.php?upload=success");
            exit;
        }
    }
}

header("Location: dashboard.php?upload=error");
exit;
?>
