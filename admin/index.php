<?php
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

// Load Configuration
require_once 'config.php';

$error = "";

// Basic rate limiting (session-based, no external dependencies)
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_lockout'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check lockout
    if ($_SESSION['login_attempts'] >= 5 && time() < $_SESSION['login_lockout']) {
        $remaining = ceil(($_SESSION['login_lockout'] - time()) / 60);
        $error = "Too many failed attempts. Try again in {$remaining} minute(s).";
    } else {
        // Reset if lockout period has passed and a lockout was actually active
        if ($_SESSION['login_lockout'] > 0 && time() >= $_SESSION['login_lockout']) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_lockout'] = 0;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // CSRF validation
        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $error = "Invalid request. Please try again.";
        } elseif ($username === $ADMIN_USER && password_verify($password, $ADMIN_HASH)) {
            // Success — regenerate session ID to prevent fixation
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['login_attempts'] = 0;

            // Generate CSRF token for authenticated actions
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['login_lockout'] = time() + 900; // 15 minute lockout
            }
            $error = "Invalid credentials.";
        }
    }
}

// Generate CSRF token for the form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Admin Login | Dr. Somanath Tripathy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <link href="admin.css" rel="stylesheet"/>
</head>
<body class="admin-page admin-login">
    <!-- Floating Action Bar -->
    <div class="floating-action-bar">
        <a href="../index.php" class="floating-btn">
            <i class="fa fa-home"></i> <span>Back to Portfolio</span>
        </a>
        <button class="floating-btn dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">
            <i class="fa fa-moon-o" id="darkModeIcon"></i>
        </button>
    </div>

    <div class="login-card">
        <h3 class="text-center">Admin Access</h3>
        
        <?php if (isset($_GET['pass_update']) && $_GET['pass_update'] == 'success'): ?>
            <div class="alert alert-success">Password updated! Please login with your new credentials.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
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

    <script src="admin-common.js"></script>
</body>
</html>
