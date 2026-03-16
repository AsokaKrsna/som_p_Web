<?php
$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

$events_json = file_get_contents('data/seminars.json');
$events = json_decode($events_json, true) ?? [];
?>

<!-- Content Area (Bio) -->
<section class="bio-section pt-4">
<div class="container">
<h2 class="section-title">Seminar / Conference / Workshops Organised</h2>
<hr/>
<ul class="pub-list">
    <?php foreach ($events as $e): ?>
        <li class="mb-2">
            <?php if (!empty($e['link'])): ?>
                <a href="<?= htmlspecialchars($e['link']) ?>" target="_blank" class="active-entry-link"><?= htmlspecialchars($e['title']) ?></a>
            <?php else: ?>
                <?= htmlspecialchars($e['title']) ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
</div>
</section>

<?php include 'components/footer.php'; ?>

