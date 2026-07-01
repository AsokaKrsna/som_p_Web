<?php
$page_title = "Cybersecurity Lab | Dr. Somanath Tripathy";
$sideNavItems = [
    ['href' => '#lab-hero', 'section' => 'lab-hero', 'text' => 'Lab Home', 'tip' => 'Top of page'],
    ['href' => '#head-of-research', 'section' => 'head-of-research', 'text' => 'Research Group', 'tip' => 'Group Head'],
    ['href' => '#current-students', 'section' => 'current-students', 'text' => 'Current Students', 'tip' => 'Ph.D. & M.Tech'],
    ['href' => '#alumni', 'section' => 'alumni', 'text' => 'Alumni', 'tip' => 'Past Members'],
    ['href' => '#research-outcome', 'section' => 'research-outcome', 'text' => 'Outcome', 'tip' => 'Projects & Pubs'],
    ['href' => '#achievements', 'section' => 'achievements', 'text' => 'Achievements', 'tip' => 'Lab Achievements']
];
include 'components/header.php';

// Safe JSON loader
function loadJsonData($file, $visibilityKey = null) {
    $path = 'data/' . $file;
    if (!file_exists($path)) return [];
    $content = @file_get_contents($path);
    if ($content === false) return [];
    $data = json_decode($content, true);
    $data = (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : [];

    if ($visibilityKey) {
        $isDictOfArrays = false;
        foreach ($data as $key => $val) {
            if (is_array($val) && !is_numeric($key)) {
                $isDictOfArrays = true;
                break;
            }
        }

        if ($isDictOfArrays) {
            foreach ($data as $cat => &$items) {
                if (is_array($items)) {
                    $items = array_filter($items, function($item) use ($visibilityKey) {
                        return !isset($item[$visibilityKey]) || $item[$visibilityKey] === true || $item[$visibilityKey] === "true";
                    });
                    $items = array_values($items);
                }
            }
        } else {
            $data = array_filter($data, function($item) use ($visibilityKey) {
                return !isset($item[$visibilityKey]) || $item[$visibilityKey] === true || $item[$visibilityKey] === "true";
            });
            $data = array_values($data);
        }
    }
    return $data;
}

$members = loadJsonData('research_group.json');
$projects = loadJsonData('projects.json', 'show_lab');
$publications = loadJsonData('publications.json', 'show_lab');
$announcements = loadJsonData('announcements.json');
$labContent = loadJsonData('lab_content.json');
$achievementsData = loadJsonData('achievements.json');
$achievements = $achievementsData['achievements'] ?? [];

// Extract lab content blocks for easy use
$heroContent = $labContent['hero'][0] ?? [];
$aboutContent = $labContent['about'][0] ?? [];
$joinUsContent = $labContent['join_us'][0] ?? [];
$headContent = $labContent['head_of_research'][0] ?? [];
$researchAreas = $labContent['research_areas'] ?? [];
$openResources = $labContent['open_resources'] ?? [];
$fundingSponsors = $labContent['funding_sponsors'] ?? [];
$gallery = $labContent['gallery'] ?? [];

// Reversing arrays for reverse chronological order
$members['phd'] = array_reverse($members['phd'] ?? []);
$members['mtech'] = array_reverse($members['mtech'] ?? []);
$members['past_phd'] = array_reverse($members['past_phd'] ?? []);
$members['past_mtech'] = array_reverse($members['past_mtech'] ?? []);

function getInitialsAvatarUrl($name) {
    // Generate initials avatar with a background color matching the theme
    $nameEncoded = urlencode($name);
    return "https://ui-avatars.com/api/?name={$nameEncoded}&background=0891b2&color=fff&size=150";
}

// Helper function to render a premium student card
function renderStudentCard($member, $index, $isPast = false) {
    $name = $member['name'] ?? '';
    $fallbackImage = getInitialsAvatarUrl($name);
    $imageSrc = !empty($member['image']) ? htmlspecialchars($member['image']) : $fallbackImage;
    $email = $member['email'] ?? '';
    $researchArea = $member['research_area'] ?? '';
    
    // For past members
    $passingYear = $member['passing_year'] ?? '';
    $currentAffiliation = $member['current_affiliation'] ?? '';
    $thesis = $member['thesis'] ?? '';
    ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="student-card h-100">
            <div class="card-index-badge"><?= $index ?></div>
            <img src="<?= $imageSrc ?>" class="avatar" alt="<?= htmlspecialchars($name) ?>" onerror="this.onerror=null; this.src='<?= $fallbackImage ?>';">
            <h5><?= htmlspecialchars($name) ?></h5>
            
            <?php if ($isPast): ?>
                <?php if ($passingYear): ?>
                    <div class="affiliation" style="font-weight: 500;">Batch of <?= htmlspecialchars($passingYear) ?></div>
                <?php endif; ?>
                <?php if ($currentAffiliation): ?>
                    <div class="affiliation mt-1"><i class="fa fa-building-o"></i> <?= htmlspecialchars($currentAffiliation) ?></div>
                <?php endif; ?>
                <?php if ($thesis): ?>
                    <div class="research-badge mt-2" style="white-space: normal; height: auto;">Thesis: <?= htmlspecialchars($thesis) ?></div>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($email): ?>
                    <div class="affiliation"><i class="fa fa-envelope-o"></i> <?= htmlspecialchars($email) ?></div>
                <?php endif; ?>
                <?php if ($researchArea): ?>
                    <div class="research-badge mt-2" style="white-space: normal; height: auto;"><?= htmlspecialchars($researchArea) ?></div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($isPast && $email): ?>
                <div class="email mt-2"><i class="fa fa-envelope-o"></i> <?= htmlspecialchars($email) ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// Helper function to render a slim, sleek member row for past M.Tech
function renderSleekAlumniCard($member, $index) {
    $name = $member['name'] ?? '';
    $fallbackImage = getInitialsAvatarUrl($name);
    $imageSrc = !empty($member['image']) ? htmlspecialchars($member['image']) : $fallbackImage;
    
    $passingYear = $member['passing_year'] ?? '';
    $thesis = $member['thesis'] ?? '';
    ?>
    <div class="col-md-6 mb-3">
        <div class="alumni-card-sleek h-100">
            <div class="index-num"><?= $index ?>.</div>
            <img src="<?= $imageSrc ?>" class="sleek-avatar" alt="<?= htmlspecialchars($name) ?>" onerror="this.onerror=null; this.src='<?= $fallbackImage ?>';">
            <div class="sleek-info">
                <h6><?= htmlspecialchars($name) ?></h6>
                <?php if ($passingYear): ?>
                    <div class="text-muted" style="font-size: 0.85rem;">Batch of <?= htmlspecialchars($passingYear) ?></div>
                <?php endif; ?>
                <?php if ($thesis): ?>
                    <span class="badge mt-1" style="background: rgba(8, 145, 178, 0.1); color: var(--accent-blue); white-space: normal; text-align: left; line-height: 1.4;"><?= htmlspecialchars($thesis) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

function renderPublicationTable($items) {
    $counter = count($items);
    foreach ($items as $pub): 
        $parts = [];
        
        if(!empty($pub['author'])){
            $parts[] = $pub['author'];
        }
        
        if(!empty($pub['link'])){
            $titleText = htmlspecialchars($pub['title'] ?? '');
            if ($titleText !== '') {
                $parts[] = '<a href="' . htmlspecialchars($pub['link']) . '" target="_blank" rel="noopener noreferrer"><strong>' . $titleText . '</strong></a>';
            }
        } else if(!empty($pub['title'])) {
            $parts[] = '<strong>' . htmlspecialchars($pub['title']) . '</strong>';
        }
        
        if(!empty($pub['published_at'])){
            $parts[] = $pub['published_at'];
        }
        
        if(!empty($pub['doi'])){
            $doiUrl = str_replace('https://doi.org/', 'https://doi.org/', $pub['doi']);
            $parts[] = '<a href="' . htmlspecialchars($doiUrl) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($pub['doi']) . '</a>';
        }

        // Clean up each part to remove stray commas/spaces
        $cleanParts = array_map(function($part) {
            $part = trim($part);
            $part = preg_replace('/^,*\s*/', '', $part); // Remove leading commas
            $part = preg_replace('/\s*,*$/', '', $part); // Remove trailing commas
            $part = preg_replace('/\s*,\s*,+/', ',', $part); // Replace multiple commas with one
            $part = preg_replace('/\s+,/', ',', $part); // Remove spaces before commas
            return $part;
        }, $parts);

        // Join parts and do one final cleanup for any edge cases
        $fullText = implode(', ', $cleanParts);
        $fullText = preg_replace('/,{2,}/', ',', $fullText);
        $fullText = preg_replace('/\s+,/', ',', $fullText);

        $impactFactorHtml = '';
        if(!empty($pub['impact_factor'])){
            $impactFactorHtml = '<br><span class="impact-factor-badge">' . htmlspecialchars($pub['impact_factor']) . '</span>';
        }
    ?>
    <tr><td>
        <p><strong><?= $counter-- ?>.</strong> <?= $fullText ?>.<?= $impactFactorHtml ?></p>
    </td></tr>
    <?php endforeach;
}
?>

<?php
// Pre-compute stats for the hero counters
$statPhd = count($members['phd'] ?? []);
$statMtech = count($members['mtech'] ?? []);
$statPastPhd = count($members['past_phd'] ?? []);
$statPastMtech = count($members['past_mtech'] ?? []);

// Count publications (flatten all categories)
$totalPubs = 0;
if (is_array($publications)) {
    foreach ($publications as $cat => $items) {
        if (is_array($items)) $totalPubs += count($items);
    }
}

// Count projects
$totalProjects = 0;
if (is_array($projects)) {
    foreach ($projects as $cat => $items) {
        if (is_array($items)) $totalProjects += count($items);
    }
}
?>

<!-- Dedicated Hero Section for Lab -->
<section id="lab-hero" class="lab-hero-section">
    <canvas id="particleCanvas" class="particle-canvas"></canvas>
    <div class="hero-glow-orb-1"></div>
    <div class="hero-glow-orb-2"></div>

    <div class="container lab-hero-content">
        <!-- Logo Placeholder -->
        <div class="hero-lab-logo">
            <img src="images/lab_logo.png" alt="Lab Logo" class="lab-logo-img">
        </div>

        <h1 class="lab-hero-title"><?= htmlspecialchars($heroContent['title'] ?? 'Cybersecurity Lab') ?></h1>
        <h2 class="lab-hero-subtitle"><?= htmlspecialchars($heroContent['subtitle'] ?? '') ?></h2>
        <?php if (!empty($heroContent['location'])): ?>
            <p class="lab-hero-location"><i class="fa fa-map-marker" style="margin-right: 6px;"></i><?= htmlspecialchars($heroContent['location']) ?></p>
        <?php endif; ?>
        <p class="lab-hero-tagline"><?= htmlspecialchars($heroContent['tagline'] ?? '') ?></p>

        <!-- Stats Row -->
        <div class="lab-stats-row">
            <div class="lab-stat-item">
                <div class="stat-value-wrap"><span class="lab-stat-number" data-target="<?= $statPastPhd ?>">0</span><span class="lab-stat-plus">+</span></div>
                <span class="lab-stat-label">Ph.D. Alumni</span>
            </div>
            <div class="lab-stat-divider"></div>
            <div class="lab-stat-item">
                <div class="stat-value-wrap"><span class="lab-stat-number" data-target="<?= $statPastMtech ?>">0</span><span class="lab-stat-plus">+</span></div>
                <span class="lab-stat-label">M.Tech Alumni</span>
            </div>
            <div class="lab-stat-divider"></div>
            <div class="lab-stat-item">
                <div class="stat-value-wrap"><span class="lab-stat-number" data-target="<?= $statPhd + $statMtech ?>">0</span></div>
                <span class="lab-stat-label">Active Scholars</span>
            </div>
            <div class="lab-stat-divider"></div>
            <div class="lab-stat-item">
                <div class="stat-value-wrap"><span class="lab-stat-number" data-target="<?= $totalPubs ?>">0</span><span class="lab-stat-plus">+</span></div>
                <span class="lab-stat-label">Publications</span>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="lab-hero-cta">
            <a href="#about-lab" class="btn btn-custom lab-cta-primary">About Lab <i class="fa fa-arrow-down ms-2"></i></a>
            <a href="#join-us" class="btn lab-cta-glow">Join Us</a>
        </div>
    </div>
</section>

<!-- Announcement Marquee -->
<?php if (!empty($announcements)): ?>
<div class="announcement-bar-marquee">
    <div class="announcement-content">
        <?php 
        $texts = [];
        foreach($announcements as $a) {
            $text = htmlspecialchars($a['text']);
            if (!empty($a['link'])) {
                $text = "<a href='".htmlspecialchars($a['link'])."' target='_blank'>{$text}</a>";
            }
            if (!empty($a['badge'])) {
                $text = "<span class='announcement-badge'>".htmlspecialchars($a['badge'])."</span> " . $text;
            }
            $texts[] = $text;
        }
        echo implode(" &nbsp;&nbsp;|&nbsp;&nbsp; ", $texts);
        ?>
    </div>
</div>
<?php endif; ?>

<!-- About the Lab & Join Us -->
<section id="about-lab" class="bio-section pt-5 pb-5">
    <div class="container">
        <div class="row g-5 align-items-stretch">
            <div class="col-lg-7">
                <div class="about-lab-content h-100 pe-lg-4">
                    <h3 class="section-title text-start mb-4">About the Lab</h3>
                    <p class="about-lab-text">
                        <?= htmlspecialchars($aboutContent['description'] ?? '') ?>
                    </p>
                    <!-- Research Area Mini Cards -->
                    <div class="research-area-grid mt-4 pt-2">
                        <?php foreach($researchAreas as $area): ?>
                        <div class="research-area-card premium-card">
                            <div class="research-area-icon"><i class="fa <?= htmlspecialchars($area['icon'] ?? 'fa-circle') ?>"></i></div>
                            <div class="research-area-info">
                                <h6><?= htmlspecialchars($area['title'] ?? '') ?></h6>
                                <p><?= htmlspecialchars($area['description'] ?? '') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5" id="join-us">
                <div class="join-us-premium-card h-100">
                    <div class="join-us-icon-wrapper mb-3">
                        <i class="fa fa-users"></i>
                    </div>
                    <h3 class="mb-3 text-white" style="font-weight: 700;"><?= htmlspecialchars($joinUsContent['heading'] ?? 'Join Our Group') ?></h3>
                    <p class="text-white-50 mb-3" style="font-size: 0.95rem; line-height: 1.6; color: rgba(255,255,255,0.85) !important;">
                        <?= htmlspecialchars($joinUsContent['description'] ?? '') ?>
                    </p>
                    <?php if (!empty($joinUsContent['note'])): ?>
                    <p class="text-white-50 mb-4" style="font-size: 0.90rem; line-height: 1.5; color: rgba(255,255,255,0.7) !important;">
                        <em><?= htmlspecialchars($joinUsContent['note']) ?></em>
                    </p>
                    <?php endif; ?>
                    <div class="d-flex flex-column gap-3 mt-auto">
                        <?php if (!empty($joinUsContent['form_link'])): ?>
                        <a href="<?= htmlspecialchars($joinUsContent['form_link']) ?>" target="_blank" class="btn btn-light btn-lg text-primary fw-bold" style="border-radius: 8px; color: #0e7490 !important;">
                            <i class="fa fa-file-text-o me-2"></i> Application Form
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($joinUsContent['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($joinUsContent['email']) ?>" class="btn btn-outline-light btn-lg fw-bold" style="border-radius: 8px;">
                            <i class="fa fa-envelope-o me-2"></i> Email Us
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Head of Research Group -->
<section id="head-of-research" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Research Group</h2>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <div class="student-card" style="flex-direction: row; align-items: center; text-align: left; padding: 2rem;">
                    <img class="avatar mb-0 me-4" src="pics/som-pic.png" alt="<?= htmlspecialchars($headContent['name'] ?? '') ?>" style="width: 120px; height: 120px;">
                    <div>
                        <h4 class="mb-1" style="font-weight: 800; color: var(--text-main);"><?= htmlspecialchars($headContent['name'] ?? '') ?></h4>
                        <p class="mb-2" style="color: var(--text-muted);"><?= htmlspecialchars($headContent['designation'] ?? '') ?></p>
                        <p class="mb-2 small" style="color: var(--text-muted);"><?= htmlspecialchars($headContent['bio'] ?? '') ?></p>
                        <p class="email m-0">
                            <?php if (!empty($headContent['scholar_link'])): ?>
                            <a href="<?= htmlspecialchars($headContent['scholar_link']) ?>" target="_blank" class="me-3" style="text-decoration: none;"><i class="fa fa-graduation-cap"></i> Scholar</a>
                            <?php endif; ?>
                            <?php if (!empty($headContent['website_link'])): ?>
                            <a href="<?= htmlspecialchars($headContent['website_link']) ?>" class="me-3" style="text-decoration: none;"><i class="fa fa-globe"></i> Website</a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Current Students -->
<section id="current-students" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Current Students</h2>
        
        <ul class="nav nav-tabs custom-tabs justify-content-center mb-4" id="currentStudentsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="phd-tab" data-bs-toggle="tab" data-bs-target="#phd-tab-pane" type="button" role="tab" aria-controls="phd-tab-pane" aria-selected="true">Ph.D. Scholars</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mtech-tab" data-bs-toggle="tab" data-bs-target="#mtech-tab-pane" type="button" role="tab" aria-controls="mtech-tab-pane" aria-selected="false">M.Tech Scholars</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="phd-tab-pane" role="tabpanel">
                <div class="row">
                    <?php 
                    if (!empty($members['phd'])) {
                        $idx = count($members['phd']);
                        foreach($members['phd'] as $member) renderStudentCard($member, $idx--);
                    } else {
                        echo "<p class='text-center text-muted m-0 py-3 w-100'>Currently no Ph.D. students listed.</p>";
                    }
                    ?>
                </div>
            </div>
            
            <div class="tab-pane fade" id="mtech-tab-pane" role="tabpanel">
                <div class="row">
                    <?php 
                    if (!empty($members['mtech'])) {
                        $idx = count($members['mtech']);
                        foreach($members['mtech'] as $member) renderStudentCard($member, $idx--);
                    } else {
                        echo "<p class='text-center text-muted m-0 py-3 w-100'>Currently no M.Tech students listed.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Alumni -->
<section id="alumni" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Alumni</h2>
        
        <ul class="nav nav-tabs custom-tabs justify-content-center mb-4" id="alumniTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="past-phd-tab" data-bs-toggle="tab" data-bs-target="#past-phd-tab-pane" type="button" role="tab" aria-controls="past-phd-tab-pane" aria-selected="true">Past Ph.D.</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-mtech-tab" data-bs-toggle="tab" data-bs-target="#past-mtech-tab-pane" type="button" role="tab" aria-controls="past-mtech-tab-pane" aria-selected="false">Past M.Tech</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="past-phd-tab-pane" role="tabpanel">
                <div class="row">
                    <?php 
                    if (!empty($members['past_phd'])): 
                        $idx = count($members['past_phd']);
                        foreach($members['past_phd'] as $member) renderStudentCard($member, $idx--, true);
                    else:
                        echo "<p class='text-center text-muted m-0 py-3 w-100'>No past Ph.D. students listed.</p>";
                    endif;
                    ?>
                </div>
            </div>
            
            <div class="tab-pane fade" id="past-mtech-tab-pane" role="tabpanel">
                <div class="row">
                    <?php 
                    if (!empty($members['past_mtech'])) {
                        $idx = count($members['past_mtech']);
                        foreach($members['past_mtech'] as $member) renderSleekAlumniCard($member, $idx--);
                    } else {
                        echo "<p class='text-center text-muted m-0 py-3 w-100'>No past M.Tech students listed.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Research Outcome -->
<section id="research-outcome" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Research Outcome</h2>
        
        <ul class="nav nav-tabs custom-tabs justify-content-center mb-4" id="researchOutcomeTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects-tab-pane" type="button" role="tab" aria-controls="projects-tab-pane" aria-selected="true">Lab Projects</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pubs-tab" data-bs-toggle="tab" data-bs-target="#pubs-tab-pane" type="button" role="tab" aria-controls="pubs-tab-pane" aria-selected="false">Lab Publications</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="projects-tab-pane" role="tabpanel">
                <div class="glass-directory p-4">
                    <?php if (empty($projects['ongoing']) && empty($projects['completed'])): ?>
                        <p class='text-center text-muted m-0 py-3'>No lab-specific projects listed.</p>
                    <?php else: ?>
                        <ul class="nav nav-tabs custom-tabs justify-content-center mb-4" id="projectSubTabs" role="tablist" style="border-bottom: none; transform: scale(0.9);">
                            <?php if (!empty($projects['ongoing'])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing-pane" type="button" role="tab" aria-selected="true">Ongoing</button>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($projects['completed'])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= empty($projects['ongoing']) ? 'active' : '' ?>" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-selected="<?= empty($projects['ongoing']) ? 'true' : 'false' ?>">Completed</button>
                            </li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="tab-content">
                            <?php if (!empty($projects['ongoing'])): ?>
                            <div class="tab-pane fade show active" id="ongoing-pane" role="tabpanel">
                                <div class="table-responsive"><table class="table custom-table">
                                    <thead><tr><th>Title</th><th>Role</th><th>Funding Agency</th><th>Amount</th><th>Duration</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($projects['ongoing'] as $proj): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($proj['title'] ?? '') ?></strong></td>
                                            <td><?= htmlspecialchars($proj['role'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($proj['funding_agency'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($proj['amount'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($proj['duration'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($projects['completed'])): ?>
                            <div class="tab-pane fade <?= empty($projects['ongoing']) ? 'show active' : '' ?>" id="completed-pane" role="tabpanel">
                                <div class="table-responsive"><table class="table custom-table">
                                    <thead><tr><th>Title</th><th>Role</th><th>Funding Agency</th><th>Amount</th><th>Duration</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($projects['completed'] as $proj): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($proj['title'] ?? '') ?></strong></td>
                                            <td><?= htmlspecialchars($proj['role'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($proj['funding_agency'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($proj['amount'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($proj['duration'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="pubs-tab-pane" role="tabpanel">
                <div class="glass-directory p-4">
                    <?php if (empty($publications['journals']) && empty($publications['conferences']) && empty($publications['preprints'])): ?>
                        <p class='text-center text-muted m-0 py-3'>No lab-specific publications listed.</p>
                    <?php else: ?>
                        <ul class="nav nav-tabs custom-tabs justify-content-center mb-4" id="pubSubTabs" role="tablist" style="border-bottom: none; transform: scale(0.9);">
                            <?php if (!empty($publications['journals'])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="journals-tab" data-bs-toggle="tab" data-bs-target="#journals-pane" type="button" role="tab" aria-selected="true">Journals</button>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($publications['conferences'])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= empty($publications['journals']) ? 'active' : '' ?>" id="conferences-tab" data-bs-toggle="tab" data-bs-target="#conferences-pane" type="button" role="tab" aria-selected="<?= empty($publications['journals']) ? 'true' : 'false' ?>">Conferences</button>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($publications['preprints'])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= (empty($publications['journals']) && empty($publications['conferences'])) ? 'active' : '' ?>" id="preprints-tab" data-bs-toggle="tab" data-bs-target="#preprints-pane" type="button" role="tab" aria-selected="<?= (empty($publications['journals']) && empty($publications['conferences'])) ? 'true' : 'false' ?>">Pre-Prints</button>
                            </li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="tab-content">
                            <?php if (!empty($publications['journals'])): ?>
                            <div class="tab-pane fade show active" id="journals-pane" role="tabpanel">
                                <div class="table-responsive"><table class="table custom-table">
                                    <tbody><?php renderPublicationTable($publications['journals']); ?></tbody>
                                </table></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($publications['conferences'])): ?>
                            <div class="tab-pane fade <?= empty($publications['journals']) ? 'show active' : '' ?>" id="conferences-pane" role="tabpanel">
                                <div class="table-responsive"><table class="table custom-table">
                                    <tbody><?php renderPublicationTable($publications['conferences']); ?></tbody>
                                </table></div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($publications['preprints'])): ?>
                            <div class="tab-pane fade <?= (empty($publications['journals']) && empty($publications['conferences'])) ? 'show active' : '' ?>" id="preprints-pane" role="tabpanel">
                                <div class="table-responsive"><table class="table custom-table">
                                    <tbody><?php renderPublicationTable($publications['preprints']); ?></tbody>
                                </table></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Achievements Section -->
<?php if (!empty($achievements)): ?>
<section id="achievements" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Achievements</h2>
        <div class="row g-4 mt-2">
            <?php foreach ($achievements as $ach): ?>
            <div class="col-12">
                <div class="admin-card h-100 shadow-sm p-4" style="border-left: 4px solid var(--accent-blue);">
                    <h5 class="fw-bold mb-2"><?= htmlspecialchars($ach['title'] ?? '') ?></h5>
                    <p class="text-muted small mb-3"><i class="fa fa-calendar me-2"></i><?= htmlspecialchars($ach['date'] ?? '') ?></p>
                    <p class="mb-3"><?= htmlspecialchars($ach['description'] ?? '') ?></p>
                    <?php if (!empty($ach['link'])): ?>
                    <a href="<?= htmlspecialchars($ach['link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Read More <i class="fa fa-external-link ms-1"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Resources Section (Mock) -->
<!-- 
<section class="bio-section pt-5 pb-5" style="background: var(--bg-alt); position: relative; overflow: hidden;">
    <div style="position: absolute; top: -150px; left: -100px; width: 400px; height: 400px; background: rgba(8,145,178,0.05); filter: blur(100px); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -150px; right: -100px; width: 400px; height: 400px; background: rgba(59,130,246,0.05); filter: blur(100px); border-radius: 50%;"></div>
    
    <div class="container position-relative z-1">
        <h3 class="section-title text-center mb-5">Open Resources & Datasets</h3>
        <div class="row g-5 justify-content-center">
            <?php foreach($openResources as $res): ?>
            <div class="col-md-5">
                <div class="resource-card p-5 h-100 text-center d-flex flex-column align-items-center justify-content-center position-relative">
                    <div class="resource-icon-wrap mb-4">
                        <i class="fa <?= htmlspecialchars($res['icon'] ?? 'fa-database') ?> fa-2x"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: var(--text-color);"><?= htmlspecialchars($res['title'] ?? '') ?></h4>
                    <p class="text-muted mb-4 fs-6"><?= htmlspecialchars($res['description'] ?? '') ?></p>
                    <a href="<?= htmlspecialchars($res['link_url'] ?? '#') ?>" class="btn resource-btn mt-auto rounded-pill px-4 py-2 fw-bold">
                        <?= htmlspecialchars($res['link_text'] ?? 'Learn More') ?> <i class="fa fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
        </div>
    </div>
</section>
-->

<!-- Lab Gallery Section (Mock) -->
<section class="bio-section pt-4 pb-5" style="background: var(--bg-alt);">
    <div class="container">
        <h3 class="section-title text-center mb-4">Life at the Lab</h3>
        <div class="row g-4">
            <?php foreach($gallery as $img): ?>
            <div class="col-md-4">
                <img src="<?= htmlspecialchars($img['image'] ?? '') ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($img['alt'] ?? '') ?>" style="object-fit: cover; width: 100%; height: 220px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Particle Network & Stat Counter Scripts -->
<script>
// Particle Network Animation
(function() {
    const canvas = document.getElementById('particleCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let particles = [];
    let animFrame;
    const PARTICLE_COUNT = 60;
    const CONNECT_DIST = 120;
    const MOUSE_RADIUS = 150;
    let mouse = { x: null, y: null };

    function resize() {
        const section = canvas.parentElement;
        canvas.width = section.offsetWidth;
        canvas.height = section.offsetHeight;
    }

    function createParticles() {
        particles = [];
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.6,
                vy: (Math.random() - 0.5) * 0.6,
                r: Math.random() * 2 + 1
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const isDark = document.body.classList.contains('dark-mode');
        const dotColor = isDark ? 'rgba(6, 182, 212, 0.7)' : 'rgba(8, 145, 178, 0.5)';
        const lineColor = isDark ? 'rgba(6, 182, 212, %%ALPHA%%)' : 'rgba(8, 145, 178, %%ALPHA%%)';

        for (let i = 0; i < particles.length; i++) {
            let p = particles[i];
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

            // Draw dot
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = dotColor;
            ctx.fill();

            // Connect to nearby particles
            for (let j = i + 1; j < particles.length; j++) {
                let p2 = particles[j];
                let dx = p.x - p2.x;
                let dy = p.y - p2.y;
                let dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECT_DIST) {
                    let alpha = (1 - dist / CONNECT_DIST) * 0.25;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = lineColor.replace('%%ALPHA%%', alpha.toFixed(3));
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }

            // Mouse interaction
            if (mouse.x !== null) {
                let dx = p.x - mouse.x;
                let dy = p.y - mouse.y;
                let dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MOUSE_RADIUS) {
                    let alpha = (1 - dist / MOUSE_RADIUS) * 0.4;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.strokeStyle = lineColor.replace('%%ALPHA%%', alpha.toFixed(3));
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }
        animFrame = requestAnimationFrame(draw);
    }

    canvas.parentElement.addEventListener('mousemove', (e) => {
        const rect = canvas.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
    });
    canvas.parentElement.addEventListener('mouseleave', () => {
        mouse.x = null;
        mouse.y = null;
    });

    window.addEventListener('resize', () => { resize(); createParticles(); });
    resize();
    createParticles();
    draw();
})();

// Animated Stat Counters
(function() {
    const counters = document.querySelectorAll('.lab-stat-number');
    if (!counters.length) return;
    let animated = false;

    function animateCounters() {
        if (animated) return;
        animated = true;
        counters.forEach(el => {
            const target = parseInt(el.dataset.target, 10);
            if (isNaN(target) || target === 0) return;
            const duration = 1800;
            const startTime = performance.now();
            function step(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Ease-out cubic
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(eased * target);
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    }

    // Trigger on scroll into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) animateCounters();
        });
    }, { threshold: 0.3 });

    const statsRow = document.querySelector('.lab-stats-row');
    if (statsRow) observer.observe(statsRow);
})();
</script>

<?php include 'components/footer.php'; ?>
