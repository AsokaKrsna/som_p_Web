<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_file'])) {
    
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
    
    if (in_array($imageFileType, $allowed_extensions)) {
        if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file)) {
            header("Location: dashboard.php?upload=success");
            exit;
        }
    }
}

header("Location: dashboard.php?upload=error");
exit;
?>
