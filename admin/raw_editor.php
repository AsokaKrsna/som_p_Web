<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$file = $_GET['file'] ?? '';
// Basic sanitization
$file = basename($file);
$path = "../data/" . $file;

if (!file_exists($path)) {
    die("File not found or not specified.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF validation
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $message = "<div class='alert alert-danger mt-3'>Invalid request. Please try again.</div>";
    } else {
        $new_content = $_POST['json_content'] ?? '';
        
        // Validate JSON
        json_decode($new_content);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Create backup of the current file before overwriting
            if (file_exists($path)) {
                copy($path, $path . ".bak");
            }
            
            // Save the file
            if (file_put_contents($path, $new_content)) {
                $message = "<div class='alert alert-success mt-3'>Successfully updated {$file}! A backup (.bak) was saved.</div>";
            } else {
                $message = "<div class='alert alert-danger mt-3'>Failed to write to {$file}. Check permissions.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger mt-3'>Invalid JSON structure. Changes were not saved.</div>";
        }
    }
}

$current_content = file_get_contents($path);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Edit <?= htmlspecialchars($file) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <link href="admin.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
</head>
<body class="admin-page">
    <!-- Floating Action Bar -->
    <div class="floating-action-bar">
        <div class="floating-btn d-none d-md-flex" style="cursor: default; opacity: 0.8;">
            <span>Editing: <span style="color: var(--accent-cyan); font-weight: bold;"><?= htmlspecialchars($file) ?></span></span>
        </div>
        <a href="dashboard.php" class="floating-btn" title="Dashboard">
            <i class="fa fa-th-large"></i> <span class="d-none d-md-inline">Dashboard</span>
        </a>
        <button class="floating-btn dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">
            <i class="fa fa-moon-o" id="darkModeIcon"></i>
        </button>
    </div>

    <div class="container-fluid editor-container">

    <?= $message ?? '' ?>

    <form method="POST" id="mainForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="row">
            <!-- Left Pane: Visual Form Editor -->
            <div class="col-lg-7">
                <h5 class="mb-3">Visual Form Editor</h5>
                <div class="form-pane" id="visualEditor">
                    <!-- Javascript will populate this -->
                </div>
            </div>

            <!-- Right Pane: Raw JSON -->
            <div class="col-lg-5">
                <h5 class="mb-3">Raw JSON Data (Ace Editor)</h5>
                <div id="aceEditor"><?= htmlspecialchars($current_content) ?></div>
                <input type="hidden" name="json_content" id="jsonTarget">
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-custom flex-fill shadow-lg"><i class="fa fa-check"></i> Save Changes</button>
                    <button type="button" class="btn btn-custom btn-preview flex-fill shadow-lg" id="previewBtn" onclick="openPreviewModal()"><i class="fa fa-eye"></i> Preview</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Shared HTML escape utility
function escapeHtml(text) {
    if (text === null || typeof text === 'undefined') return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// Initialize Ace Editor
const editor = ace.edit("aceEditor");
editor.setTheme("ace/theme/tomorrow_night_eighties");
editor.session.setMode("ace/mode/json");
editor.setOptions({
    fontSize: "14px",
    showPrintMargin: false,
    useSoftTabs: true,
    tabSize: 4
});

const jsonTarget = document.getElementById('jsonTarget');
const visualEditor = document.getElementById('visualEditor');
const mainForm = document.getElementById('mainForm');

let currentData = {};
let isArrayRoot = false;

// Sync Ace to Hidden Input on form submit
mainForm.addEventListener('submit', () => {
    jsonTarget.value = editor.getValue();
});

function renderForm() {
    try {
        currentData = JSON.parse(editor.getValue());
        isArrayRoot = Array.isArray(currentData);
        visualEditor.innerHTML = '';
        
        let iterableData = isArrayRoot ? { "items": currentData } : currentData;
        
        for (const [category, items] of Object.entries(iterableData)) {
            const categoryTitle = category.charAt(0).toUpperCase() + category.slice(1).replace(/_/g, ' ');
            
            const section = document.createElement('div');
            section.className = 'card bg-transparent border-secondary mb-4';
            section.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.03);">
                    <h5 class="m-0 text-primary">${categoryTitle}</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem('${category}')">+ Add New Entry</button>
                </div>
                <div class="card-body p-2 sortable-list" data-category="${category}"></div>`;
            
            const listContainer = section.querySelector('.sortable-list');
            
            items.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'form-card';
                card.setAttribute('data-index', index);
                
                let cardHtml = `
                   <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                       <div class="d-flex align-items-center">
                           <span class="drag-handle mr-2"><i class="fas fa-grip-vertical"></i></span>
                           <strong>Entry #${index + 1}</strong>
                       </div>
                       <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteItem('${category}', ${index})">
                           <i class="fa fa-trash"></i> Delete
                       </button>
                   </div>`;
                
                for (const [key, value] of Object.entries(item)) {
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    const isLongText = key === 'details' || key === 'description' || (typeof value === 'string' && value.length > 60);
                    
                    cardHtml += `<div class="mb-3"><label class="form-label small">${label}</label>`;
                    
                    if (isLongText) {
                        cardHtml += `<textarea class="form-control form-control-sm" rows="3" onchange="updateItem('${category}', ${index}, '${key}', this.value)">${escapeHtml(value)}</textarea>`;
                    } else {
                        cardHtml += `<input type="text" class="form-control form-control-sm" value="${escapeHtml(value)}" onchange="updateItem('${category}', ${index}, '${key}', this.value)">`;
                    }
                    cardHtml += `</div>`;
                }
                card.innerHTML = cardHtml;
                listContainer.appendChild(card);
            });
            
            visualEditor.appendChild(section);

            // Initialize Sortable for this category
            new Sortable(listContainer, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    const cat = evt.from.getAttribute('data-category');
                    const oldIndex = evt.oldIndex;
                    const newIndex = evt.newIndex;
                    
                    const targetArray = isArrayRoot ? currentData : currentData[cat];
                    const item = targetArray.splice(oldIndex, 1)[0];
                    targetArray.splice(newIndex, 0, item);
                    
                    editor.setValue(JSON.stringify(currentData, null, 4), -1);
                    renderForm();
                }
            });
        }
    } catch (e) {
        visualEditor.innerHTML = `<div class="alert alert-warning border border-warning"><strong>JSON Syntax Error:</strong> Visual form disabled. Please fix the raw JSON schema first.</div>`;
    }
}

function updateAce() {
    editor.setValue(JSON.stringify(currentData, null, 4), -1);
    renderForm();
}

window.updateItem = function(category, index, key, newValue) {
    if (isArrayRoot) {
        currentData[index][key] = newValue;
    } else {
        currentData[category][index][key] = newValue;
    }
    updateAce();
};

window.deleteItem = function(category, index) {
    if(confirm('Delete this entry permanently?')) {
        if (isArrayRoot) {
            currentData.splice(index, 1);
        } else {
            currentData[category].splice(index, 1);
        }
        updateAce();
    }
};

// ──────────────────────────────────
// Preview Modal
// ──────────────────────────────────
window.openPreviewModal = function() {
    try {
        const data = JSON.parse(editor.getValue());
        let previewHtml = '';
        
        if (Array.isArray(data)) {
            // Simple array: render as a list
            previewHtml = '<div class="preview-list">';
            data.forEach((item, i) => {
                previewHtml += `<div class="preview-card">`;
                previewHtml += `<div class="preview-card-header">Entry #${i + 1}</div>`;
                for (const [key, val] of Object.entries(item)) {
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    previewHtml += `<div class="preview-field"><span class="preview-label">${label}:</span> <span class="preview-value">${escapeHtml(String(val))}</span></div>`;
                }
                previewHtml += `</div>`;
            });
            previewHtml += '</div>';
        } else {
            // Object with categories
            for (const [category, items] of Object.entries(data)) {
                if (!Array.isArray(items)) continue;
                const catLabel = category.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                previewHtml += `<h6 style="color: var(--accent-cyan); margin: 1.5rem 0 0.75rem; font-weight: 700; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(34,211,238,0.15);">${catLabel}</h6>`;
                previewHtml += '<div class="preview-list">';
                items.forEach((item, i) => {
                    previewHtml += `<div class="preview-card">`;
                    previewHtml += `<div class="preview-card-header">${i + 1}</div>`;
                    for (const [key, val] of Object.entries(item)) {
                        if (!val) continue;
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                        const isLink = key === 'link' || key.endsWith('_url');
                        previewHtml += `<div class="preview-field"><span class="preview-label">${label}:</span> `;
                        if (isLink && val) {
                            previewHtml += `<a href="${escapeHtml(String(val))}" target="_blank" class="preview-value preview-link">${escapeHtml(String(val))}</a>`;
                        } else {
                            previewHtml += `<span class="preview-value">${escapeHtml(String(val))}</span>`;
                        }
                        previewHtml += `</div>`;
                    }
                    previewHtml += `</div>`;
                });
                previewHtml += '</div>';
            }
        }
        
        if (!previewHtml) previewHtml = '<p style="color: var(--text-muted); text-align: center; padding: 2rem;">No data to preview.</p>';
        
        document.getElementById('previewModalBody').innerHTML = previewHtml;
        document.getElementById('previewModal').classList.add('active');
        document.body.classList.add('no-scroll');
    } catch (e) {
        alert('Invalid JSON — cannot preview. Fix the syntax first.');
    }
};

window.closePreviewModal = function() {
    document.getElementById('previewModal').classList.remove('active');
    document.body.classList.remove('no-scroll');
};

// Click outside modal to close
document.addEventListener('click', function(e) {
    const modal = document.getElementById('previewModal');
    if (modal && e.target === modal && modal.classList.contains('active')) {
        closePreviewModal();
    }
});

// Escape key closes
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
    }
});

window.addItem = function(category) {
    let template = {};
    let targetArray = isArrayRoot ? currentData : currentData[category];
    
    if (targetArray.length > 0) {
        Object.entries(targetArray[0]).forEach(([k, v]) => {
            if (typeof v === 'boolean') {
                template[k] = true;
            } else {
                template[k] = "";
            }
        });
    } else {
        template = {"title": "", "details": "", "show_personal": true, "show_lab": true};
    }
    targetArray.unshift(template);
    updateAce();
};

// Listen for raw edits in Ace to sync back to visual editor
editor.getSession().on('change', () => {
    if (editor.curOp && editor.curOp.command.name) { // Only sync if user edited
        try {
            JSON.parse(editor.getValue());
            renderForm();
        } catch (e) {}
    }
});

// Init
renderForm();
</script>

    <!-- Preview Modal Overlay -->
    <div class="preview-overlay" id="previewModal">
        <div class="preview-modal">
            <div class="preview-modal-header">
                <h5><i class="fa fa-eye"></i> Data Preview</h5>
                <button class="preview-modal-close" onclick="closePreviewModal()"><i class="fa fa-times"></i> Close</button>
            </div>
            <div class="preview-modal-body" id="previewModalBody">
                <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Loading preview...</p>
            </div>
        </div>
    </div>

    <script src="admin-common.js"></script>
</body>
</html>
