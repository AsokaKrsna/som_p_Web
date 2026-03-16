<?php
session_start();

// Load Configuration
require_once 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $ADMIN_USER && password_verify($password, $ADMIN_HASH)) {
        $_SESSION['loggedin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Admin Login | Dr. Somanath Tripathy</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-color);
        }
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="mb-4 text-center">Admin Access</h3>
    
    <?php if (isset($_GET['pass_update']) && $_GET['pass_update'] == 'success'): ?>
        <div class="alert alert-success">Password updated! Please login with your new credentials.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-4">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>
</div>

</body>
</html>
