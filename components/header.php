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
        <div class="menu-toggle" id="mobile-menu">
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
    
