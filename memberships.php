<?php
$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

$memberships_json = file_get_contents('data/memberships.json');
$memberships = json_decode($memberships_json, true) ?? [];
?>

<!-- Content Area (Bio) -->
<section class="bio-section pt-4">
<div class="container">
<h2 class="section-title">Professional Memberships</h2>
<hr/>
<ul class="pub-list">
    <?php foreach ($memberships as $m): ?>
        <li class="mb-3"><?= htmlspecialchars($m['membership']) ?></li>
    <?php endforeach; ?>
</ul>
</div>
</section>

<?php include 'components/footer.php'; ?>

