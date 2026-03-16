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
    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../style/custom.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        body { background-color: var(--bg-color); color: var(--text-main); }
        .editor-container {
            max-width: 1400px;
            margin: 2rem auto;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 2rem;
        }
        #aceEditor {
            width: 100%;
            height: 70vh;
            border-radius: 8px;
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
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: default;
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
    </style>
</head>
<body>

<div class="container-fluid editor-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Editing: <span class="text-primary"><?= htmlspecialchars($file) ?></span></h4>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

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
                <input type="hidden" name="json_content" id="jsonTarget">
                <button type="submit" class="btn btn-custom w-100 mt-3 shadow-lg">Save Changes</button>
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
};

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
</script>

</body>
</html>
