<?php
/**
 * Developed by Durjoy Majumdar
 * LinkedIn: https://www.linkedin.com/in/durjoy-majumdar/
 */
// Set a default page title if not provided by the parent file
$page_title = isset($page_title) ? $page_title : "Dr. Somanath Tripathy | Academic Portfolio";
$page_description = isset($page_description) ? $page_description : "Academic portfolio of Dr. Somanath Tripathy, Professor in the Department of Computer Science & Engineering at IIT Patna. Research interests include Cybersecurity, Malware Detection, Secure ML, and Blockchain.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>"/>
    <meta name="keywords" content="Somanath Tripathy, IIT Patna, Computer Science, Cybersecurity, Malware Detection, Blockchain, Lightweight Cryptography, Secure Machine Learning"/>
    <meta name="author" content="Dr. Somanath Tripathy"/>
    <meta name="developer" content="Durjoy Majumdar | https://www.linkedin.com/in/durjoy-majumdar/"/>

    <!-- Open Graph / Link Preview -->
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>"/>
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>"/>
    <meta property="og:site_name" content="Dr. Somanath Tripathy — IIT Patna"/>

    <!-- Favicon -->
    <link rel="icon" href="images/lab_logo.png" type="image/png"/>

    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="style/custom.css" rel="stylesheet"/>
</head>
<body>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fa fa-arrow-up"></i>
    </button>

    <!-- Side Navigation Toggle (tablets) -->
    <button class="nav-side-toggle" id="navSideToggle" aria-label="Toggle side navigation">
        <i class="fa fa-chevron-right"></i>
    </button>

    
    <!-- Side Navigation (desktop) -->
    <nav class="nav-side" id="navSide" aria-label="Section navigation">
        <?php if (!isset($hideSideNav) || !$hideSideNav): ?>
        <div class="nav-side-inner">
            <!-- Timeline track with gradient fill -->
            <div class="nav-side-timeline">
                <div class="nav-side-timeline-fill" id="navSideTimelineFill"></div>
            </div>
            <!-- Navigation items -->
            <div class="nav-side-items">
                <?php
                if (!isset($sideNavItems)) {
                    $sideNavItems = [
                        ['href' => 'index.php#home', 'section' => 'home', 'text' => 'Home', 'tip' => 'Top of page'],
                        ['href' => 'index.php#bio', 'section' => 'bio', 'text' => 'About', 'tip' => 'Editorial bio'],
                        ['href' => 'index.php#patents', 'section' => 'patents', 'text' => 'Patents', 'tip' => 'Filed & granted patents'],
                        ['href' => 'index.php#publications', 'section' => 'publications', 'text' => 'Publications', 'tip' => 'Research papers & articles'],
                        ['href' => 'index.php#projects', 'section' => 'projects', 'text' => 'Projects', 'tip' => 'Research & consulting projects'],
                        ['href' => 'index.php#teaching', 'section' => 'teaching', 'text' => 'Teaching', 'tip' => 'Courses taught'],
                        ['href' => 'index.php#seminars', 'section' => 'seminars', 'text' => 'Seminars', 'tip' => 'Talks & invited lectures'],
                        ['href' => 'index.php#memberships', 'section' => 'memberships', 'text' => 'Memberships', 'tip' => 'Professional memberships'],
                        ['href' => 'index.php#editorship', 'section' => 'editorship', 'text' => 'Editorship', 'tip' => 'Editorial roles'],
                        ['href' => 'index.php#admin-responsibilities', 'section' => 'admin-responsibilities', 'text' => 'Admin Roles', 'tip' => 'Administrative responsibilities'],
                        ['href' => 'index.php#other-responsibilities', 'section' => 'other-responsibilities', 'text' => 'Other Roles', 'tip' => 'Other responsibilities'],
                        ['href' => 'index.php#awards', 'section' => 'awards', 'text' => 'Awards', 'tip' => 'Honors & recognitions']
                    ];
                }
                foreach ($sideNavItems as $item):
                ?>
                <a href="<?= htmlspecialchars($item['href']) ?>" class="nav-side-item" data-section="<?= htmlspecialchars($item['section']) ?>" data-tip="<?= htmlspecialchars($item['tip']) ?>">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text"><?= htmlspecialchars($item['text']) ?></span>
                    <span class="nav-side-tip"><?= htmlspecialchars($item['tip']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Floating Action Bar (replaces top navbar) -->
    <div class="floating-action-bar" id="mainNav">
        <?php if (basename($_SERVER['PHP_SELF']) === 'cybersecurity-lab.php'): ?>
        <a href="index.php" class="floating-btn d-none d-lg-flex">
            <i class="fa fa-home"></i> <span>Homepage@som</span>
        </a>
        <?php else: ?>
        <a href="cybersecurity-lab.php" class="floating-btn d-none d-lg-flex">
            <i class="fa fa-users"></i> <span>Cybersecurity Lab</span>
        </a>
        <?php endif; ?>
        <a href="https://www.iitp.ac.in/" target="_blank" class="floating-btn d-none d-lg-flex">
            <i class="fa fa-external-link"></i> <span>IITP Home</span>
        </a>
        <button class="floating-btn dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode" title="Toggle dark mode">
            <i class="fa fa-moon-o" id="darkModeIcon"></i>
        </button>

        <div class="menu-toggle d-lg-none" id="mobile-menu" style="position: relative; top: 0; right: 0;">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>

    <!-- Glass Overlay Menu (mobile/tablet) -->
    <div class="glass-overlay" id="overlayMenu">
        <div class="overlay-content-wrapper">
            <div class="nav-column">
                <span class="nav-label">Sections</span>
                <a class="nav-link-large" href="index.php#home">Homepage@som</a>
                <a class="nav-link-large" href="index.php#bio">About</a>
                <a class="nav-link-large" href="index.php#patents">Patents</a>
                <a class="nav-link-large" href="index.php#publications">Publications</a>
                <a class="nav-link-large" href="index.php#projects">Projects</a>
                <a class="nav-link-large" href="index.php#teaching">Teaching</a>
                <a class="nav-link-large" href="index.php#seminars">Seminars</a>
                <a class="nav-link-large" href="index.php#memberships">Memberships</a>
                <a class="nav-link-large" href="index.php#editorship">Editorship</a>
                <a class="nav-link-large" href="index.php#admin-responsibilities">Admin Roles</a>
                <a class="nav-link-large" href="index.php#other-responsibilities">Other Roles</a>
                <a class="nav-link-large" href="index.php#awards">Awards</a>
            </div>
            <div class="nav-column">
                <span class="nav-label">Links</span>
                <?php if (basename($_SERVER['PHP_SELF']) === 'cybersecurity-lab.php'): ?>
                <a class="nav-link-large" href="index.php">Homepage@som</a>
                <?php else: ?>
                <a class="nav-link-large" href="cybersecurity-lab.php">Cybersecurity Lab</a>
                <?php endif; ?>
                <a class="nav-link-large" href="https://www.iitp.ac.in/" target="_blank">IITP Home</a>
                <button class="overlay-dark-mode-toggle" id="overlayDarkModeToggle">
                    <i class="fa fa-moon-o" id="overlayDarkModeIcon"></i>
                    <span id="overlayDarkModeLabel">Dark Mode</span>
                </button>
            </div>
        </div>
    </div>
    
    <main>
