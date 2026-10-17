<?php
/**
 * Developed by Durjoy Majumdar
 * LinkedIn: https://www.linkedin.com/in/durjoy-majumdar/
 */

$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

// Safely load JSON data with fallback
function loadJsonData($file, $visibilityKey = null)
{
    $path = 'data/' . $file;
    if (!file_exists($path))
        return [];
    $content = @file_get_contents($path);
    if ($content === false)
        return [];
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
                    $items = array_filter($items, function ($item) use ($visibilityKey) {
                        return !isset($item[$visibilityKey]) || $item[$visibilityKey] === true || $item[$visibilityKey] === "true";
                    });
                    $items = array_values($items); // re-index
                }
            }
        } else {
            $data = array_filter($data, function ($item) use ($visibilityKey) {
                return !isset($item[$visibilityKey]) || $item[$visibilityKey] === true || $item[$visibilityKey] === "true";
            });
            $data = array_values($data); // re-index
        }
    }
    return $data;
}

// Reusable publication table renderer (eliminates 4x duplication)
function renderPublicationTable($items)
{
    $counter = count($items);
    $idx = 0;
    foreach ($items as $pub): 
        $display = ($idx < 5) ? '' : 'style="display: none;"';
        $idx++;
    ?>
        <tr class="pub-row" <?= $display ?>>
            <td>
                <p><strong><?= $counter-- ?>.</strong>
                    <?php if (!empty($pub['author'])): ?>
                        <?= htmlspecialchars($pub['author']) ?>,
                    <?php endif; ?>

                    <?php if (!empty($pub['link'])): ?>
                        <a href="<?= htmlspecialchars($pub['link']) ?>" target="_blank" rel="noopener noreferrer">
                            <strong><?= htmlspecialchars($pub['title'] ?? '') ?></strong>
                        </a>
                    <?php else: ?>
                        <strong><?= htmlspecialchars($pub['title'] ?? '') ?></strong>
                    <?php endif; ?>

                    <?php if (!empty($pub['published_at'])): ?>
                        , <?= htmlspecialchars($pub['published_at']) ?>
                    <?php endif; ?>

                    <?php if (!empty($pub['doi'])): ?>
                        , <a href="<?= htmlspecialchars(str_replace('https://doi.org/', 'https://doi.org/', $pub['doi'])) ?>"
                            target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($pub['doi']) ?></a>
                    <?php endif; ?>

                    <?php if (!empty($pub['impact_factor'])): ?>
                        <br><span class="impact-factor-badge"><?= htmlspecialchars($pub['impact_factor']) ?></span>
                    <?php endif; ?>
                </p>
            </td>
        </tr>
    <?php endforeach;
}

$publications = loadJsonData('publications.json', 'show_personal');
$patents = loadJsonData('patents.json');
$projects = loadJsonData('projects.json', 'show_personal');
$courses = loadJsonData('teaching.json');
$events = loadJsonData('seminars.json');
$memberships = loadJsonData('memberships.json');
$editorships = loadJsonData('editorships.json');
$awards = loadJsonData('awards_honours.json');
$adminResponsibilities = loadJsonData('admin_responsibilities.json');
$otherResponsibilities = loadJsonData('other_responsibilities.json');
$profile = loadJsonData('profile_content.json');
$announcements = loadJsonData('announcements.json');
$snackbarAnnouncements = array_values(array_filter($announcements, function ($a) {
    return !empty($a['show_in_snackbar']) && ($a['show_in_snackbar'] === true || $a['show_in_snackbar'] === 'true' || $a['show_in_snackbar'] === 1 || $a['show_in_snackbar'] === '1');
}));
?>

<!-- Section 1: Hero Section -->
<section id="home" class="hero-section">
    <div class="hero-glow-orb-1"></div>
    <div class="hero-glow-orb-2"></div>

    <div class="container hero-container">
        <div class="hero-content">
            <h1 class="hero-title"><?= htmlspecialchars($profile['hero']['title'] ?? 'Dr. Somanath Tripathy') ?></h1>
            <h2 class="hero-subtitle"><?= htmlspecialchars($profile['hero']['subtitle'] ?? 'Professor') ?></h2>

            <div class="hero-details">
                <div class="hero-contact-item">
                    <i class="fa fa-university"></i>
                    <span><?= htmlspecialchars($profile['hero']['department'] ?? '') ?></span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-map-marker"></i>
                    <span><?= htmlspecialchars($profile['hero']['institute'] ?? '') ?></span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-building"></i>
                    <span><?= htmlspecialchars($profile['hero']['room'] ?? '') ?></span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-envelope"></i>
                    <span><?= htmlspecialchars($profile['hero']['email'] ?? '') ?></span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-phone"></i>
                    <span><?= htmlspecialchars($profile['hero']['phone'] ?? '') ?></span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-flask"></i>
                    <span>Research Interest: <?= htmlspecialchars($profile['hero']['research_interests'] ?? '') ?></span>
                </div>
            </div>

            <div class="mt-4 pt-2">
                <a href="#publications" class="btn btn-custom">Explore Research <i
                        class="fa fa-arrow-down ms-2"></i></a>
            </div>
        </div>

        <div class="hero-visuals">
            <div class="glass-avatar-wrapper">
                <img src="<?= htmlspecialchars($profile['hero']['image'] ?? 'images/som_2.png') ?>" alt="Profile Picture" class="profile-img-3d">
            </div>
        </div>
    </div>
</section>

<!-- Section: Announcements / Snackbar -->
<?php if (!empty($snackbarAnnouncements)): ?>
<section class="profile-announcement-section" aria-label="Announcements & Updates">
    <div class="container">
        <div class="profile-announcement-pill" id="profileAnnouncementPill" role="region" aria-roledescription="carousel" aria-label="Latest announcements">
            <div class="announcement-pill-top">
                <div class="announcement-pill-indicator">
                    <span class="announcement-pill-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 9h4l11-4v14L8 15H4zM8 15l2 5H6l-2-5M8 9v6" stroke-linejoin="round"/></svg></span>
                    <span class="announcement-header-label">Announcements</span>
                </div>
                <?php if (count($snackbarAnnouncements) > 1): ?>
                <div class="announcement-pill-controls">
                    <span class="announcement-counter" id="announcementCounter" aria-live="off">01 / <?= str_pad((string) count($snackbarAnnouncements), 2, '0', STR_PAD_LEFT) ?></span>
                    <button type="button" class="announcement-nav-btn" id="pauseAnnouncements" aria-label="Pause announcements" aria-pressed="false"><span aria-hidden="true">&#8545;</span></button>
                    <button type="button" class="announcement-nav-btn" id="prevAnnouncement" aria-label="Previous announcement"><span aria-hidden="true">&#8249;</span></button>
                    <button type="button" class="announcement-nav-btn" id="nextAnnouncement" aria-label="Next announcement"><span aria-hidden="true">&#8250;</span></button>
                </div>
                <?php endif; ?>
            </div>
            <div class="announcement-pill-content" id="announcementSlides" aria-live="off">
                <?php foreach ($snackbarAnnouncements as $idx => $announcement): ?>
                <article class="announcement-item <?= $idx === 0 ? 'active' : '' ?>" data-index="<?= $idx ?>" role="group" aria-roledescription="slide" aria-label="<?= $idx + 1 ?> of <?= count($snackbarAnnouncements) ?>" <?= $idx === 0 ? '' : 'inert aria-hidden="true"' ?>>
                    <div class="announcement-item-body">
                        <div class="announcement-item-body-header">
                            <?php if (!empty($announcement['badge'])): ?>
                            <span class="announcement-pill-badge"><?= htmlspecialchars($announcement['badge']) ?></span>
                            <?php endif; ?>
                            <h2 class="announcement-pill-title"><?= htmlspecialchars($announcement['title'] ?? 'Latest update') ?></h2>
                        </div>
                        <p class="announcement-pill-text"><?= htmlspecialchars($announcement['text'] ?? '') ?></p>
                    </div>
                    <div class="announcement-item-footer">
                        <span class="announcement-pill-date">
                            <?php if (!empty($announcement['date'])): ?>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v5m8-5v5"/></svg><span><?= htmlspecialchars($announcement['date']) ?></span>
                            <?php endif; ?>
                        </span>
                        <div class="announcement-item-actions">
                            <button type="button" class="announcement-expand" hidden>Read more</button>
                            <?php if (!empty($announcement['link'])): ?>
                            <a href="<?= htmlspecialchars($announcement['link']) ?>" target="_blank" rel="noopener noreferrer" class="announcement-pill-cta"><span>View details</span><span aria-hidden="true">&#8599;</span></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <dialog class="announcement-dialog" id="announcementDialog" aria-labelledby="announcementDialogTitle">
        <div class="announcement-dialog-header"><span>Announcement</span><button type="button" class="announcement-nav-btn" aria-label="Close announcement"><span aria-hidden="true">&#215;</span></button></div>
        <h2 id="announcementDialogTitle"></h2>
        <p class="announcement-dialog-meta"></p>
        <p class="announcement-dialog-text"></p>
    </dialog>
    <script src="components/announcements.js" defer></script>
</section>
<?php endif; ?>

<!-- Section 2: Editorial Bio -->
<section id="bio" class="editorial-bio">
    <div class="container">
        <div class="section-title">About</div>
        <div class="editorial-text">
            <?php foreach (($profile['about'] ?? []) as $para): ?>
                <p><?= htmlspecialchars($para) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- Section 3: Publications -->
<section id="publications" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Publications</h2>

        <ul class="nav nav-tabs" id="pubTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="authored-books-tab" data-bs-toggle="tab" data-bs-target="#authored-books-tab-pane"
                    type="button" role="tab" aria-controls="authored-books-tab-pane" aria-selected="true">Books</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="books-tab" data-bs-toggle="tab" data-bs-target="#books-tab-pane"
                    type="button" role="tab" aria-controls="books-tab-pane" aria-selected="false">Edited Books &
                    Chapters</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pub-tab" data-bs-toggle="tab" data-bs-target="#pub-tab-pane" type="button"
                    role="tab" aria-controls="pub-tab-pane" aria-selected="false">Journals</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="conf-tab" data-bs-toggle="tab" data-bs-target="#conf-tab-pane"
                    type="button" role="tab" aria-controls="conf-tab-pane" aria-selected="false">Conferences</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="prints-tab" data-bs-toggle="tab" data-bs-target="#prints-tab-pane"
                    type="button" role="tab" aria-controls="prints-tab-pane" aria-selected="false">Pre Prints</button>
            </li>
        </ul>

        <div class="tab-content" id="pubTabContent">
            <div class="tab-pane fade show active" id="authored-books-tab-pane" role="tabpanel" aria-labelledby="authored-books-tab"
                tabindex="0">
                <div class="table-responsive"><table class="table custom-table"><tbody>
                    <?php renderPublicationTable($publications['authored_books'] ?? []); ?>
                </tbody></table></div>
            </div>

            <div class="tab-pane fade" id="books-tab-pane" role="tabpanel" aria-labelledby="books-tab"
                tabindex="0">
                <div class="table-responsive"><table class="table custom-table"><tbody>
                    <?php renderPublicationTable($publications['books'] ?? []); ?>
                </tbody></table></div>
            </div>

            <div class="tab-pane fade" id="pub-tab-pane" role="tabpanel" aria-labelledby="pub-tab" tabindex="0">
                <div class="table-responsive"><table class="table custom-table"><tbody>
                    <?php renderPublicationTable($publications['journals'] ?? []); ?>
                </tbody></table></div>
            </div>

            <div class="tab-pane fade" id="conf-tab-pane" role="tabpanel" aria-labelledby="conf-tab" tabindex="0">
                <div class="table-responsive"><table class="table custom-table"><tbody>
                    <?php renderPublicationTable($publications['conferences'] ?? []); ?>
                </tbody></table></div>
            </div>

            <div class="tab-pane fade" id="prints-tab-pane" role="tabpanel" aria-labelledby="prints-tab" tabindex="0">
                <div class="table-responsive"><table class="table custom-table"><tbody>
                    <?php renderPublicationTable($publications['preprints'] ?? []); ?>
                </tbody></table></div>
            </div>
        
        <div class="text-center mt-4">
            <button id="seeMorePubsBtn" class="btn btn-outline-primary rounded-pill px-4" onclick="showMorePubs()">See More <i class="fa fa-chevron-down ms-1"></i></button>
        </div>
        <script>
        function showMorePubs() {
            const activeTabPane = document.querySelector('#pubTabContent .tab-pane.active');
            if (!activeTabPane) return;
            const hiddenRows = activeTabPane.querySelectorAll('.pub-row[style*="display: none"]');
            for (let i = 0; i < 10 && i < hiddenRows.length; i++) {
                hiddenRows[i].style.display = '';
            }
            const remaining = activeTabPane.querySelectorAll('.pub-row[style*="display: none"]');
            if (remaining.length === 0) {
                document.getElementById('seeMorePubsBtn').style.display = 'none';
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            const initialTabPane = document.querySelector('#pubTabContent .tab-pane.active');
            if (initialTabPane) {
                const hiddenRows = initialTabPane.querySelectorAll('.pub-row[style*="display: none"]');
                if (hiddenRows.length === 0) {
                    const btn = document.getElementById('seeMorePubsBtn');
                    if(btn) btn.style.display = 'none';
                }
            }

            const pubTabs = document.querySelectorAll('#pubTab [data-bs-toggle="tab"]');
            pubTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const targetPane = document.querySelector(event.target.getAttribute('data-bs-target'));
                    const hiddenRows = targetPane.querySelectorAll('.pub-row[style*="display: none"]');
                    if (hiddenRows.length === 0) {
                        document.getElementById('seeMorePubsBtn').style.display = 'none';
                    } else {
                        document.getElementById('seeMorePubsBtn').style.display = 'inline-block';
                    }
                });
            });
        });
        </script>
    </div>
</section>

<!-- Section 4: Patents -->
<section id="patents" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Patents</h2>

        <div class="table-responsive">
            <table class="table custom-table">
                <tbody>
                <?php
                $counter = count($patents);
                foreach ($patents as $patent):
                    ?>
                    <tr>
                        <td>
                            <p class="mb-0"><strong><?= $counter-- ?>.</strong>
                                <?= htmlspecialchars($patent['authors'] ?? '') ?>,
                                <strong>'<?= htmlspecialchars($patent['title'] ?? '') ?>'</strong>,
                                Indian Patent Filed <?= htmlspecialchars($patent['filed_year'] ?? '') ?>,
                                App No: <?= htmlspecialchars($patent['application_no'] ?? '') ?>,
                                Patent No.: <?= htmlspecialchars($patent['patent_no'] ?? '') ?>
                            </p>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Section 5: Projects -->
<section id="projects" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Projects</h2>

        <ul class="nav nav-tabs" id="projTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing-tab-pane"
                    type="button" role="tab" aria-controls="ongoing-tab-pane" aria-selected="true">Ongoing</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-tab-pane"
                    type="button" role="tab" aria-controls="completed-tab-pane" aria-selected="false">Completed</button>
            </li>
        </ul>

        <div class="tab-content" id="projTabContent">
            <div class="tab-pane fade show active" id="ongoing-tab-pane" role="tabpanel" aria-labelledby="ongoing-tab"
                tabindex="0">
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th style="min-width: 150px;">Role</th>
                                <th>Funding Agency</th>
                                <th>Amount</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach (($projects['ongoing'] ?? []) as $proj): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($proj['title'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($proj['role'] ?? '') ?></td>
                                <td><?= htmlspecialchars($proj['funding_agency'] ?? '') ?></td>
                                <td><?= htmlspecialchars($proj['amount'] ?? '') ?></td>
                                <td><?= htmlspecialchars($proj['duration'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="completed-tab-pane" role="tabpanel" aria-labelledby="completed-tab"
                tabindex="0">
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th style="min-width: 150px;">Role</th>
                                <th>Funding Agency</th>
                                <th>Amount</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach (($projects['completed'] ?? []) as $proj): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($proj['title'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($proj['role'] ?? '') ?></td>
                                <td><?= htmlspecialchars($proj['funding_agency'] ?? '') ?></td>
                                <td><?= htmlspecialchars($proj['amount'] ?? '') ?></td>
                                <td><?= htmlspecialchars($proj['duration'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Memberships -->
<section id="memberships" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Professional Memberships</h2>

        <ul class="pub-list ps-4">
            <?php foreach ($memberships as $m): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <span class="entry-text"><strong><?= htmlspecialchars($m['role'] ?? '') ?></strong>,
                        <?= htmlspecialchars($m['organization'] ?? '') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 7: Editorship -->
<section id="editorship" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Editorship</h2>

        <ul class="pub-list ps-4">
            <?php foreach ($editorships as $e): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <strong><?= htmlspecialchars($e['role'] ?? '') ?>,</strong>
                    <?= htmlspecialchars($e['journal'] ?? '') ?>
                    <span class="text-muted">[<?= htmlspecialchars($e['duration'] ?? '') ?>]</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 8: Administrative Responsibilities -->
<section id="admin-responsibilities" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Administrative Responsibilities</h2>

        <ul class="pub-list ps-4">
            <?php foreach ($adminResponsibilities as $resp): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <span class="entry-text">
                        <strong><?= htmlspecialchars($resp['role'] ?? '') ?></strong><?php
                            $parts = [];
                            if (!empty($resp['organization'])) $parts[] = htmlspecialchars($resp['organization']);
                            if (!empty($resp['institution'])) $parts[] = htmlspecialchars($resp['institution']);
                            if (!empty($parts)) echo ', ' . implode(', ', $parts);
                        ?>
                        <?php if (!empty($resp['duration'])): ?>
                            <span class="text-muted">[<?= htmlspecialchars($resp['duration']) ?>]</span>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 9: Teaching -->
<section id="teaching" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Courses Taught</h2>

        <ul class="pub-list">
            <?php foreach ($courses as $c): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <?php if (!empty($c['link'])): ?>
                        <a href="<?= htmlspecialchars($c['link']) ?>" target="_blank" rel="noopener noreferrer"
                            class="active-entry-link"><?= htmlspecialchars($c['course'] ?? '') ?></a>
                    <?php else: ?>
                        <span class="entry-text"><?= htmlspecialchars($c['course'] ?? '') ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 10: Seminars -->
<section id="seminars" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Seminar / Conference / Workshops Organised</h2>

        <ul class="pub-list ps-4">
            <?php foreach ($events as $e): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <?php if (!empty($e['link'])): ?>
                        <a href="<?= htmlspecialchars($e['link']) ?>" target="_blank" class="active-entry-link">
                            <strong><?= htmlspecialchars($e['title'] ?? '') ?></strong>
                        </a>
                    <?php else: ?>
                        <strong class="entry-text"><?= htmlspecialchars($e['title'] ?? '') ?></strong>
                    <?php endif; ?>

                    <?php if (!empty($e['location'])): ?>
                        at <?= htmlspecialchars($e['location']) ?>
                    <?php endif; ?>

                    <?php if (!empty($e['date'])): ?>
                        <span class="text-muted">(<?= htmlspecialchars($e['date']) ?>)</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section: Recognition -->
<section id="other-responsibilities" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Recognition</h2>

        <ul class="pub-list ps-4">
            <?php foreach ($otherResponsibilities as $resp): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <span class="entry-text">
                        <strong><?= htmlspecialchars($resp['role'] ?? '') ?></strong><?php
                            $parts = [];
                            if (!empty($resp['organization'])) $parts[] = htmlspecialchars($resp['organization']);
                            if (!empty($resp['institution'])) $parts[] = htmlspecialchars($resp['institution']);
                            if (!empty($parts)) echo ', ' . implode(', ', $parts);
                        ?>
                        <?php if (!empty($resp['duration'])): ?>
                            <span class="text-muted">[<?= htmlspecialchars($resp['duration']) ?>]</span>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>




<!-- Section 10: Awards and Honours -->
<section id="awards" class="bio-section pt-4">

    <div class="container">
        <h2 class="section-title">Awards and Honours</h2>

        <ul class="pub-list ps-4">
            <?php foreach ($awards as $a): ?>
                <li class="mb-3 border-bottom pb-2 section-list-item">
                    <span class="entry-text">
                        <strong><?= htmlspecialchars($a['title'] ?? '') ?></strong>,
                        <?php if (!empty($a['awardee'])): ?>
                            <span class="text-primary"><?= htmlspecialchars($a['awardee']) ?></span>,
                        <?php endif; ?>
                        <?= htmlspecialchars($a['event'] ?? '') ?>
                        <?php if (!empty($a['location'])): ?>
                            <span class="text-muted">(<?= htmlspecialchars($a['location']) ?>)</span>
                        <?php endif; ?>
                        <?php if (!empty($a['link'])): ?>
                            <a href="<?= htmlspecialchars($a['link']) ?>" target="_blank" class="ms-2 text-decoration-none" title="View Certificate/Document">
                                <i class="fa fa-external-link"></i>
                            </a>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php include 'components/footer.php'; ?>