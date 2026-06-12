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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-color);
            overflow: hidden;
        }
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow), var(--glass-glow);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 2;
        }
        .login-card h3 {
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-main), var(--accent-cyan));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }
        .login-card label {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.3rem;
        }
        .login-card .form-control {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            color: var(--text-main);
        }
        .login-card .form-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
        }
        body.dark-mode .login-card .form-control {
            background: rgba(10, 14, 26, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .dark-mode-toggle-login {
            position: fixed;
            top: 1.5rem;
            right: 2rem;
            z-index: 100;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.2rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
        }
        .dark-mode-toggle-login:hover {
            color: var(--accent-cyan);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Dark mode toggle -->
    <button class="dark-mode-toggle-login" id="adminDarkModeToggle" aria-label="Toggle dark mode">
        <i class="fa fa-moon-o" id="adminDarkModeIcon"></i>
    </button>

    <div class="login-card">
        <h3 class="text-center">Admin Access</h3>
        
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

        <div class="text-center mt-4">
            <a href="../index.php" class="small" style="color: var(--text-muted);">← Back to Portfolio</a>
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
