<?php
$page_title = "Research Group | Dr. Somanath Tripathy";
include 'components/header.php';

$json_data = file_get_contents('data/research_group.json');
$members = json_decode($json_data, true);

// Helper function to render a member row
function renderMemberRow($member) {
    $hasImage = !empty($member['image']);
    $initials = !empty($member['initials']) ? $member['initials'] : '';
    $name = $member['name'] ?? '';
    $affiliation = $member['affiliation'] ?? '';
    $researchArea = $member['research_area'] ?? '';
    $email = $member['email'] ?? '';
    $rawDetails = $member['raw_details'] ?? '';
    ?>
    <div class="member-row">
        <div class="member-row-header">
            <?php if ($hasImage): ?>
                <img class="member-avatar" src="<?= htmlspecialchars($member['image']) ?>"/>
            <?php else: ?>
                <div class="member-avatar"><?= htmlspecialchars($initials) ?></div>
            <?php endif; ?>
            
            <div class="member-info">
                <h5><?= htmlspecialchars($name) ?></h5>
                <?php if ($affiliation): ?>
                    <p><?= htmlspecialchars($affiliation) ?></p>
                <?php endif; ?>
                <?php if ($researchArea): ?>
                    <div class="mt-1"><span class="research-area"><?= htmlspecialchars($researchArea) ?></span></div>
                <?php endif; ?>
                <?php if ($email): ?>
                    <p class="email mt-1"><i class="fa fa-envelope-o"></i> <?= htmlspecialchars($email) ?></p>
                <?php endif; ?>
                <?php if ($rawDetails && !$researchArea && !$email): ?>
                    <p class="mt-1"><?= htmlspecialchars($rawDetails) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
?>

<section class="bio-section pt-4">
    <div class="container">
        <h2 class="section-title">Research Group</h2>
        <hr>

        <h4 class="subhead">Faculty Members</h4>
        <div class="glass-directory">
            <div class="member-row">
                <div class="member-row-header">
                    <img class="member-avatar" src="pics/som-pic.png" alt="Dr. Somanath Tripathy"/>
                    <div class="member-info">
                        <h5>Dr. Somanath Tripathy</h5>
                        <p>Professor, Department of Computer Science and Engineering, IIT Patna</p>
                        <p class="email mt-1"><i class="fa fa-envelope-o"></i> som [at] iitp.ac.in</p>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-4 mb-4">

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="phd-tab" data-bs-toggle="tab" data-bs-target="#phd-tab-pane" type="button" role="tab" aria-controls="phd-tab-pane" aria-selected="true">Present Ph.D Students</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pastphd-tab" data-bs-toggle="tab" data-bs-target="#pastphd-tab-pane" type="button" role="tab" aria-controls="pastphd-tab-pane" aria-selected="false">Past Ph.D Students</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pastmtech-tab" data-bs-toggle="tab" data-bs-target="#pastmtech-tab-pane" type="button" role="tab" aria-controls="pastmtech-tab-pane" aria-selected="false">Past M.Tech Students</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <!-- Present PhD -->
            <div class="tab-pane fade show active" id="phd-tab-pane" role="tabpanel" aria-labelledby="phd-tab" tabindex="0">
                <div class="glass-directory">
                    <?php 
                    if (!empty($members['phd'])) {
                        foreach($members['phd'] as $member) renderMemberRow($member); 
                    } else {
                        echo "<p class='mt-4 text-center'>Currently no students listed.</p>";
                    }
                    ?>
                </div>
            </div>

            <!-- Past PhD -->
            <div class="tab-pane fade" id="pastphd-tab-pane" role="tabpanel" aria-labelledby="pastphd-tab" tabindex="0">
                <div class="glass-directory">
                    <?php 
                    if (!empty($members['past_phd'])) {
                        foreach($members['past_phd'] as $member) renderMemberRow($member); 
                    }
                    ?>
                </div>
            </div>

            <!-- Past MTech -->
            <div class="tab-pane fade" id="pastmtech-tab-pane" role="tabpanel" aria-labelledby="pastmtech-tab" tabindex="0">
                <div class="glass-directory">
                    <?php 
                    if (!empty($members['past_mtech'])) {
                        foreach($members['past_mtech'] as $member) renderMemberRow($member); 
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
