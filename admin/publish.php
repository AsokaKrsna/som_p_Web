<?php
/**
 * Publish Center — push CMS content changes to GitHub from the browser.
 * Part of the Tripathy Portfolio CMS.
 *
 * Security model (same as the rest of /admin):
 * - Session gate: only a logged-in admin can view or act.
 * - CSRF token on the POST action.
 * - POST-only publish; all git calls are argument-array exec (no shell).
 */
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/publish_lib.php';

// ─────────────────────────────────────────────────────────────
// Defaults + config loading (config file is gitignored & editable)
// ─────────────────────────────────────────────────────────────
$publishDefaults = [
    'repo_path'        => dirname(__DIR__),
    'remote'           => 'origin',
    'branch'           => null,
    'git_bin'          => 'git',
    'command_timeout'  => 120,
    'env'              => ['GIT_TERMINAL_PROMPT' => '0'],
    'commit_user_name' => null,
    'commit_user_email'=> null,
    'commit_prefix'    => 'CMS publish',
];
$publish = $publishDefaults;
if (is_file(__DIR__ . '/publish_config.php')) {
    $loaded = include __DIR__ . '/publish_config.php';
    if (is_array($loaded)) {
        $publish = array_merge($publishDefaults, $loaded);
    }
}

// ─────────────────────────────────────────────────────────────
// Handle the publish action (POST + CSRF + lock)
// ─────────────────────────────────────────────────────────────
$actionMsg  = null;   // ['type' => success|danger|warning, 'text' => ...]
$actionLog  = [];     // step-by-step command output for the UI
$published  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'publish') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $actionMsg = ['type' => 'danger', 'text' => 'Invalid CSRF token. Refresh the page and try again.'];
    } else {
        $lock = pub_is_locked();
        if ($lock === null) {
            $actionMsg = ['type' => 'warning', 'text' => 'A publish is already in progress (or a previous one stalled). Wait a moment and retry.'];
        } else {
            $message = trim($_POST['commit_message'] ?? '');
            if ($message === '') {
                $message = trim(($publish['commit_prefix'] ?? 'CMS publish') . ' — ' . date('d M Y, H:i'));
            }
            // Normalize newlines to spaces for a clean single-line subject.
            $message = trim(preg_replace('/\s+/', ' ', $message));

            // Step 1: stage everything
            $rAdd = pub_add_all($publish);
            $actionLog[] = ['label' => 'Stage changes (git add -A)', 'r' => $rAdd];

            // Step 2: verify something is actually staged
            $hasStaged = false;
            if ($rAdd['ok']) {
                $rDiff = pub_exec(['diff', '--cached', '--quiet'], $publish, 30);
                $hasStaged = !$rDiff['ok']; // exit code 1 = there ARE staged changes
            }

            if ($rAdd['ok'] && !$hasStaged) {
                $actionMsg = ['type' => 'warning', 'text' => 'Nothing to publish — the repository is already up to date.'];
            } elseif ($rAdd['ok']) {
                // Step 3: commit
                $rCommit = pub_commit($publish, $message);
                $actionLog[] = ['label' => 'Commit', 'r' => $rCommit];

                if ($rCommit['ok']) {
                    $branch = $publish['branch'] ?? pub_branch($publish) ?? 'main';
                    // Step 4: push
                    $rPushSet = pub_push($publish, $branch);
                    $rPush = $rPushSet['push'];
                    $actionLog[] = ['label' => "Push to {$rPush['remote']}/{$rPush['branch']}", 'r' => $rPush];

                    if ($rPush['ok']) {
                        $published = true;
                        $actionMsg = ['type' => 'success', 'text' => "Published to GitHub: {$rPush['remote']}/{$rPush['branch']} — “{$message}”"];
                    } else {
                        $hint = trim($rPush['err'] ?: $rPush['out']);
                        $actionMsg = ['type' => 'danger', 'text' => 'Commit created but push failed. ' . $hint];
                    }
                } else {
                    $actionMsg = ['type' => 'danger', 'text' => 'Commit failed: ' . trim($rCommit['err'] ?: $rCommit['out'])];
                }
            } else {
                $actionMsg = ['type' => 'danger', 'text' => 'Could not stage changes: ' . trim($rAdd['err'] ?: $rAdd['out'])];
            }
            pub_unlock($lock);
        }
    }
}

// ─────────────────────────────────────────────────────────────
// Gather current state for the status panel
// ─────────────────────────────────────────────────────────────
$gitVersion = pub_git_version($publish);
$repoPath   = pub_repo_path($publish);
$status     = $repoPath ? pub_status($publish) : null;
$branch     = $status['branch'] ?? null;
$remoteUrl  = pub_remote_url($publish);
$speed      = ($status['ok'] && $branch) ? pub_ahead_behind($publish, $branch) : null;
$recent     = $repoPath ? pub_log($publish, 8) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Publish Center | Tripathy Portfolio CMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <link href="admin.css" rel="stylesheet"/>
</head>
<body class="admin-page">
    <!-- Floating Action Bar -->
    <div class="floating-action-bar">
        <a href="dashboard.php" class="floating-btn">
            <i class="fa fa-th-large"></i> <span class="d-none d-md-inline">Dashboard</span>
        </a>
        <a href="../index.php" class="floating-btn" target="_blank" title="View Site">
            <i class="fa fa-external-link"></i> <span class="d-none d-md-inline">View Site</span>
        </a>
        <button class="floating-btn dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">
            <i class="fa fa-moon-o" id="darkModeIcon"></i>
        </button>
        <a href="logout.php" class="floating-btn" style="color: #ef4444;" title="Logout">
            <i class="fa fa-sign-out"></i> <span class="d-none d-md-inline">Logout</span>
        </a>
    </div>

    <div class="container dashboard-content mt-4" style="max-width: 980px;">
        <h2 class="section-title"><i class="fa fa-cloud-upload me-2" style="color: var(--accent-cyan);"></i>Publish Center</h2>
        <p class="page-title text-center">Commit &amp; push website changes to GitHub</p>

        <?php if ($actionMsg): ?>
            <div class="alert alert-<?= $actionMsg['type'] === 'success' ? 'success' : ($actionMsg['type'] === 'warning' ? 'warning' : 'danger') ?>">
                <?= htmlspecialchars($actionMsg['text']) ?>
            </div>
        <?php endif; ?>

        <?php if ($actionLog): ?>
            <div class="admin-card">
                <h5><i class="fa fa-terminal" style="color: var(--accent-cyan);"></i> Publish Steps</h5>
                <?php foreach ($actionLog as $step): ?>
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="fa <?= $step['r']['ok'] ? 'fa-check-circle' : 'fa-times-circle' ?>"
                           style="color: <?= $step['r']['ok'] ? '#16a34a' : '#dc2626' ?>; margin-top: 3px;"></i>
                        <div>
                            <strong class="small"><?= htmlspecialchars($step['label']) ?></strong>
                            <?php if (!$step['r']['ok']): ?>
                                <pre class="small mb-0" style="white-space: pre-wrap; color: #dc2626;"><?= htmlspecialchars(trim($step['r']['err'] ?: $step['r']['out'])) ?></pre>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Diagnostics -->
        <?php if (!$gitVersion || !$repoPath): ?>
            <div class="alert alert-warning">
                <strong>Publish Center cannot run git.</strong><br>
                <?php if (!$gitVersion): ?>
                    The git binary (<?= htmlspecialchars($publish['git_bin']) ?>) was not found or is not executable.<br>
                <?php endif; ?>
                <?php if (!$repoPath): ?>
                    <?= htmlspecialchars($publish['repo_path']) ?> is not a git repository.<br>
                <?php endif; ?>
                Fix <code>admin/publish_config.php</code> and reload.
            </div>
        <?php else: ?>

        <!-- Repository status -->
        <div class="admin-card">
            <h5><i class="fa fa-git" style="color: var(--accent-cyan);"></i> Repository Status</h5>
            <div class="row small">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Repo:</strong> <code><?= htmlspecialchars($repoPath) ?></code></p>
                    <p class="mb-1"><strong>Branch:</strong> <code><?= htmlspecialchars($branch ?? '?') ?></code></p>
                    <p class="mb-1"><strong>Remote:</strong>
                        <?php if ($remoteUrl): ?>
                            <code><?= htmlspecialchars($remoteUrl) ?></code>
                        <?php else: ?>
                            <span class="text-danger">not configured</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Working tree:</strong>
                        <?php if ($status['ok']): ?>
                            <?= $status['clean']
                                ? '<span style="color:#16a34a;"><i class="fa fa-check-circle"></i> Clean — nothing to publish</span>'
                                : '<span style="color:#d97706;"><i class="fa fa-exclamation-circle"></i> ' . count($status['entries']) . ' change(s) pending</span>' ?>
                        <?php else: ?>
                            <span class="text-danger">git status failed: <?= htmlspecialchars($status['error'] ?? '') ?></span>
                        <?php endif; ?>
                    </p>
                    <?php if ($speed && $speed['ahead'] !== null): ?>
                        <p class="mb-1"><strong>Sync state:</strong>
                            <?php if ($speed['ahead'] === 0 && $speed['behind'] === 0): ?>
                                <span style="color:#16a34a;"><i class="fa fa-check-circle"></i> Up to date with <?= htmlspecialchars($speed['upstream']) ?></span>
                            <?php else: ?>
                                ahead <?= $speed['ahead'] ?> commit(s),
                                behind <?= $speed['behind'] ?> commit(s)
                                <span class="text-muted">(vs <?= htmlspecialchars($speed['upstream']) ?>)</span>
                            <?php endif; ?>
                        </p>
                    <?php elseif ($speed && $speed['ahead'] === null): ?>
                        <p class="mb-1"><strong>Sync state:</strong> no upstream yet (first push will set it)</p>
                    <?php endif; ?>
                    <p class="mb-1"><strong>git version:</strong> <?= htmlspecialchars($gitVersion) ?></p>
                </div>
            </div>

            <?php if ($status['ok'] && !$status['clean']): ?>
                <div class="table-responsive mt-2">
                    <table class="table table-sm custom-table mb-0">
                        <thead><tr><th>Change</th><th>File</th></tr></thead>
                        <tbody>
                        <?php foreach (array_slice($status['entries'], 0, 100) as $e): ?>
                            <tr>
                                <td class="small text-nowrap"><?= htmlspecialchars(pub_describe_status($e['x'], $e['y'])) ?></td>
                                <td class="small"><code><?= htmlspecialchars($e['file']) ?></code><?php if ($e['orig']): ?> <span class="text-muted">&larr; <?= htmlspecialchars($e['orig']) ?></span><?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($status['entries']) > 100): ?>
                            <tr><td colspan="2" class="small text-muted">…and <?= count($status['entries']) - 100 ?> more</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Publish form -->
        <div class="admin-card">
            <h5><i class="fa fa-rocket" style="color: var(--accent-cyan);"></i> Publish</h5>
            <p class="text-muted small mb-3">
                Publishes <strong>every change in the repository</strong> — CMS content, uploaded files, and any code edits —
                as a single commit to <code><?= htmlspecialchars($publish['remote'] ?? 'origin') ?></code>.
                <?php if ($branch): ?>Pushes <code><?= htmlspecialchars($branch) ?></code> to its upstream on GitHub.<?php endif; ?>
            </p>
            <form method="POST" action="publish.php" autocomplete="off">
                <input type="hidden" name="action" value="publish">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="mb-2">
                    <label class="small fw-bold" style="color: var(--text-muted);">Commit message (optional)</label>
                    <input type="text" name="commit_message" class="form-control form-control-sm"
                           maxlength="200" placeholder="e.g. Update publications and add new seminar brochure">
                </div>
                <button type="submit" class="btn btn-custom w-100" <?= ($status['ok'] && $remoteUrl) ? '' : 'disabled' ?>>
                    <i class="fa fa-cloud-upload"></i>
                    Publish to GitHub
                </button>
            </form>
        </div>

        <!-- Recent commits -->
        <?php if ($recent): ?>
        <div class="admin-card">
            <h5><i class="fa fa-history" style="color: var(--accent-cyan);"></i> Recent Commits</h5>
            <table class="table table-sm custom-table mb-0">
                <tbody>
                <?php foreach ($recent as $c): ?>
                    <tr>
                        <td class="small text-nowrap" style="width: 90px;"><code><?= htmlspecialchars($c['hash']) ?></code></td>
                        <td class="small text-nowrap text-muted" style="width: 150px;"><?= htmlspecialchars($c['date']) ?></td>
                        <td class="small"><?= htmlspecialchars($c['subject']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php endif; /* end diagnostics else */ ?>
    </div>

    <script src="admin-common.js"></script>
</body>
</html>
