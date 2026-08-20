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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0">Raw JSON Data (Ace Editor)</h5>
                </div>
                <div style="position: relative;">
                    <div id="aceEditor"><?= htmlspecialchars($current_content) ?></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreenAce()" id="fullscreenBtn" title="Toggle Fullscreen" style="position: absolute; bottom: 15px; right: 30px; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                        <i class="fa fa-expand"></i> Fullscreen
                    </button>
                </div>
                <input type="hidden" name="json_content" id="jsonTarget">
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-custom flex-fill shadow-lg"><i class="fa fa-check"></i> Save</button>
                    <button type="button" class="btn btn-custom btn-preview flex-fill shadow-lg" id="previewBtn" onclick="openPreviewModal()"><i class="fa fa-eye"></i> Preview</button>
                    <a href="dashboard.php" class="btn btn-outline-danger flex-fill shadow-lg d-flex align-items-center justify-content-center" style="border-radius: 10px;"><i class="fa fa-times"></i> Exit</a>
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
        
        // Build Section Navigation
        let navHtml = '<div class="d-flex gap-2 mb-3 py-2 overflow-auto" style="white-space: nowrap; border-bottom: 1px solid rgba(255,255,255,0.05); position: sticky; top: 0; z-index: 10; background: var(--glass-bg); backdrop-filter: blur(10px);">';
        for (const category of Object.keys(iterableData)) {
            const categoryTitle = category.charAt(0).toUpperCase() + category.slice(1).replace(/_/g, ' ');
            navHtml += `<a href="#section-${category}" class="badge bg-secondary text-decoration-none p-2 fs-6 text-light opacity-75 hover-opacity-100">${categoryTitle}</a>`;
        }
        navHtml += '</div>';
        visualEditor.innerHTML = navHtml;
        
        for (const [category, items] of Object.entries(iterableData)) {
            const categoryTitle = category.charAt(0).toUpperCase() + category.slice(1).replace(/_/g, ' ');
            
            const isArray = Array.isArray(items);
            const isObject = !isArray && typeof items === 'object' && items !== null;
            
            if (!isArray && !isObject) continue;
            
            const section = document.createElement('div');
            section.className = 'card bg-transparent border-secondary mb-4';
            section.id = `section-${category}`;
            
            let headerHtml = `<div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.03);">
                <h5 class="m-0 text-primary">${categoryTitle}</h5>`;
            if (isArray) {
                headerHtml += `<button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem('${category}')">+ Add New Entry</button>`;
            }
            headerHtml += `</div>`;
            
            section.innerHTML = headerHtml + `<div class="card-body p-2 sortable-list" data-category="${category}"></div>`;
            
            const listContainer = section.querySelector('.sortable-list');
            
            const renderCard = (item, index, canDelete) => {
                const card = document.createElement('div');
                card.className = 'form-card';
                card.setAttribute('data-index', index);
                
                let cardHtml = `
                   <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                       <div class="d-flex align-items-center">
                           ${isArray ? '<span class="drag-handle mr-2"><i class="fas fa-grip-vertical"></i></span>' : ''}
                           <strong>${isArray ? `Entry #${index + 1}` : 'Configuration'}</strong>
                       </div>
                       ${canDelete ? `
                       <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteItem('${category}', ${index})">
                           <i class="fa fa-trash"></i> Delete
                       </button>` : ''}
                   </div>`;
                
                const isPrimitive = typeof item !== 'object' || item === null;
                
                if (isPrimitive) {
                    const isLongText = (typeof item === 'string' && item.length > 60);
                    cardHtml += `<div class="mb-3">`;
                    if (isLongText) {
                        cardHtml += `<textarea class="form-control form-control-sm" rows="3" onchange="updateItem('${category}', ${index}, '__primitive__', this.value)">${escapeHtml(String(item))}</textarea>`;
                    } else {
                        cardHtml += `<input type="text" class="form-control form-control-sm" value="${escapeHtml(String(item))}" onchange="updateItem('${category}', ${index}, '__primitive__', this.value)">`;
                    }
                    cardHtml += `</div>`;
                } else {
                    let boolFields = [];
                    let keys = Object.keys(item);
                    
                    // Preferred sorting order (globally applied, unknown fields go to bottom)
                    const preferredOrder = [
                        'show_in_snackbar',
                        'title',
                        'text',
                        'badge',
                        'badge_color',
                        'date',
                        'link',
                        'name',
                        'subtitle',
                        'email',
                        'passing_year',
                        'thesis',
                        'author',
                        'published_at',
                        'doi',
                        'impact_factor'
                    ];
                    
                    keys.sort((a, b) => {
                        let idxA = preferredOrder.indexOf(a);
                        let idxB = preferredOrder.indexOf(b);
                        if (idxA === -1) idxA = 999;
                        if (idxB === -1) idxB = 999;
                        
                        if (idxA !== idxB) return idxA - idxB;
                        return 0;
                    });

                    for (const key of keys) {
                        const value = item[key];
                        if (typeof value === 'boolean') {
                            boolFields.push({key, value});
                            continue;
                        }
                        
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                        const isLongText = key === 'details' || key === 'description' || (typeof value === 'string' && value.length > 60);
                        
                        cardHtml += `<div class="mb-3"><label class="form-label small fw-bold text-secondary">${label}</label>`;
                        
                        if (key === 'badge_color') {
                            const colors = {
                                "bg-primary": "Blue",
                                "bg-secondary": "Gray",
                                "bg-success": "Green",
                                "bg-danger": "Red",
                                "bg-warning text-dark": "Yellow",
                                "bg-info text-dark": "Cyan",
                                "bg-dark": "Black"
                            };
                            cardHtml += `<select class="form-select form-select-sm" onchange="updateItem('${category}', ${index}, '${key}', this.value)">`;
                            for (const [cls, name] of Object.entries(colors)) {
                                const selected = value === cls ? "selected" : "";
                                cardHtml += `<option value="${cls}" ${selected}>${name}</option>`;
                            }
                            cardHtml += `</select>`;
                        } else if (isLongText) {
                            cardHtml += `<textarea class="form-control form-control-sm" rows="3" onchange="updateItem('${category}', ${index}, '${key}', this.value)">${escapeHtml(value)}</textarea>`;
                        } else {
                            cardHtml += `<input type="text" class="form-control form-control-sm" value="${escapeHtml(value)}" onchange="updateItem('${category}', ${index}, '${key}', this.value)">`;
                        }
                        cardHtml += `</div>`;
                    }

                    if (boolFields.length > 0) {
                        cardHtml += `<div class="mb-2 p-2 rounded d-flex align-items-center gap-3" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                            <label class="form-label small fw-bold text-secondary mb-0">Show at:</label>
                            <div class="d-flex gap-3">`;
                        boolFields.forEach(bf => {
                            const rawLabel = bf.key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                            const displayLabel = rawLabel.replace(/^Show /i, ''); 
                            const id = `chk_${category}_${index}_${bf.key}`;
                            
                            cardHtml += `
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="${id}" 
                                        ${bf.value ? 'checked' : ''} 
                                        onchange="updateItem('${category}', ${index}, '${bf.key}', this.checked)">
                                    <label class="form-check-label small user-select-none pt-1" for="${id}" style="cursor: pointer;">
                                        ${displayLabel}
                                    </label>
                                </div>`;
                        });
                        cardHtml += `</div></div>`;
                    }
                }
                card.innerHTML = cardHtml;
                listContainer.appendChild(card);
            };

            if (isArray) {
                items.forEach((item, index) => renderCard(item, index, true));
                
                // Initialize Sortable for this array category
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
            } else {
                renderCard(items, -1, false);
            }
            
            visualEditor.appendChild(section);
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
        if (key === '__primitive__') {
            currentData[index] = newValue;
        } else {
            currentData[index][key] = newValue;
        }
    } else {
        if (index === -1) {
            currentData[category][key] = newValue;
        } else {
            if (key === '__primitive__') {
                currentData[category][index] = newValue;
            } else {
                currentData[category][index][key] = newValue;
            }
        }
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
                if (typeof item === 'object' && item !== null) {
                    for (const [key, val] of Object.entries(item)) {
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                        previewHtml += `<div class="preview-field"><span class="preview-label">${label}:</span> <span class="preview-value">${escapeHtml(String(val))}</span></div>`;
                    }
                } else {
                    previewHtml += `<div class="preview-field"><span class="preview-value">${escapeHtml(String(item))}</span></div>`;
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
                    if (typeof item === 'object' && item !== null) {
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
                    } else {
                        previewHtml += `<div class="preview-field"><span class="preview-value">${escapeHtml(String(item))}</span></div>`;
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
        if (typeof targetArray[0] === 'string' || typeof targetArray[0] === 'number') {
            template = "";
        } else {
            // Get base template based on file to ensure we don't miss keys if targetArray[0] is incomplete
            const currentFileName = "<?= addslashes($file) ?>";
            if (currentFileName === 'announcements.json') {
                template = {"show_in_snackbar": false, "title": "", "text": "", "link": "", "badge": "", "badge_color": "bg-primary", "date": ""};
            } else if (currentFileName === 'research_group.json') {
                template = {"image": "", "name": "", "subtitle": "", "email": "", "research_area": "", "passing_year": "", "thesis": ""};
            } else if (currentFileName === 'other_responsibilities.json' || currentFileName === 'admin_responsibilities.json') {
                template = {"role": "", "organization": "", "duration": ""};
            } else if (currentFileName === 'awards_honours.json') {
                template = {"title": "", "awardee": "", "event": "", "organization": "", "location": "", "link": ""};
            } else {
                template = {"title": "", "author": "", "published_at": "", "doi": "", "show_personal": true, "show_lab": true};
            }
            
            // Also merge any extra keys that might exist in targetArray[0]
            Object.entries(targetArray[0]).forEach(([k, v]) => {
                if (!(k in template)) {
                    if (typeof v === 'boolean') {
                        template[k] = true;
                    } else {
                        template[k] = "";
                    }
                }
            });
        }
    } else {
        const currentFileName = "<?= addslashes($file) ?>";
        if (currentFileName === 'announcements.json') {
            template = {"show_in_snackbar": false, "title": "", "text": "", "link": "", "badge": "", "badge_color": "bg-primary", "date": ""};
        } else if (currentFileName === 'research_group.json') {
            template = {"image": "", "name": "", "subtitle": "", "email": "", "research_area": "", "passing_year": "", "thesis": ""};
        } else if (currentFileName === 'other_responsibilities.json' || currentFileName === 'admin_responsibilities.json') {
            template = {"role": "", "organization": "", "duration": ""};
        } else if (currentFileName === 'awards_honours.json') {
            template = {"title": "", "awardee": "", "event": "", "organization": "", "location": "", "link": ""};
        } else {
            template = {"title": "", "author": "", "published_at": "", "doi": "", "show_personal": true, "show_lab": true};
        }
    }
    
    // For research group, we want to append to the end. For everything else, prepend to the beginning.
    const currentFileName = "<?= addslashes($file) ?>";
    if (currentFileName === "research_group.json") {
        targetArray.push(template);
    } else {
        targetArray.unshift(template);
    }
    
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
window.toggleFullscreenAce = function() {
    const aceDiv = document.getElementById('aceEditor');
    const btn = document.getElementById('fullscreenBtn');
    if (aceDiv.classList.contains('ace-fullscreen')) {
        aceDiv.classList.remove('ace-fullscreen');
        btn.innerHTML = '<i class="fa fa-expand"></i> Fullscreen';
        btn.style.position = 'absolute';
        btn.style.bottom = '15px';
        btn.style.right = '30px';
        btn.style.top = 'auto';
        btn.style.left = 'auto';
        btn.style.zIndex = '100';
        btn.style.transform = 'none';
        btn.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
        btn.classList.replace('btn-danger', 'btn-outline-secondary');
        document.body.classList.remove('no-scroll');
    } else {
        aceDiv.classList.add('ace-fullscreen');
        btn.innerHTML = '<i class="fa fa-compress"></i> Exit Fullscreen';
        btn.style.position = 'fixed';
        btn.style.bottom = '30px';
        btn.style.right = '30px';
        btn.style.top = 'auto';
        btn.style.left = 'auto';
        btn.style.transform = 'none';
        btn.style.zIndex = '10001';
        btn.classList.replace('btn-outline-secondary', 'btn-danger');
        btn.style.boxShadow = '0 4px 12px rgba(0,0,0,0.5)';
        document.body.classList.add('no-scroll');
    }
    editor.resize();
};

document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") {
        const aceDiv = document.getElementById('aceEditor');
        if (aceDiv && aceDiv.classList.contains('ace-fullscreen')) {
            toggleFullscreenAce();
        }
    }
});
</script>

    <style>
        .ace-fullscreen {
            position: fixed !important;
            top: 0; right: 0; bottom: 0; left: 0;
            width: 100% !important;
            height: 100% !important;
            z-index: 10000;
        }
    </style>

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
