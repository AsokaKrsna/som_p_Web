<?php
$page_title = "Dr. Somanath Tripathy | Academic Portfolio";
include 'components/header.php';

$editorships_json = file_get_contents('data/editorships.json');
$editorships = json_decode($editorships_json, true) ?? [];
?>

<!-- Content Area (Bio) -->
<section class="bio-section pt-4">
<div class="container">
<h2 class="section-title">Editorial Activities</h2>
<hr/>
<ul class="pub-list list-unstyled ps-0">
    <?php foreach ($editorships as $e): ?>
        <li class="mb-3 border-bottom pb-2">
            <strong><?= htmlspecialchars($e['role']) ?>,</strong> 
            <?= htmlspecialchars($e['journal']) ?> 
            <span class="text-muted">[<?= htmlspecialchars($e['duration']) ?>]</span>
        </li>
    <?php endforeach; ?>
</ul>
</div>
</section>

<?php include 'components/footer.php'; ?>

