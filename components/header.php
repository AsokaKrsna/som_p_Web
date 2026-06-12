<?php
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

    <!-- Open Graph / Link Preview -->
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>"/>
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>"/>
    <meta property="og:site_name" content="Dr. Somanath Tripathy — IIT Patna"/>

    <!-- Favicon (inline SVG data URI — no external file needed) -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>"/>

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
                <a href="index.php#home" class="nav-side-item" data-section="home" data-tip="Top of page">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Home / Bio</span>
                    <span class="nav-side-tip">Top</span>
                </a>
                <a href="index.php#patents" class="nav-side-item" data-section="patents" data-tip="Filed &amp; granted patents">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Patents</span>
                    <span class="nav-side-tip">Patents</span>
                </a>
                <a href="index.php#projects" class="nav-side-item" data-section="projects" data-tip="Research &amp; consulting projects">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Projects</span>
                    <span class="nav-side-tip">Projects</span>
                </a>
                <a href="index.php#teaching" class="nav-side-item" data-section="teaching" data-tip="Courses taught">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Teaching</span>
                    <span class="nav-side-tip">Courses</span>
                </a>
                <a href="index.php#seminars" class="nav-side-item" data-section="seminars" data-tip="Talks &amp; invited lectures">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Seminars</span>
                    <span class="nav-side-tip">Talks</span>
                </a>
                <a href="index.php#memberships" class="nav-side-item" data-section="memberships" data-tip="Professional memberships">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Memberships</span>
                    <span class="nav-side-tip">Memberships</span>
                </a>
                <a href="index.php#editorship" class="nav-side-item" data-section="editorship" data-tip="Editorial roles">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Editorship</span>
                    <span class="nav-side-tip">Editorial</span>
                </a>
                <a href="index.php#publications" class="nav-side-item" data-section="publications" data-tip="Research papers &amp; articles">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Publications</span>
                    <span class="nav-side-tip">Papers</span>
                </a>
                <a href="index.php#awards" class="nav-side-item" data-section="awards" data-tip="Honors &amp; recognitions">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-text">Awards</span>
                    <span class="nav-side-tip">Awards</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Floating Action Bar (replaces top navbar) -->
    <div class="floating-action-bar" id="mainNav">
        <?php if (basename($_SERVER['PHP_SELF']) === 'research_group.php'): ?>
        <a href="index.php" class="floating-btn d-none d-lg-flex">
            <i class="fa fa-home"></i> <span>Home / Bio</span>
        </a>
        <?php else: ?>
        <a href="research_group.php" class="floating-btn d-none d-lg-flex">
            <i class="fa fa-users"></i> <span>Research Group</span>
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
                <a class="nav-link-large" href="index.php#home">Home / Bio</a>
                <a class="nav-link-large" href="index.php#patents">Patents</a>
                <a class="nav-link-large" href="index.php#projects">Projects</a>
                <a class="nav-link-large" href="index.php#teaching">Teaching</a>
                <a class="nav-link-large" href="index.php#seminars">Seminars</a>
                <a class="nav-link-large" href="index.php#memberships">Memberships</a>
                <a class="nav-link-large" href="index.php#editorship">Editorship</a>
                <a class="nav-link-large" href="index.php#publications">Publications</a>
                <a class="nav-link-large" href="index.php#awards">Awards</a>
            </div>
            <div class="nav-column">
                <span class="nav-label">Links</span>
                <?php if (basename($_SERVER['PHP_SELF']) === 'research_group.php'): ?>
                <a class="nav-link-large" href="index.php">Home / Bio</a>
                <?php else: ?>
                <a class="nav-link-large" href="research_group.php">Research Group</a>
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
