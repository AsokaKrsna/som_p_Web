<?php
$page_title = "Patents | Dr. Somanath Tripathy";
include 'components/header.php';

$json_data = file_get_contents('data/patents.json');
$patents = json_decode($json_data, true);
?>

<section class="bio-section pt-4">
    <div class="container">
        <h2 class="section-title">Patents</h2>
        <hr>
        
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

<?php include 'components/footer.php'; ?>
