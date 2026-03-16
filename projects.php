<?php
$page_title = "Projects | Dr. Somanath Tripathy";
include 'components/header.php';

$json_data = file_get_contents('data/projects.json');
$projects = json_decode($json_data, true);
?>

<section class="bio-section pt-4">
    <div class="container">
        <h2 class="section-title">Projects</h2>
        <hr>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing-tab-pane" type="button" role="tab" aria-controls="ongoing-tab-pane" aria-selected="true">Ongoing</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-tab-pane" type="button" role="tab" aria-controls="completed-tab-pane" aria-selected="false">Completed</button>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
            
            <!-- Ongoing Tab -->
            <div class="tab-pane fade show active" id="ongoing-tab-pane" role="tabpanel" aria-labelledby="ongoing-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Agency</th>
                            <th>Proposed Amount</th>
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

            <!-- Completed Tab -->
            <div class="tab-pane fade" id="completed-tab-pane" role="tabpanel" aria-labelledby="completed-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Agency</th>
                            <th>Proposed Amount</th>
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

<?php include 'components/footer.php'; ?>
