<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF validation
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        header("Location: dashboard.php?pass_update=error");
        exit;
    }

    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';

    // 1. Verify Current Password
    if (password_verify($current_pass, $ADMIN_HASH)) {
        
        // 2. Hash New Password
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        // 3. Generate New Config File Content
        $config_content = "<?php\n";
        $config_content .= "// Tripathy Portfolio CMS - Configuration\n";
        $config_content .= "// This file is updated automatically by the CMS.\n\n";
        $config_content .= "\$ADMIN_USER = \"admin\";\n";
        $config_content .= "\$ADMIN_HASH = '" . $new_hash . "';\n";
        $config_content .= "?>";

        // 4. Save to config.php
        if (file_put_contents('config.php', $config_content)) {
            // Logout user for security after password change
            session_destroy();
            header("Location: index.php?pass_update=success");
            exit;
        } else {
            header("Location: dashboard.php?pass_update=error");
            exit;
        }

    } else {
        // Current password invalid
        header("Location: dashboard.php?pass_update=invalid");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>
