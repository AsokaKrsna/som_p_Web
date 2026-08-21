<?php
/**
 * Developed by Durjoy Majumdar
 * LinkedIn: https://www.linkedin.com/in/durjoy-majumdar/
 */
$page_title = "Cybersecurity Lab | Dr. Somanath Tripathy";
$sideNavItems = [
    ['href' => '#lab-hero', 'section' => 'lab-hero', 'text' => 'Lab Home', 'tip' => 'Top of page'],
    ['href' => '#about-lab', 'section' => 'about-lab', 'text' => 'About the Lab', 'tip' => 'Mission & Vision'],
    ['href' => '#research-group', 'section' => 'research-group', 'text' => 'Research Group', 'tip' => 'Team & Scholars'],
    ['href' => '#research-outcome', 'section' => 'research-outcome', 'text' => 'Outcome', 'tip' => 'Projects & Pubs'],
    ['href' => '#awards-honours', 'section' => 'awards-honours', 'text' => 'Awards & Honours', 'tip' => 'Awards and Honours'],
    ['href' => '#glimpses', 'section' => 'glimpses', 'text' => 'Glimpses', 'tip' => 'Moments & Milestones'],
    ['href' => '#join-us', 'section' => 'join-us', 'text' => 'Join Us', 'tip' => 'Contact & Openings']
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
$awardsHonours = loadJsonData('awards_honours.json');

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
    // Generate high-resolution dark styled initials avatar
    $nameEncoded = urlencode($name);
    return "https://ui-avatars.com/api/?name={$nameEncoded}&background=0f172a&color=22d3ee&size=500&font-size=0.35&bold=true";
}

// Helper function to render an elevated academic student card (Ph.D. & M.Tech)
function renderStudentCard($member, $index, $roleTitle = 'Scholar') {
    $name = $member['name'] ?? '';
    $fallbackImage = getInitialsAvatarUrl($name);
    $imageSrc = !empty($member['image']) ? htmlspecialchars($member['image']) : $fallbackImage;
    $email = $member['email'] ?? '';
    $linkedin = $member['linkedin'] ?? '';
    $researchArea = $member['research_area'] ?? '';
    $subtitle = !empty($member['subtitle']) ? $member['subtitle'] : '';
    $isHidden = ($index > 6);
    ?>
    <div class="col-md-6 col-lg-4 mb-4 academic-card-col <?= $isHidden ? 'd-none' : '' ?>">
        <div class="academic-student-card h-100">
            <span class="academic-card-idx">#<?= sprintf('%02d', $index) ?></span>
            
            <div class="academic-avatar-wrap">
                <img src="<?= $imageSrc ?>" class="academic-avatar-img" alt="<?= htmlspecialchars($name) ?>" onerror="this.onerror=null; this.src='<?= $fallbackImage ?>';">
            </div>

            <h5 class="academic-scholar-name"><?= htmlspecialchars($name) ?></h5>
            
            <?php if (!empty($subtitle)): ?>
                <span class="academic-subtitle-note"><?= htmlspecialchars($subtitle) ?></span>
            <?php endif; ?>

            <?php if (!empty($email)): ?>
                <a href="mailto:<?= htmlspecialchars($email) ?>" class="academic-email-text" title="Send Email">
                    <i class="fa fa-envelope-o me-1"></i> <?= htmlspecialchars($email) ?>
                </a>
            <?php else: ?>
                <div class="mb-2"></div>
            <?php endif; ?>

            <?php if (!empty($researchArea)): ?>
                <div class="academic-topic-pill">
                    <i class="fa fa-shield"></i> <?= htmlspecialchars($researchArea) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($linkedin)): ?>
                <div class="academic-action-row mt-auto pt-2">
                    <a href="<?= htmlspecialchars($linkedin) ?>" target="_blank" rel="noopener noreferrer" class="academic-linkedin-pill" title="LinkedIn Profile">
                        <i class="fa fa-linkedin me-1"></i> LinkedIn
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// Helper function to render an elevated Past Ph.D. Alumni card
function renderPastPhdCard($member, $index) {
    $name = $member['name'] ?? '';
    $fallbackImage = getInitialsAvatarUrl($name);
    $imageSrc = !empty($member['image']) ? htmlspecialchars($member['image']) : $fallbackImage;
    $email = $member['email'] ?? '';
    $linkedin = $member['linkedin'] ?? '';
    $passingYear = $member['passing_year'] ?? '';
    $currentAffiliation = $member['current_affiliation'] ?? '';
    $thesis = $member['thesis'] ?? '';
    $subtitle = $member['subtitle'] ?? '';
    $isHidden = ($index > 6);
    ?>
    <div class="col-md-6 col-lg-4 mb-4 academic-card-col <?= $isHidden ? 'd-none' : '' ?>">
        <div class="academic-phd-alumni-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="<?= $imageSrc ?>" class="academic-avatar-img" style="width: 72px; height: 72px; flex-shrink: 0;" alt="<?= htmlspecialchars($name) ?>" onerror="this.onerror=null; this.src='<?= $fallbackImage ?>';">
                <div>
                    <h5 class="academic-scholar-name mb-1" style="font-size: 1.1rem;"><?= htmlspecialchars($name) ?></h5>
                    <?php if ($passingYear): ?>
                        <span class="badge" style="background: rgba(8, 145, 178, 0.12); color: var(--accent-blue); font-size: 0.78rem; font-weight: 600; border-radius: 12px;"><i class="fa fa-graduation-cap me-1"></i> Class of <?= htmlspecialchars($passingYear) ?></span>
                    <?php endif; ?>
                    <?php if ($subtitle): ?>
                        <div class="text-muted small mt-1" style="font-size: 0.78rem;"><?= htmlspecialchars($subtitle) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($email)): ?>
                <a href="mailto:<?= htmlspecialchars($email) ?>" class="academic-email-text text-start mb-2" title="Send Email">
                    <i class="fa fa-envelope-o me-1"></i> <?= htmlspecialchars($email) ?>
                </a>
            <?php endif; ?>

            <?php if ($currentAffiliation): ?>
                <div class="mb-2" style="font-size: 0.88rem; color: var(--text-main); font-weight: 500;">
                    <i class="fa fa-university me-1 text-cyan"></i> <?= htmlspecialchars($currentAffiliation) ?>
                </div>
            <?php endif; ?>

            <?php if ($thesis): ?>
                <div class="alumni-thesis-quote">
                    <strong><i class="fa fa-book me-1"></i> Thesis:</strong> <?= htmlspecialchars($thesis) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($linkedin)): ?>
                <div class="academic-action-row mt-auto pt-2 justify-content-start">
                    <a href="<?= htmlspecialchars($linkedin) ?>" target="_blank" rel="noopener noreferrer" class="academic-linkedin-pill" title="LinkedIn Profile">
                        <i class="fa fa-linkedin me-1"></i> LinkedIn
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// Helper function to render a slim, sleek Past M.Tech Alumni row
function renderPastMtechCard($member, $index) {
    $name = $member['name'] ?? '';
    $fallbackImage = getInitialsAvatarUrl($name);
    $imageSrc = !empty($member['image']) ? htmlspecialchars($member['image']) : $fallbackImage;
    $passingYear = $member['passing_year'] ?? '';
    $thesis = $member['thesis'] ?? '';
    $currentAffiliation = $member['current_affiliation'] ?? '';
    $isHidden = ($index > 6);
    ?>
    <div class="col-md-6 mb-3 academic-card-col <?= $isHidden ? 'd-none' : '' ?>">
        <div class="academic-mtech-alumni-row h-100">
            <span style="font-weight: 800; color: var(--accent-blue); font-size: 0.95rem; min-width: 28px;">#<?= sprintf('%02d', $index) ?></span>
            <img src="<?= $imageSrc ?>" class="academic-avatar-img" style="width: 52px; height: 52px; flex-shrink: 0;" alt="<?= htmlspecialchars($name) ?>" onerror="this.onerror=null; this.src='<?= $fallbackImage ?>';">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <h6 class="mb-0" style="font-weight: 700; color: var(--text-main); font-size: 0.98rem;"><?= htmlspecialchars($name) ?></h6>
                    <?php if ($passingYear): ?>
                        <span class="badge" style="background: rgba(8, 145, 178, 0.1); color: var(--accent-blue); font-size: 0.75rem;"><i class="fa fa-calendar-check-o me-1"></i> <?= htmlspecialchars($passingYear) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($currentAffiliation): ?>
                    <div class="text-muted small mt-1" style="font-size: 0.82rem;"><i class="fa fa-building-o me-1"></i> <?= htmlspecialchars($currentAffiliation) ?></div>
                <?php endif; ?>
                <?php if ($thesis): ?>
                    <div class="text-muted small mt-1" style="font-size: 0.82rem; line-height: 1.35;"><strong class="text-cyan">Thesis:</strong> <?= htmlspecialchars($thesis) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

function renderPublicationTable($items) {
    $counter = count($items);
    $idx = 0;
    foreach ($items as $pub): 
        $display = ($idx < 5) ? '' : 'style="display: none;"';
        $idx++;
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
    <tr class="pub-row" <?= $display ?>><td>
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

    <div class="lab-hero-watermark">
        <img src="images/lab_logo.png" alt="Lab Logo Watermark">
    </div>

    <div class="container lab-hero-content">
        <div class="row align-items-center align-items-md-start justify-content-center justify-content-md-start">
            <div class="col-12 col-md-auto text-center mb-4 mb-md-0 pe-md-4">
                <img src="images/lab_logo.png" alt="Lab Logo" class="lab-logo-img" style="max-width: 120px; margin-top: 8px; border-radius: 50%; box-shadow: 0 0 25px rgba(8, 145, 178, 0.25);">
            </div>
            <div class="col-12 col-md text-center text-md-start">
                <h1 class="lab-hero-title mb-1" style="font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; letter-spacing: -1px;"><?= htmlspecialchars($heroContent['title'] ?? 'Cybersecurity Lab') ?></h1>
                <h2 class="lab-hero-subtitle mb-4" style="font-weight: 600; font-size: 1.4rem; letter-spacing: 1.5px; color: var(--text-muted); text-transform: uppercase;"><?= htmlspecialchars($heroContent['subtitle'] ?? '') ?></h2>
                
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-start gap-3">
                    <?php if (!empty($heroContent['location'])): ?>
                        <span class="lab-hero-location d-inline-flex align-items-center" style="background: rgba(8, 145, 178, 0.1); padding: 8px 20px; border-radius: 30px; color: var(--accent-blue); font-weight: 600; font-size: 0.95rem;">
                            <i class="fa fa-map-marker me-2"></i><?= htmlspecialchars($heroContent['location']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
        
        <?php if (!empty($heroContent['tagline'])): ?>
        <div class="text-center mt-2 mb-4">
            <p class="lab-hero-tagline text-muted mx-auto mb-0" style="font-size: 1.15rem; max-width: 800px;"><?= htmlspecialchars($heroContent['tagline']) ?></p>
        </div>
        <?php endif; ?>

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
            if (empty($a['show_in_snackbar'])) continue; // Only show if checkbox is checked
            $full_text = (!empty($a['title']) ? "<strong>".htmlspecialchars($a['title'])."</strong>: " : "") . htmlspecialchars($a['text']);
            if (!empty($a['link'])) {
                $full_text = "<a href='".htmlspecialchars($a['link'])."' target='_blank'>{$full_text}</a>";
            }
            if (!empty($a['badge'])) {
                $badge_class = !empty($a['badge_color']) ? htmlspecialchars($a['badge_color']) : 'bg-primary';
                $full_text = "<span class='badge {$badge_class}'>".htmlspecialchars($a['badge'])."</span> " . $full_text;
            }
            $texts[] = $full_text;
        }
        if (empty($texts)) {
            $texts[] = "Welcome to the Cybersecurity Lab";
        }
        echo implode(" &nbsp;&nbsp;|&nbsp;&nbsp; ", $texts);
        ?>
    </div>
</div>
<?php endif; ?>

<!-- About the Lab & Join Us -->
<section id="about-lab" class="bio-section pt-5 pb-5">
    <div class="container">
        <!-- Row 1: About and News -->
        <div class="row g-5 align-items-stretch mb-5">
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
            <div class="col-lg-5">
                <!-- News & Highlights -->
                <div class="h-100 d-flex flex-column">
                    <h3 class="section-title text-start mb-4">News & Highlights</h3>
                    <div class="news-ticker-container flex-grow-1" style="background: var(--glass-bg); border: var(--glass-border); border-radius: 15px; padding: 20px; box-shadow: var(--glass-shadow); overflow-y: auto; overflow-x: hidden;">
                        <ul class="list-unstyled mb-0">
                            <?php foreach($announcements as $a): ?>
                            <li class="mb-3 border-bottom pb-2">
                                <?php if (!empty($a['badge'])): ?>
                                    <span class="badge <?= htmlspecialchars($a['badge_color'] ?? 'bg-primary') ?> me-2"><?= htmlspecialchars($a['badge']) ?></span> 
                                <?php endif; ?>
                                <?php if (!empty($a['title'])): ?>
                                    <strong><?= htmlspecialchars($a['title']) ?>:</strong> 
                                <?php endif; ?>
                                <?= htmlspecialchars($a['text']) ?> 
                                <?php if (!empty($a['link'])): ?>
                                    <a href="<?= htmlspecialchars($a['link']) ?>" target="_blank" class="ms-1"><i class="fa fa-external-link" style="font-size: 0.8rem;"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($a['date'])): ?>
                                    <span class="text-muted small ms-2"><?= htmlspecialchars($a['date']) ?></span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                            <?php if(empty($announcements)): ?>
                                <li class="text-muted text-center py-3">No recent news.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- Research Group (Unified Editorial Portrait Wall) -->
<?php
$combinedTeam = [];

// PhD Scholars
if (!empty($members['phd'])) {
    foreach ($members['phd'] as $m) {
        $combinedTeam[] = [
            'name' => $m['name'] ?? '',
            'image' => $m['image'] ?? '',
            'category' => 'phd',
            'role_display' => 'Ph.D. Scholar',
            'desc_display' => !empty($m['research_area']) ? $m['research_area'] : 'Cyber Security',
            'email' => $m['email'] ?? '',
            'linkedin' => $m['linkedin'] ?? '',
            'subtitle' => $m['subtitle'] ?? ''
        ];
    }
}

// M.Tech Scholars
if (!empty($members['mtech'])) {
    foreach ($members['mtech'] as $m) {
        $role = !empty($m['subtitle']) ? $m['subtitle'] : 'M.Tech Scholar';
        $combinedTeam[] = [
            'name' => $m['name'] ?? '',
            'image' => $m['image'] ?? '',
            'category' => 'mtech',
            'role_display' => $role,
            'desc_display' => !empty($m['research_area']) ? $m['research_area'] : 'Cyber Security',
            'email' => $m['email'] ?? '',
            'linkedin' => $m['linkedin'] ?? '',
            'subtitle' => $m['subtitle'] ?? ''
        ];
    }
}

// Past PhD (Alumni)
if (!empty($members['past_phd'])) {
    foreach ($members['past_phd'] as $m) {
        $yearTag = !empty($m['passing_year']) ? 'Ph.D. Alumni · ' . $m['passing_year'] : 'Ph.D. Alumni';
        $desc = !empty($m['current_affiliation']) ? $m['current_affiliation'] : (!empty($m['thesis']) ? 'Thesis: ' . $m['thesis'] : 'IIT Patna Alumnus');
        $combinedTeam[] = [
            'name' => $m['name'] ?? '',
            'image' => $m['image'] ?? '',
            'category' => 'alumni',
            'role_display' => $yearTag,
            'desc_display' => $desc,
            'email' => $m['email'] ?? '',
            'linkedin' => $m['linkedin'] ?? '',
            'subtitle' => $m['thesis'] ?? ''
        ];
    }
}

// Past M.Tech (Alumni)
if (!empty($members['past_mtech'])) {
    foreach ($members['past_mtech'] as $m) {
        $yearTag = !empty($m['passing_year']) ? 'M.Tech Alumni · ' . $m['passing_year'] : 'M.Tech Alumni';
        $desc = !empty($m['thesis']) ? 'Thesis: ' . $m['thesis'] : (!empty($m['current_affiliation']) ? $m['current_affiliation'] : 'IIT Patna Alumnus');
        $combinedTeam[] = [
            'name' => $m['name'] ?? '',
            'image' => $m['image'] ?? '',
            'category' => 'alumni',
            'role_display' => $yearTag,
            'desc_display' => $desc,
            'email' => $m['email'] ?? '',
            'linkedin' => $m['linkedin'] ?? '',
            'subtitle' => $m['thesis'] ?? ''
        ];
    }
}

$phdCount = count($members['phd'] ?? []);
$mtechCount = count($members['mtech'] ?? []);
$alumniCount = count($members['past_phd'] ?? []) + count($members['past_mtech'] ?? []);
$totalCount = count($combinedTeam);
?>

<section id="research-group" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Research Group</h2>
        
        <!-- Professor / Head of Group -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="student-card professor-card p-4 p-md-5 d-flex flex-column flex-md-row align-items-center align-items-md-start text-center text-md-start gap-4">
                    <img class="professor-avatar-rect mb-0" src="pics/som-pic.png" alt="<?= htmlspecialchars($headContent['name'] ?? '') ?>" style="width: 140px; height: 160px; min-width: 140px; max-width: 140px; object-fit: cover; object-position: center 15%; border-radius: 20px; border: 4px solid rgba(8, 145, 178, 0.3); box-shadow: 0 10px 25px rgba(8, 145, 178, 0.25); flex-shrink: 0;">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start mb-2">
                            <div>
                                <h3 class="mb-1" style="font-weight: 800; color: var(--text-main); letter-spacing: -0.5px;"><?= htmlspecialchars($headContent['name'] ?? '') ?></h3>
                                <span class="badge px-3 py-1 mb-2" style="font-size: 0.95rem; font-weight: 600; background: rgba(8, 145, 178, 0.12); color: var(--accent-blue) !important; border-radius: 20px;"><?= htmlspecialchars($headContent['designation'] ?? 'Professor') ?></span>
                            </div>
                            <div class="d-flex gap-2 mt-2 mt-md-0">
                                <?php if (!empty($headContent['scholar_link'])): ?>
                                <a href="<?= htmlspecialchars($headContent['scholar_link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fa fa-graduation-cap me-1"></i> Google Scholar</a>
                                <?php endif; ?>
                                <?php if (!empty($headContent['website_link'])): ?>
                                <a href="<?= htmlspecialchars($headContent['website_link']) ?>" class="btn btn-sm btn-primary rounded-pill px-3" style="background: var(--accent-blue); border-color: var(--accent-blue);"><i class="fa fa-globe me-1"></i> Personal Website</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="mb-0 mt-3" style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.7;"><?= htmlspecialchars($headContent['bio'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Group Navigation Tabs -->
        <ul class="nav nav-pills custom-academic-tabs justify-content-center gap-2 mb-4" id="researchGroupTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="phd-tab" data-bs-toggle="pill" data-bs-target="#phd-pane" type="button" role="tab" aria-controls="phd-pane" aria-selected="true">
                    <i class="fa fa-mortar-board me-1"></i> Ph.D. Scholars <span class="tab-count"><?= count($members['phd'] ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mtech-tab" data-bs-toggle="pill" data-bs-target="#mtech-pane" type="button" role="tab" aria-controls="mtech-pane" aria-selected="false">
                    <i class="fa fa-users me-1"></i> M.Tech Scholars <span class="tab-count"><?= count($members['mtech'] ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-phd-tab" data-bs-toggle="pill" data-bs-target="#past-phd-pane" type="button" role="tab" aria-controls="past-phd-pane" aria-selected="false">
                    <i class="fa fa-certificate me-1"></i> Ph.D. Alumni <span class="tab-count"><?= count($members['past_phd'] ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-mtech-tab" data-bs-toggle="pill" data-bs-target="#past-mtech-pane" type="button" role="tab" aria-controls="past-mtech-pane" aria-selected="false">
                    <i class="fa fa-history me-1"></i> M.Tech Alumni <span class="tab-count"><?= count($members['past_mtech'] ?? []) ?></span>
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content" id="researchGroupTabContent">
            <!-- Ph.D. Scholars -->
            <div class="tab-pane fade show active" id="phd-pane" role="tabpanel" aria-labelledby="phd-tab">
                <div class="row">
                    <?php 
                    $idx = 1;
                    foreach ($members['phd'] as $m) {
                        renderStudentCard($m, $idx++, 'Ph.D. Scholar');
                    }
                    ?>
                </div>
                <?php if (count($members['phd'] ?? []) > 6): ?>
                <div class="text-center mt-3 mb-2 see-more-wrap">
                    <button class="btn academic-see-more-btn" onclick="revealNextBatch(this)">
                        <span>See More</span>
                        <i class="fa fa-angle-down ms-2"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- M.Tech Scholars -->
            <div class="tab-pane fade" id="mtech-pane" role="tabpanel" aria-labelledby="mtech-tab">
                <div class="row">
                    <?php 
                    $idx = 1;
                    foreach ($members['mtech'] as $m) {
                        $role = !empty($m['subtitle']) ? $m['subtitle'] : 'M.Tech Scholar';
                        renderStudentCard($m, $idx++, $role);
                    }
                    ?>
                </div>
                <?php if (count($members['mtech'] ?? []) > 6): ?>
                <div class="text-center mt-3 mb-2 see-more-wrap">
                    <button class="btn academic-see-more-btn" onclick="revealNextBatch(this)">
                        <span>See More</span>
                        <i class="fa fa-angle-down ms-2"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Ph.D. Alumni -->
            <div class="tab-pane fade" id="past-phd-pane" role="tabpanel" aria-labelledby="past-phd-tab">
                <div class="row">
                    <?php 
                    $idx = 1;
                    foreach ($members['past_phd'] as $m) {
                        renderPastPhdCard($m, $idx++);
                    }
                    ?>
                </div>
                <?php if (count($members['past_phd'] ?? []) > 6): ?>
                <div class="text-center mt-3 mb-2 see-more-wrap">
                    <button class="btn academic-see-more-btn" onclick="revealNextBatch(this)">
                        <span>See More</span>
                        <i class="fa fa-angle-down ms-2"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- M.Tech Alumni -->
            <div class="tab-pane fade" id="past-mtech-pane" role="tabpanel" aria-labelledby="past-mtech-tab">
                <div class="row">
                    <?php 
                    $idx = 1;
                    foreach ($members['past_mtech'] as $m) {
                        renderPastMtechCard($m, $idx++);
                    }
                    ?>
                </div>
                <?php if (count($members['past_mtech'] ?? []) > 6): ?>
                <div class="text-center mt-3 mb-2 see-more-wrap">
                    <button class="btn academic-see-more-btn" onclick="revealNextBatch(this)">
                        <span>See More</span>
                        <i class="fa fa-angle-down ms-2"></i>
                    </button>
                </div>
                <?php endif; ?>
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
                                    <thead><tr><th>Title</th><th style="min-width: 150px;">Role</th><th>Funding Agency</th><th>Amount</th><th>Duration</th></tr></thead>
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
                                    <thead><tr><th>Title</th><th style="min-width: 150px;">Role</th><th>Funding Agency</th><th>Amount</th><th>Duration</th></tr></thead>
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

                        <div class="text-center mt-4">
                            <button id="seeMoreLabPubsBtn" class="btn btn-outline-primary rounded-pill px-4" onclick="showMoreLabPubs()">See More <i class="fa fa-chevron-down ms-1"></i></button>
                        </div>
                        <script>
                        function showMoreLabPubs() {
                            const activeTabPane = document.querySelector('#pubs-tab-pane .tab-content .tab-pane.active');
                            if (!activeTabPane) return;
                            const hiddenRows = activeTabPane.querySelectorAll('.pub-row[style*="display: none"]');
                            for (let i = 0; i < 10 && i < hiddenRows.length; i++) {
                                hiddenRows[i].style.display = '';
                            }
                            const remaining = activeTabPane.querySelectorAll('.pub-row[style*="display: none"]');
                            if (remaining.length === 0) {
                                document.getElementById('seeMoreLabPubsBtn').style.display = 'none';
                            }
                        }
                        function updateLabSeeMoreBtn() {
                            const activeTabPane = document.querySelector('#pubs-tab-pane .tab-content .tab-pane.active');
                            const btn = document.getElementById('seeMoreLabPubsBtn');
                            if (!btn) return;
                            if (activeTabPane) {
                                const hiddenRows = activeTabPane.querySelectorAll('.pub-row[style*="display: none"]');
                                btn.style.display = (hiddenRows.length === 0) ? 'none' : 'inline-block';
                            } else {
                                btn.style.display = 'none';
                            }
                        }
                        document.addEventListener('DOMContentLoaded', () => {
                            updateLabSeeMoreBtn();

                            const pubSubTabs = document.querySelectorAll('#pubSubTabs [data-bs-toggle="tab"]');
                            pubSubTabs.forEach(tab => {
                                tab.addEventListener('shown.bs.tab', function () {
                                    updateLabSeeMoreBtn();
                                });
                            });

                            const mainResearchTabs = document.querySelectorAll('#researchOutcomeTab [data-bs-toggle="tab"]');
                            mainResearchTabs.forEach(tab => {
                                tab.addEventListener('shown.bs.tab', function (e) {
                                    if (e.target.getAttribute('data-bs-target') === '#pubs-tab-pane') {
                                        updateLabSeeMoreBtn();
                                    }
                                });
                            });
                        });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Awards and Honours Section -->
<?php if (!empty($awardsHonours)): ?>
<section id="awards-honours" class="bio-section py-4">
    <div class="container">
        <h2 class="section-title">Awards and Honours</h2>
        <div class="row g-4 mt-2">
            <?php foreach ($awardsHonours as $award): ?>
            <div class="col-12">
                <div class="admin-card h-100 shadow-sm p-4" style="border-left: 4px solid var(--accent-blue);">
                    <h5 class="fw-bold mb-2"><?= htmlspecialchars($award['title'] ?? '') ?></h5>
                    <?php if (!empty($award['awardee'])): ?>
                        <p class="text-primary fw-semibold mb-2"><i class="fa fa-user me-2"></i><?= htmlspecialchars($award['awardee']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($award['event'])): ?>
                        <p class="text-muted small mb-2"><i class="fa fa-calendar me-2"></i><?= htmlspecialchars($award['event']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($award['organization'])): ?>
                        <p class="mb-1"><i class="fa fa-building-o me-2"></i><?= htmlspecialchars($award['organization']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($award['location'])): ?>
                        <p class="text-muted small mb-2"><i class="fa fa-map-marker me-2"></i><?= htmlspecialchars($award['location']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($award['link'])): ?>
                    <a href="<?= htmlspecialchars($award['link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Read More <i class="fa fa-external-link ms-1"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Glimpses: Scroll-Driven Horizontal Showcase Gallery -->
<?php if (!empty($gallery)): ?>
<?php $slideCount = count($gallery); ?>
<section id="glimpses" class="glimpses-section" style="height: <?= ($slideCount * 75) ?>vh;">
    <div class="glimpses-sticky">
        <!-- Top Centered Header inside Container -->
        <div class="container mb-3 text-center glimpses-header-container">
            <h2 class="section-title mb-1">Glimpses</h2>
            <p class="text-muted mb-2" style="font-size: 1rem; max-width: 600px; margin-left: auto; margin-right: auto;">Moments &amp; milestones from the Cybersecurity Lab.</p>
            
            <div class="d-inline-flex align-items-center gap-3 px-3 py-1 mt-1 rounded-pill glimpses-indicator-pill">
                <div class="glimpses-counter">
                    <span class="glimpses-counter-current" id="glimpsesCounterCurrent">01</span>
                    <span class="glimpses-counter-sep">/</span>
                    <span class="glimpses-counter-total"><?= str_pad($slideCount, 2, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="glimpses-progress-track">
                    <div class="glimpses-progress-fill" id="glimpsesProgressFill"></div>
                </div>
            </div>
        </div>

        <!-- Horizontal Photo Viewport -->
        <div class="glimpses-viewport">
            <div class="glimpses-track" id="glimpsesTrack">
                <?php foreach ($gallery as $index => $img):
                    $title = !empty($img['alt']) ? $img['alt'] : 'Lab Moment';
                    $date  = !empty($img['date']) ? $img['date'] : '';
                    $desc  = !empty($img['description']) ? $img['description'] : '';
                ?>
                <div class="glimpse-slide" data-index="<?= $index ?>" data-src="<?= htmlspecialchars($img['image'] ?? '') ?>" data-title="<?= htmlspecialchars($title) ?>" data-date="<?= htmlspecialchars($date) ?>">
                    <img src="<?= htmlspecialchars($img['image'] ?? '') ?>" alt="<?= htmlspecialchars($title) ?>" class="glimpse-slide-img" loading="lazy">
                    <div class="glimpse-slide-overlay"></div>
                    <div class="glimpse-slide-caption">
                        <?php if ($date): ?>
                            <span class="glimpse-slide-date"><i class="fa fa-calendar-o me-1"></i><?= htmlspecialchars($date) ?></span>
                        <?php endif; ?>
                        <h4 class="glimpse-slide-title"><?= htmlspecialchars($title) ?></h4>
                        <?php if ($desc): ?>
                            <p class="glimpse-slide-desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Compact Scroll Hint -->
        <div class="glimpses-scroll-hint" id="glimpsesScrollHint">
            <i class="fa fa-arrows-h me-1"></i> Scroll down to explore gallery
        </div>
    </div>
</section>

<!-- Fullscreen Lightbox -->
<div class="glimpse-lightbox" id="glimpseLightbox" aria-hidden="true">
    <button class="glimpse-lightbox-close" id="glimpseLightboxClose" aria-label="Close"><i class="fa fa-times"></i></button>
    <button class="glimpse-lightbox-nav glimpse-lightbox-prev" id="glimpseLightboxPrev" aria-label="Previous"><i class="fa fa-chevron-left"></i></button>
    <button class="glimpse-lightbox-nav glimpse-lightbox-next" id="glimpseLightboxNext" aria-label="Next"><i class="fa fa-chevron-right"></i></button>
    <div class="glimpse-lightbox-content">
        <img src="" alt="" class="glimpse-lightbox-img" id="glimpseLightboxImg">
        <div class="glimpse-lightbox-caption" id="glimpseLightboxCaption"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const section    = document.querySelector('.glimpses-section');
    const track      = document.getElementById('glimpsesTrack');
    const slides     = Array.from(document.querySelectorAll('.glimpse-slide'));
    const counterEl  = document.getElementById('glimpsesCounterCurrent');
    const progressEl = document.getElementById('glimpsesProgressFill');
    const scrollHint = document.getElementById('glimpsesScrollHint');

    if (!section || !track || slides.length === 0) return;

    let ticking = false;

    function onScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            if (window.innerWidth <= 768) {
                track.style.transform = 'none';
                ticking = false;
                return;
            }

            const rect = section.getBoundingClientRect();
            const scrollableH = section.offsetHeight - window.innerHeight;
            const scrolled = -rect.top;
            const progress = Math.max(0, Math.min(1, scrolled / scrollableH));

            const trackW = track.scrollWidth;
            const maxTranslate = Math.max(0, trackW - window.innerWidth + 80);
            track.style.transform = `translate3d(${-progress * maxTranslate}px, 0, 0)`;

            if (progressEl) progressEl.style.width = `${progress * 100}%`;

            const activeIdx = Math.min(Math.floor(progress * slides.length), slides.length - 1);
            if (counterEl) counterEl.textContent = String(activeIdx + 1).padStart(2, '0');

            if (scrollHint) scrollHint.style.opacity = progress > 0.05 ? '0' : '0.85';

            ticking = false;
        });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    onScroll();

    // Lightbox
    const lightbox  = document.getElementById('glimpseLightbox');
    const lbImg     = document.getElementById('glimpseLightboxImg');
    const lbCaption = document.getElementById('glimpseLightboxCaption');
    const lbClose   = document.getElementById('glimpseLightboxClose');
    const lbPrev    = document.getElementById('glimpseLightboxPrev');
    const lbNext    = document.getElementById('glimpseLightboxNext');
    let lbIdx = 0;

    slides.forEach((slide, idx) => {
        slide.addEventListener('click', () => openLb(idx));
    });

    function openLb(idx) {
        lbIdx = idx;
        const s = slides[lbIdx];
        if (!s) return;
        lbImg.src = s.getAttribute('data-src');
        const t = s.getAttribute('data-title');
        const d = s.getAttribute('data-date');
        lbCaption.innerHTML = `<span>${t}</span>${d ? ` <span class="text-white-50 small">&bull; ${d}</span>` : ''}`;
        lightbox.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeLb() {
        lightbox.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (lbClose) lbClose.addEventListener('click', closeLb);
    if (lbNext) lbNext.addEventListener('click', () => { lbIdx = (lbIdx + 1) % slides.length; openLb(lbIdx); });
    if (lbPrev) lbPrev.addEventListener('click', () => { lbIdx = (lbIdx - 1 + slides.length) % slides.length; openLb(lbIdx); });
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLb(); });
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('show')) return;
        if (e.key === 'Escape') closeLb();
        if (e.key === 'ArrowRight') { lbIdx = (lbIdx + 1) % slides.length; openLb(lbIdx); }
        if (e.key === 'ArrowLeft') { lbIdx = (lbIdx - 1 + slides.length) % slides.length; openLb(lbIdx); }
    });
});
</script>
<?php endif; ?>

<!-- Join Our Group -->
<section id="join-us" class="bio-section py-5 mt-4" style="background: var(--bg-alt); position: relative; overflow: hidden;">
    <div class="container" style="position: relative; z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="student-card join-us-card p-4 p-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4 text-center text-md-start">
                    <div class="pe-md-4">
                        <h3 class="mb-2" style="font-weight: 800; color: var(--text-main); letter-spacing: -0.5px;"><?= htmlspecialchars($joinUsContent['heading'] ?? 'Join Our Group') ?></h3>
                        <p class="mb-0" style="font-size: 1.05rem; line-height: 1.6; color: var(--text-muted);">
                            <?= htmlspecialchars($joinUsContent['description'] ?? 'We are always looking for motivated Ph.D., M.Tech, and B.Tech students passionate about cybersecurity research.') ?>
                        </p>
                        <?php if (!empty($joinUsContent['note'])): ?>
                            <div class="mt-2 text-muted small" style="font-style: italic; opacity: 0.85;">
                                <i class="fa fa-info-circle me-1" style="color: var(--accent-blue);"></i> <?= htmlspecialchars($joinUsContent['note']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-shrink-0">
                        <?php 
                            $contactEmail = !empty($joinUsContent['email']) ? $joinUsContent['email'] : 'somanath@iitp.ac.in';
                            $mailSubject = rawurlencode("Inquiry Regarding Research / Joining Cybersecurity Lab");
                            $mailBody = rawurlencode("Dear Dr. Tripathy,\n\nI am writing to express my interest in joining the Cybersecurity Lab at IIT Patna.\n\nName: \nCurrent / Last Completed Degree: \nPosition of Interest (Ph.D. / M.Tech / B.Tech / Intern / Postdoc): \nResearch Area / Background: \nLinks to CV / Portfolio / Profile: \n\nLooking forward to hearing from you.\n\nSincerely,\n");
                            $mailtoLink = "mailto:{$contactEmail}?subject={$mailSubject}&body={$mailBody}";
                        ?>
                        <a href="<?= $mailtoLink ?>" class="btn btn-primary rounded-pill px-4 py-3 fw-bold d-inline-flex align-items-center justify-content-center shadow-sm" style="background: var(--accent-blue); border-color: var(--accent-blue); font-size: 1rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                            <i class="fa fa-envelope me-2"></i> Get in Touch
                        </a>
                    </div>
                </div>
            </div>
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

// Progressive Reveal for Academic Cards (6 items per click)
function revealNextBatch(btn) {
    const pane = btn.closest('.tab-pane');
    if (!pane) return;
    const hiddenItems = pane.querySelectorAll('.academic-card-col.d-none');
    const toReveal = Array.from(hiddenItems).slice(0, 6);
    
    toReveal.forEach((item, idx) => {
        item.classList.remove('d-none');
        item.style.opacity = '0';
        item.style.transform = 'translateY(24px) scale(0.96)';
        setTimeout(() => {
            item.style.transition = 'opacity 0.45s cubic-bezier(0.16, 1, 0.3, 1), transform 0.45s cubic-bezier(0.16, 1, 0.3, 1)';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0) scale(1)';
        }, idx * 50);
    });

    if (hiddenItems.length <= 6) {
        const wrap = btn.closest('.see-more-wrap');
        if (wrap) wrap.style.display = 'none';
    }
}
</script>

<?php include 'components/footer.php'; ?>
