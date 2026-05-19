<?php
// Set a default page title if not provided by the parent file
$page_title = isset($page_title) ? $page_title : "Dr. Somanath Tripathy | Academic Portfolio";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="style/custom.css" rel="stylesheet"/>
</head>
<body>

    <!-- Minimal Full Screen Navbar (Injected via PHP) -->
    <header class="custom-navbar" id="mainNav">
        <a class="navbar-brand-modern" href="index.php">Dr. Somanath Tripathy</a>
        
        <!-- Desktop Navigation -->
        <nav class="desktop-nav d-none d-lg-flex">
            <div class="nav-item">
                <a href="index.php" class="nav-link">Home / Bio</a>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Research</a>
                <div class="dropdown-menu glass-dropdown">
                    <a href="research_group.php" class="dropdown-item">Research Group</a>
                    <a href="publications.php" class="dropdown-item">Publications</a>
                    <a href="patents.php" class="dropdown-item">Patents</a>
                    <a href="projects.php" class="dropdown-item">Projects</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Academia</a>
                <div class="dropdown-menu glass-dropdown">
                    <a href="teaching.php" class="dropdown-item">Teaching</a>
                    <a href="seminar.php" class="dropdown-item">Seminars / Talks</a>
                    <a href="memberships.php" class="dropdown-item">Memberships</a>
                    <a href="editorship.php" class="dropdown-item">Editorship</a>
                    <a href="awards.php" class="dropdown-item">Awards</a>
                </div>
            </div>
            <div class="nav-item">
                <a href="https://www.iitp.ac.in/" target="_blank" class="nav-link">IITP Home</a>
            </div>
        </nav>

        <div class="menu-toggle d-lg-none" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </header>

    <!-- Glass Overlay Menu -->
    <div class="glass-overlay" id="overlayMenu">
        <div class="overlay-content-wrapper">
            <div class="nav-column">
                <span class="nav-label">About</span>
                <a class="nav-link-large" href="https://www.iitp.ac.in/" target="_blank">IITP Home</a>
                <a class="nav-link-large" href="index.php">Home / Bio</a>
            </div>
            <div class="nav-column">
                <span class="nav-label">Research Output</span>
                <a class="nav-link-large" href="research_group.php">Research Group</a>
                <a class="nav-link-large" href="publications.php">Publications</a>
                <a class="nav-link-large" href="patents.php">Patents</a>
                <a class="nav-link-large" href="projects.php">Projects</a>
            </div>
            <div class="nav-column">
                <span class="nav-label">Academia</span>
                <a class="nav-link-large" href="teaching.php">Teaching</a>
                <a class="nav-link-large" href="seminar.php">Seminars / Talks</a>
                <a class="nav-link-large" href="memberships.php">Memberships</a>
                <a class="nav-link-large" href="editorship.php">Editorship</a>
                <a class="nav-link-large" href="awards.php">Awards</a>
            </div>
        </div>
    </div>
    
