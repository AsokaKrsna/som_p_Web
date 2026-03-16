<?php
$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

$teaching_json = file_get_contents('data/teaching.json');
$courses = json_decode($teaching_json, true) ?? [];
?>

<!-- Content Area (Bio) -->
<section class="bio-section pt-4">
<div class="container">
<h2 class="section-title">Courses Taught:</h2>
<hr/>
<ul class="pub-list">
    <?php foreach ($courses as $c): ?>
        <li class="mb-2">
            <?php if (!empty($c['link'])): ?>
                <a href="<?= htmlspecialchars($c['link']) ?>" target="_blank" class="active-entry-link"><?= htmlspecialchars($c['course']) ?></a>
            <?php else: ?>
                <?= htmlspecialchars($c['course']) ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
</div>
</section>

<?php include 'components/footer.php'; ?>

