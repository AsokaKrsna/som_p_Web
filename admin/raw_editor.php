<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
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
    $new_content = $_POST['json_content'] ?? '';
    
    // Validate JSON
    json_decode($new_content);
    if (json_last_error() === JSON_ERROR_NONE) {
        // Save the file
        if (file_put_contents($path, $new_content)) {
            $message = "<div class='alert alert-success mt-3'>Successfully updated {$file}!</div>";
        } else {
            $message = "<div class='alert alert-danger mt-3'>Failed to write to {$file}. Check permissions.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger mt-3'>Invalid JSON structure. Changes were not saved.</div>";
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>        <style>
        /* ── Preview Modal ── */
        .preview-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            z-index: 10000; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden; transition: all 0.3s ease; padding: 2rem;
        }
        .preview-overlay.active { opacity: 1; visibility: visible; }
        .preview-modal {
            width: 100%; max-width: 900px; max-height: 85vh; display: flex; flex-direction: column;
            background: var(--glass-bg); backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border); border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            transform: translateY(30px) scale(0.95); transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        .preview-overlay.active .preview-modal { transform: translateY(0) scale(1); }
        .preview-modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .preview-modal-header h5 { margin: 0; font-weight: 700; color: var(--text-main); }
        .preview-modal-close {
            background: none; border: 1px solid rgba(71,85,105,0.15); cursor: pointer;
            border-radius: 8px; padding: 0.35rem 0.85rem; color: var(--text-muted); font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .preview-modal-close:hover {
            background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.2); color: #ef4444;
        }
        .preview-modal-body { flex: 1; overflow-y: auto; padding: 1.5rem; }
        .preview-list { display: flex; flex-direction: column; gap: 0.75rem; }
        .preview-card {
            background: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.5);
            border-radius: 10px; padding: 1rem;
        }
        body.dark-mode .preview-card {
            background: rgba(10,14,26,0.2); border: 1px solid rgba(255,255,255,0.04);
        }
        .preview-card-header {
            font-weight: 700; color: var(--accent-cyan); margin-bottom: 0.5rem;
            padding-bottom: 0.5rem; border-bottom: 1px solid rgba(0,0,0,0.04); font-size: 0.9rem;
        }
        body.dark-mode .preview-card-header { border-bottom: 1px solid rgba(255,255,255,0.04); }
        .preview-field { display: flex; gap: 0.5rem; margin-bottom: 0.35rem; font-size: 0.88rem; }
        .preview-label {
            font-weight: 600; color: var(--text-muted); min-width: 100px; flex-shrink: 0;
        }
        .preview-value { color: var(--text-main); word-break: break-word; }
        .preview-link { color: var(--accent-cyan); }
        .preview-link:hover { color: var(--accent-blue); text-decoration: underline; }

        body { background-color: var(--bg-color); color: var(--text-main); }
        .editor-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            padding: 0.8rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        body.dark-mode .editor-header {
            background: rgba(10, 14, 26, 0.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .editor-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-main);
        }
        .editor-header h4 span {
            color: var(--accent-cyan);
        }
        .editor-header .header-actions {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .editor-container {
            max-width: 1400px;
            margin: 5rem auto 2rem;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--glass-shadow);
        }
        #aceEditor {
            width: 100%;
            height: 70vh;
            border-radius: 10px;
            border: 1px solid var(--glass-border);
        }
        .form-pane {
            height: 70vh;
            overflow-y: auto;
            padding-right: 15px;
        }
        .form-label {
            color: var(--text-main);
            font-weight: 600;
        }
        .form-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: default;
            transition: all 0.2s ease;
        }
        body.dark-mode .form-card {
            background: rgba(10, 14, 26, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
        .form-card:hover {
            border-color: rgba(8, 145, 178, 0.2);
        }
        .drag-handle {
            cursor: grab;
            color: var(--text-muted);
            padding: 5px;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: var(--accent-cyan) !important;
        }
        .editor-container .btn-custom {
            border-radius: 10px;
            padding: 0.65rem 2rem;
            font-weight: 600;
        }
        .card-header {
            background: rgba(255, 255, 255, 0.03) !important;
            border-bottom: 1px solid var(--glass-border);
        }
        body.dark-mode .card-header {
            background: rgba(10, 14, 26, 0.1) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .card-header h5 {
            color: var(--accent-cyan);
            font-weight: 700;
        }
        .card {
            background: transparent !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 12px !important;
            overflow: hidden;
        }
        body.dark-mode .card {
            border: 1px solid rgba(255, 255, 255, 0.04) !important;
        }
        .form-card .form-control {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 8px;
            color: var(--text-main);
        }
        body.dark-mode .form-card .form-control {
            background: rgba(10, 14, 26, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .form-card .form-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
        }
        .dark-mode-toggle-editor {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.1rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .dark-mode-toggle-editor:hover {
            color: var(--accent-cyan);
            background: rgba(8, 145, 178, 0.06);
            transform: scale(1.1);
        }
        .btn-outline-secondary {
            border: 1px solid rgba(71, 85, 105, 0.15);
            color: var(--text-muted);
            background: transparent;
            border-radius: 8px;
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-outline-secondary:hover {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #fff;
        }
        .btn-preview {
            background: rgba(37, 99, 235, 0.06) !important;
            border: 1px solid var(--accent-blue) !important;
            color: var(--accent-blue) !important;
        }
        .btn-preview:hover {
            background: var(--accent-blue) !important;
            border-color: var(--accent-blue) !important;
            color: #fff !important;
        }
        body.dark-mode .btn-preview {
            background: rgba(96, 165, 250, 0.08) !important;
            border: 1px solid var(--accent-blue) !important;
            color: var(--accent-blue) !important;
        }
        body.dark-mode .btn-preview:hover {
            background: var(--accent-blue) !important;
            color: #080c18 !important;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        body.dark-mode .alert-success {
            background: rgba(34, 197, 94, 0.08);
            color: #4ade80;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        body.dark-mode .alert-danger {
            background: rgba(239, 68, 68, 0.08);
            color: #f87171;
        }
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }
        body.dark-mode .alert-warning {
            background: rgba(245, 158, 11, 0.08);
            color: #fbbf24;
        }
    </style>
</head>
<body>
    <header class="editor-header">
        <h4>Editing: <span><?= htmlspecialchars($file) ?></span></h4>
        <div class="header-actions">
            <button class="dark-mode-toggle-editor" id="editorDarkModeToggle" aria-label="Toggle dark mode">
                <i class="fa fa-moon-o" id="editorDarkModeIcon"></i>
            </button>
            <a href="dashboard.php" class="btn-outline-secondary"><i class="fa fa-arrow-left"></i> Dashboard</a>
        </div>
    </header>

    <div class="container-fluid editor-container">

    <?= $message ?? '' ?>

    <form method="POST" id="mainForm">
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
                <input type="hidden" name="json_content" id="jsonTarget">                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-custom flex-fill shadow-lg"><i class="fa fa-check"></i> Save Changes</button>
                            <button type="button" class="btn btn-custom btn-preview flex-fill shadow-lg" id="previewBtn" onclick="openPreviewModal()"><i class="fa fa-eye"></i> Preview</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

<script>
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

function escapeHtml(text) {
    if (text === null || typeof text !== 'string') return '';
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

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
};    // ──────────────────────────────────
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
                        previewHtml += `<div class="preview-field"><span class="preview-label">${label}:</span> <span class="preview-value">${escapeHtmlPreview(String(val))}</span></div>`;
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
                                previewHtml += `<a href="${escapeHtmlPreview(String(val))}" target="_blank" class="preview-value preview-link">${escapeHtmlPreview(String(val))}</a>`;
                            } else {
                                previewHtml += `<span class="preview-value">${escapeHtmlPreview(String(val))}</span>`;
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

    function escapeHtmlPreview(text) {
        if (text === null || typeof text === 'undefined') return '';
        return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

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
        Object.keys(targetArray[0]).forEach(k => template[k] = "");
    } else {
        template = {"title": "", "details": ""};
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

// Dark mode toggle
const editorToggle = document.getElementById('editorDarkModeToggle');
const editorIcon = document.getElementById('editorDarkModeIcon');
if (editorToggle && editorIcon) {
    if (document.body.classList.contains('dark-mode')) {
        editorIcon.className = 'fa fa-sun-o';
    }
    editorToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        editorIcon.className = isDark ? 'fa fa-sun-o' : 'fa fa-moon-o';
    });
}
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
</body>
</html>
