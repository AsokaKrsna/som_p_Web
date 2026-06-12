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

    <?php if (!isset($hideSideNav) || !$hideSideNav): ?>
    <!-- Right Viewport Section Indicator -->
    <nav class="scroll-indicator" id="scrollIndicator">
        <div class="scroll-indicator-track" role="tablist" aria-label="Section navigation">
            <span class="scroll-indicator-dot" data-target="home" role="tab" tabindex="0" aria-label="Home section"></span>
            <span class="scroll-indicator-dot" data-target="bio" role="tab" tabindex="0" aria-label="About section"></span>
            <span class="scroll-indicator-dot" data-target="publications" role="tab" tabindex="0" aria-label="Publications section"></span>
            <span class="scroll-indicator-dot" data-target="patents" role="tab" tabindex="0" aria-label="Patents section"></span>
            <span class="scroll-indicator-dot" data-target="projects" role="tab" tabindex="0" aria-label="Projects section"></span>
            <span class="scroll-indicator-dot" data-target="teaching" role="tab" tabindex="0" aria-label="Teaching section"></span>
            <span class="scroll-indicator-dot" data-target="seminars" role="tab" tabindex="0" aria-label="Seminars section"></span>
            <span class="scroll-indicator-dot" data-target="memberships" role="tab" tabindex="0" aria-label="Memberships section"></span>
            <span class="scroll-indicator-dot" data-target="editorship" role="tab" tabindex="0" aria-label="Editorship section"></span>
            <span class="scroll-indicator-dot" data-target="awards" role="tab" tabindex="0" aria-label="Awards section"></span>
        </div>
        <div class="scroll-indicator-label">
            <span class="scroll-indicator-label-text" id="scrollIndicatorLabel">Home</span>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Side Navigation (desktop) -->
    <nav class="nav-side" id="navSide">
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
                    <span class="nav-side-num">01</span>
                    <span class="nav-side-text">Home / Bio</span>
                    <span class="nav-side-tip">Top</span>
                </a>
                <a href="index.php#publications" class="nav-side-item" data-section="publications" data-tip="Research papers &amp; articles">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">02</span>
                    <span class="nav-side-text">Publications</span>
                    <span class="nav-side-tip">Papers</span>
                </a>
                <a href="index.php#patents" class="nav-side-item" data-section="patents" data-tip="Filed &amp; granted patents">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">03</span>
                    <span class="nav-side-text">Patents</span>
                    <span class="nav-side-tip">Patents</span>
                </a>
                <a href="index.php#projects" class="nav-side-item" data-section="projects" data-tip="Research &amp; consulting projects">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">04</span>
                    <span class="nav-side-text">Projects</span>
                    <span class="nav-side-tip">Projects</span>
                </a>
                <a href="index.php#teaching" class="nav-side-item" data-section="teaching" data-tip="Courses taught">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">05</span>
                    <span class="nav-side-text">Teaching</span>
                    <span class="nav-side-tip">Courses</span>
                </a>
                <a href="index.php#seminars" class="nav-side-item" data-section="seminars" data-tip="Talks &amp; invited lectures">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">06</span>
                    <span class="nav-side-text">Seminars</span>
                    <span class="nav-side-tip">Talks</span>
                </a>
                <a href="index.php#memberships" class="nav-side-item" data-section="memberships" data-tip="Professional memberships">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">07</span>
                    <span class="nav-side-text">Memberships</span>
                    <span class="nav-side-tip">Memberships</span>
                </a>
                <a href="index.php#editorship" class="nav-side-item" data-section="editorship" data-tip="Editorial roles">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">08</span>
                    <span class="nav-side-text">Editorship</span>
                    <span class="nav-side-tip">Editorial</span>
                </a>
                <a href="index.php#awards" class="nav-side-item" data-section="awards" data-tip="Honors &amp; recognitions">
                    <span class="nav-side-dot"></span>
                    <span class="nav-side-num">09</span>
                    <span class="nav-side-text">Awards</span>
                    <span class="nav-side-tip">Awards</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Minimal Top Navbar -->
    <header class="custom-navbar" id="mainNav">
        <a class="navbar-brand-modern" href="index.php">Dr. Somanath Tripathy</a>
        
        <!-- Desktop Navigation (simplified: only Research Group & IITP Home) -->
        <nav class="desktop-nav d-none d-lg-flex">
            <div class="nav-item">
                <a href="research_group.php" class="nav-link">
                    <i class="fa fa-users me-1"></i> Research Group
                </a>
            </div>
            <div class="nav-item">
                <a href="https://www.iitp.ac.in/" target="_blank" class="nav-link">
                    <i class="fa fa-external-link me-1"></i> IITP Home
                </a>
            </div>
            <div class="nav-item">
                <button class="dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode" title="Toggle dark mode">
                    <i class="fa fa-moon-o" id="darkModeIcon"></i>
                </button>
            </div>
        </nav>

        <div class="menu-toggle d-lg-none" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </header>

    <!-- Glass Overlay Menu (mobile/tablet) -->
    <div class="glass-overlay" id="overlayMenu">
        <div class="overlay-content-wrapper">
            <div class="nav-column">
                <span class="nav-label">Sections</span>
                <a class="nav-link-large" href="index.php#home">Home / Bio</a>
                <a class="nav-link-large" href="index.php#publications">Publications</a>
                <a class="nav-link-large" href="index.php#patents">Patents</a>
                <a class="nav-link-large" href="index.php#projects">Projects</a>
                <a class="nav-link-large" href="index.php#teaching">Teaching</a>
                <a class="nav-link-large" href="index.php#seminars">Seminars</a>
                <a class="nav-link-large" href="index.php#memberships">Memberships</a>
                <a class="nav-link-large" href="index.php#editorship">Editorship</a>
                <a class="nav-link-large" href="index.php#awards">Awards</a>
            </div>
            <div class="nav-column">
                <span class="nav-label">Links</span>
                <a class="nav-link-large" href="research_group.php">Research Group</a>
                <a class="nav-link-large" href="https://www.iitp.ac.in/" target="_blank">IITP Home</a>
                <button class="overlay-dark-mode-toggle" id="overlayDarkModeToggle">
                    <i class="fa fa-moon-o" id="overlayDarkModeIcon"></i>
                    <span id="overlayDarkModeLabel">Dark Mode</span>
                </button>
            </div>
        </div>
    </div>
    
