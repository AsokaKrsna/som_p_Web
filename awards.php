<?php
$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

$awards_json = file_get_contents('data/awards.json');
$awards = json_decode($awards_json, true) ?? [];
?>

<!-- Content Area (Bio) -->
<section class="bio-section pt-4">
<div class="container">
<h2 class="section-title">Awards and Honours</h2>
<hr/>
<ul class="pub-list">
    <?php foreach ($awards as $a): ?>
        <li class="mb-2"><?= htmlspecialchars($a['award']) ?></li>
    <?php endforeach; ?>
</ul>
</div>
</section>

<?php include 'components/footer.php'; ?>

