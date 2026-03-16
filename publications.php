<?php
$page_title = "Publications | Dr. Somanath Tripathy";
include 'components/header.php';

// Fetch and decode JSON
$json_data = file_get_contents('data/publications.json');
$publications = json_decode($json_data, true);
?>

<section class="bio-section pt-4">
    <div class="container">
        <h2 class="section-title">Publications</h2>
        <hr>
        
        <!-- Tabs Nav -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
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

        <!-- Tabs Content -->
        <div class="tab-content" id="myTabContent">
            
            <!-- Journals -->
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
            
            <!-- Conferences -->
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
            
            <!-- Preprints -->
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
            
            <!-- Books -->
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

<?php include 'components/footer.php'; ?>
