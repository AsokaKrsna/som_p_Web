<?php
session_start();
$isAdmin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

// Fetch all JSON data
$publications = json_decode(file_get_contents('data/publications.json'), true);
$patents = json_decode(file_get_contents('data/patents.json'), true);
$projects = json_decode(file_get_contents('data/projects.json'), true);
$courses = json_decode(file_get_contents('data/teaching.json'), true) ?? [];
$events = json_decode(file_get_contents('data/seminars.json'), true) ?? [];
$memberships = json_decode(file_get_contents('data/memberships.json'), true) ?? [];
$editorships = json_decode(file_get_contents('data/editorships.json'), true) ?? [];
$awards = json_decode(file_get_contents('data/awards.json'), true) ?? [];
?>

<!-- Section 1: Hero Section -->
<section id="home" class="hero-section">
    <div class="hero-glow-orb-1"></div>
    <div class="hero-glow-orb-2"></div>

    <div class="container hero-container">
        <div class="hero-content">
            <h1 class="hero-title">Dr. Somanath<br>Tripathy</h1>
            <h2 class="hero-subtitle">Professor</h2>
            
            <div class="hero-details">
                <div class="hero-contact-item">
                    <i class="fa fa-university"></i>
                    <span>Department of Computer Science & Engineering</span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-map-marker"></i>
                    <span>Indian Institute of Technology Patna</span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-envelope"></i>
                    <span>som[at]iitp.ac.in</span>
                </div>
                <div class="hero-contact-item">
                    <i class="fa fa-phone"></i>
                    <span>+91-6115-233-036</span>
                </div>
            </div>

            <div class="mt-4 pt-2">
                <a href="#publications" class="btn btn-custom">Explore Research <i class="fa fa-arrow-down ms-2"></i></a>
            </div>
        </div>

        <div class="hero-visuals">
            <div class="glass-avatar-wrapper">
                <img src="images/som.jpg" alt="Dr. Somanath Tripathy" class="profile-img-3d">
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Editorial Bio -->
<section id="bio" class="editorial-bio">
    <div class="container">
        <div class="section-title">About</div>
        <div class="editorial-text">
            <p>Dr. Somanath Tripathy received his PhD from IIT Guwahati in 2007. Currently, he is a professor in the Department of Computer Science and Engineering at the Indian Institute of Technology, Patna, where he has been a faculty member since December 2008. Prof. Tripathy has held significant administrative positions at IIT Patna, including Associate Dean of Academics (January 2016 - March 2017), Head, Computer Centre (November 2022-November 2023) and Associate Dean of Administration (July 2021 - November 2023).</p>
            <p>His research interests encompass Cybersecurity, Malware Detection, Secure Machine Learning, Lightweight Cryptography, and Blockchain. Prof. Tripathy holds two patents and has published over 130 research papers in reputed journals and conferences. He has led several projects as Principal Investigator, notably his team developed a malware detection app presented to the Bureau of Police Research and Development (BPRD) and the Ministry of Home Affairs (MHA) as part of a sponsored project.</p>
            <p>Dr. Tripathy is currently an editor of the IETE Technical Review and an associate editor of the journal Multimedia Tools and Applications.</p>
        </div>
    </div>
</section>

<!-- Section 3: Publications -->
<section id="publications" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('publications.json', 'publications')" title="Edit publications"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Publications</h2>
        
        <ul class="nav nav-tabs" id="pubTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pub-tab" data-bs-toggle="tab" data-bs-target="#pub-tab-pane" type="button" role="tab" aria-controls="pub-tab-pane" aria-selected="true">Journals</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="conf-tab" data-bs-toggle="tab" data-bs-target="#conf-tab-pane" type="button" role="tab" aria-controls="conf-tab-pane" aria-selected="false">Conferences</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="prints-tab" data-bs-toggle="tab" data-bs-target="#prints-tab-pane" type="button" role="tab" aria-controls="prints-tab-pane" aria-selected="false">Pre Prints</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="books-tab" data-bs-toggle="tab" data-bs-target="#books-tab-pane" type="button" role="tab" aria-controls="books-tab-pane" aria-selected="false">Edited Books & Chapters</button>
            </li>
        </ul>

        <div class="tab-content" id="pubTabContent">
            <div class="tab-pane fade show active" id="pub-tab-pane" role="tabpanel" aria-labelledby="pub-tab" tabindex="0">
                <table class="table pub-list">
                    <?php 
                    $counter = count($publications['journals']);
                    foreach($publications['journals'] as $pub): 
                    ?>
                    <tr><td>
                        <p><strong><?= $counter-- ?>.</strong> <?= htmlspecialchars($pub['details']) ?>
                            <?php if(!empty($pub['link'])): ?>
                                <a href="<?= htmlspecialchars($pub['link']) ?>" target="_blank" rel="noopener noreferrer">
                                    <strong><?= htmlspecialchars($pub['title']) ?></strong>
                                </a>,
                            <?php else: ?>
                                <strong><?= htmlspecialchars($pub['title']) ?></strong>,
                            <?php endif; ?>
                            <?php if(!empty($pub['impact_factor'])): ?>
                                <br><span style="color: var(--accent-blue); font-weight: 500;"><?= htmlspecialchars($pub['impact_factor']) ?></span>
                            <?php endif; ?>
                        </p>
                    </td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="tab-pane fade" id="conf-tab-pane" role="tabpanel" aria-labelledby="conf-tab" tabindex="0">
                <table class="table pub-list">
                    <?php 
                    $counter = count($publications['conferences']);
                    foreach($publications['conferences'] as $pub): 
                    ?>
                    <tr><td>
                        <p><strong><?= $counter-- ?>.</strong> <?= htmlspecialchars($pub['details']) ?>
                            <?php if(!empty($pub['link'])): ?>
                                <a href="<?= htmlspecialchars($pub['link']) ?>" target="_blank" rel="noopener noreferrer">
                                    <strong><?= htmlspecialchars($pub['title']) ?></strong>
                                </a>,
                            <?php else: ?>
                                <strong><?= htmlspecialchars($pub['title']) ?></strong>,
                            <?php endif; ?>
                            <?php if(!empty($pub['impact_factor'])): ?>
                                <br><span style="color: var(--accent-blue); font-weight: 500;"><?= htmlspecialchars($pub['impact_factor']) ?></span>
                            <?php endif; ?>
                        </p>
                    </td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="tab-pane fade" id="prints-tab-pane" role="tabpanel" aria-labelledby="prints-tab" tabindex="0">
                <table class="table pub-list">
                    <?php 
                    $counter = count($publications['preprints']);
                    foreach($publications['preprints'] as $pub): 
                    ?>
                    <tr><td>
                        <p><strong><?= $counter-- ?>.</strong> <?= htmlspecialchars($pub['details']) ?>
                            <?php if(!empty($pub['link'])): ?>
                                <a href="<?= htmlspecialchars($pub['link']) ?>" target="_blank" rel="noopener noreferrer">
                                    <strong><?= htmlspecialchars($pub['title']) ?></strong>
                                </a>,
                            <?php else: ?>
                                <strong><?= htmlspecialchars($pub['title']) ?></strong>,
                            <?php endif; ?>
                            <?php if(!empty($pub['impact_factor'])): ?>
                                <br><span style="color: var(--accent-blue); font-weight: 500;"><?= htmlspecialchars($pub['impact_factor']) ?></span>
                            <?php endif; ?>
                        </p>
                    </td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="tab-pane fade" id="books-tab-pane" role="tabpanel" aria-labelledby="books-tab" tabindex="0">
                <table class="table pub-list">
                    <?php 
                    $counter = count($publications['books']);
                    foreach($publications['books'] as $pub): 
                    ?>
                    <tr><td>
                        <p><strong><?= $counter-- ?>.</strong> <?= htmlspecialchars($pub['details']) ?>
                            <?php if(!empty($pub['link'])): ?>
                                <a href="<?= htmlspecialchars($pub['link']) ?>" target="_blank" rel="noopener noreferrer">
                                    <strong><?= htmlspecialchars($pub['title']) ?></strong>
                                </a>,
                            <?php else: ?>
                                <strong><?= htmlspecialchars($pub['title']) ?></strong>,
                            <?php endif; ?>
                            <?php if(!empty($pub['impact_factor'])): ?>
                                <br><span style="color: var(--accent-blue); font-weight: 500;"><?= htmlspecialchars($pub['impact_factor']) ?></span>
                            <?php endif; ?>
                        </p>
                    </td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Patents -->
<section id="patents" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('patents.json', 'patents')" title="Edit patents"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Patents</h2>
        
        <table class="table pub-list table-striped">
            <?php 
            $counter = count($patents);
            foreach($patents as $patent): 
            ?>
            <tr><td>
                <p><strong><?= $counter-- ?>.</strong> 
                    <strong><?= htmlspecialchars($patent['title']) ?></strong>, 
                    <?= htmlspecialchars($patent['details']) ?>
                </p>
            </td></tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<!-- Section 5: Projects -->
<section id="projects" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('projects.json', 'projects')" title="Edit projects"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Projects</h2>

        <ul class="nav nav-tabs" id="projTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing-tab-pane" type="button" role="tab" aria-controls="ongoing-tab-pane" aria-selected="true">Ongoing</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-tab-pane" type="button" role="tab" aria-controls="completed-tab-pane" aria-selected="false">Completed</button>
            </li>
        </ul>
        
        <div class="tab-content" id="projTabContent">
            <div class="tab-pane fade show active" id="ongoing-tab-pane" role="tabpanel" aria-labelledby="ongoing-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Agency</th>
                            <th>Amount</th>
                            <th>Duration</th>
                        </tr>
                        <?php foreach($projects['ongoing'] as $proj): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($proj['title']) ?></strong></td>
                            <td><?= htmlspecialchars($proj['role']) ?></td>
                            <td><strong><?= htmlspecialchars($proj['agency']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($proj['amount']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($proj['duration']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="completed-tab-pane" role="tabpanel" aria-labelledby="completed-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Agency</th>
                            <th>Amount</th>
                            <th>Duration</th>
                        </tr>
                        <?php foreach($projects['completed'] as $proj): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($proj['title']) ?></strong></td>
                            <td><?= htmlspecialchars($proj['role']) ?></td>
                            <td><strong><?= htmlspecialchars($proj['agency']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($proj['amount']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($proj['duration']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Teaching -->
<section id="teaching" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('teaching.json', 'teaching')" title="Edit teaching"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Courses Taught</h2>
        
        <ul class="pub-list">
            <?php foreach ($courses as $c): ?>
                <li class="mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                    <?php if (!empty($c['link'])): ?>
                        <a href="<?= htmlspecialchars($c['link']) ?>" target="_blank" rel="noopener noreferrer" class="active-entry-link"><?= htmlspecialchars($c['course']) ?></a>
                    <?php else: ?>
                        <span style="font-size: 1.05rem; color: var(--text-muted);"><?= htmlspecialchars($c['course']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 7: Seminars -->
<section id="seminars" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('seminars.json', 'seminars')" title="Edit seminars"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Seminar / Conference / Workshops Organised</h2>
        
        <ul class="pub-list list-unstyled ps-0">
            <?php foreach ($events as $e): ?>
                <li class="mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                    <?php if (!empty($e['link'])): ?>
                        <a href="<?= htmlspecialchars($e['link']) ?>" target="_blank" class="active-entry-link"><?= htmlspecialchars($e['title']) ?></a>
                    <?php else: ?>
                        <span style="font-size: 1.05rem; color: var(--text-muted);"><?= htmlspecialchars($e['title']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 8: Memberships -->
<section id="memberships" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('memberships.json', 'memberships')" title="Edit memberships"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Professional Memberships</h2>
        
        <ul class="pub-list list-unstyled ps-0">
            <?php foreach ($memberships as $m): ?>
                <li class="mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                    <span style="font-size: 1.05rem; color: var(--text-muted);"><?= htmlspecialchars($m['membership']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 9: Editorship -->
<section id="editorship" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('editorships.json', 'editorship')" title="Edit editorship"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Editorial Activities</h2>
        
        <ul class="pub-list list-unstyled ps-0">
            <?php foreach ($editorships as $e): ?>
                <li class="mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                    <strong><?= htmlspecialchars($e['role']) ?>,</strong> 
                    <?= htmlspecialchars($e['journal']) ?> 
                    <span class="text-muted">[<?= htmlspecialchars($e['duration']) ?>]</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Section 10: Awards -->
<section id="awards" class="bio-section pt-4">
    <?php if ($isAdmin): ?>
    <button class="inline-edit-btn" onclick="openInlineEditor('awards.json', 'awards')" title="Edit awards"><i class="fa fa-pencil"></i></button>
    <?php endif; ?>

    <div class="container">
        <h2 class="section-title">Awards and Honours</h2>
        
        <ul class="pub-list list-unstyled ps-0">
            <?php foreach ($awards as $a): ?>
                <li class="mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                    <span style="font-size: 1.05rem; color: var(--text-muted);"><?= htmlspecialchars($a['award']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php if ($isAdmin): ?>
<script>
window.__isAdmin = true;
</script>
<script src="components/inline-edit.js"></script>
<?php endif; ?>

<?php include 'components/footer.php'; ?>
