/* Inline Editing for Admin Users */
(function() {
    'use strict';

    if (!window.__isAdmin) return;

    let activeModal = null;

    // ──────────────────────────────────────────────
    // Open inline editor modal for a section
    // ──────────────────────────────────────────────
    window.openInlineEditor = function(dataFile, sectionId) {
        // Fetch current data
        const modal = document.createElement('div');
        modal.className = 'inline-edit-overlay';
        modal.id = 'inlineEditModal';

        modal.innerHTML = `
            <div class="inline-edit-modal glass-panel">
                <div class="inline-edit-header">
                    <h5>Editing: <span class="text-cyan">${dataFile}</span></h5>
                    <div class="inline-edit-actions">
                        <button class="inline-edit-preview-btn" onclick="previewInlineChanges('${dataFile}', '${sectionId}')">
                            <i class="fa fa-eye"></i> Preview
                        </button>
                        <button class="inline-edit-close-btn" onclick="closeInlineEditor()">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="inline-edit-body">
                    <div class="inline-edit-loading">
                        <i class="fa fa-spinner fa-spin"></i> Loading data...
                    </div>
                </div>
                <div class="inline-edit-footer">
                    <div class="inline-edit-status" id="inlineEditStatus"></div>
                    <button class="btn btn-custom" onclick="saveInlineChanges('${dataFile}', '${sectionId}')">
                        <i class="fa fa-check"></i> Save Changes
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Trigger entrance animation
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });

        activeModal = modal;

        // Load data via AJAX
        fetchInlineData(dataFile, sectionId);
    };

    // ──────────────────────────────────────────────
    // Close inline editor
    // ──────────────────────────────────────────────
    window.closeInlineEditor = function() {
        if (activeModal) {
            activeModal.classList.remove('active');
            setTimeout(() => {
                if (activeModal && activeModal.parentNode) {
                    activeModal.parentNode.removeChild(activeModal);
                }
                activeModal = null;
            }, 300);
        }
    };

    // ──────────────────────────────────────────────
    // Fetch current data file content
    // ──────────────────────────────────────────────
    function fetchInlineData(file, sectionId) {
        const body = document.querySelector('.inline-edit-body');
        if (!body) return;

        fetch(`admin/ajax_fetch.php?file=${encodeURIComponent(file)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderInlineForm(data.content, file, sectionId);
                } else {
                    body.innerHTML = `<div class="alert alert-danger">${data.message || 'Failed to load data'}</div>`;
                }
            })
            .catch(err => {
                body.innerHTML = `<div class="alert alert-danger">Network error: ${err.message}</div>`;
            });
    }

    // ──────────────────────────────────────────────
    // Render the inline form
    // ──────────────────────────────────────────────
    function renderInlineForm(jsonStr, file, sectionId) {
        const body = document.querySelector('.inline-edit-body');
        if (!body) return;

        let data;
        try {
            data = JSON.parse(jsonStr);
        } catch (e) {
            body.innerHTML = `<div class="alert alert-danger">Invalid JSON data file</div>`;
            return;
        }

        // Store original for cancel
        body.setAttribute('data-original', jsonStr);
        body.setAttribute('data-file', file);

        // Build the form
        let html = '';
        const isArray = Array.isArray(data);

        if (isArray) {
            html += buildArrayForm(data, 'root');
        } else {
            for (const [key, items] of Object.entries(data)) {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                if (Array.isArray(items)) {
                    html += `<div class="inline-section-group">
                        <h6 class="inline-group-title">${label}</h6>
                        <div class="inline-items" data-category="${key}">`;
                    items.forEach((item, idx) => {
                        html += buildItemCard(item, key, idx);
                    });
                    html += `</div>
                        <button class="inline-add-btn" onclick="addInlineItem('${key}', '${file}', '${sectionId}')">
                            <i class="fa fa-plus"></i> Add ${label.slice(0, -1) || 'Entry'}
                        </button>
                    </div>`;
                }
            }
        }

        body.innerHTML = html || '<p class="text-muted">No editable fields found.</p>';

        // Make items sortable
        document.querySelectorAll('.inline-items').forEach(container => {
            makeSortable(container, file, sectionId);
        });
    }

    // ──────────────────────────────────────────────
    // Build an array-based form
    // ──────────────────────────────────────────────
    function buildArrayForm(arr, category) {
        let html = `<div class="inline-section-group">
            <div class="inline-items" data-category="${category}">`;
        arr.forEach((item, idx) => {
            html += buildItemCard(item, category, idx);
        });
        html += `</div>
            <button class="inline-add-btn" onclick="addArrayItem('${file}', '${sectionId}')">
                <i class="fa fa-plus"></i> Add Entry
            </button>
        </div>`;
        return html;
    }

    // ──────────────────────────────────────────────
    // Build a single item card
    // ──────────────────────────────────────────────
    function buildItemCard(item, category, index) {
        let html = `<div class="inline-item-card" data-index="${index}">
            <div class="inline-item-header">
                <span class="inline-drag-handle"><i class="fa fa-grip-vertical"></i></span>
                <strong>Entry #${index + 1}</strong>
                <button class="inline-delete-btn" onclick="deleteInlineItem(this, '${category}', ${index})">
                    <i class="fa fa-trash-o"></i>
                </button>
            </div>`;

        for (const [key, value] of Object.entries(item)) {
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            const isLong = key === 'details' || key === 'description' || (typeof value === 'string' && value.length > 60);
            const fieldName = `item_${category}_${index}_${key}`;

            html += `<div class="inline-field">
                <label class="inline-field-label">${label}</label>`;

            if (isLong) {
                html += `<textarea class="inline-input" name="${fieldName}" rows="3">${escapeHtml(value || '')}</textarea>`;
            } else {
                html += `<input type="text" class="inline-input" name="${fieldName}" value="${escapeHtml(value || '')}">`;
            }

            html += `</div>`;
        }

        html += `</div>`;
        return html;
    }

    // ──────────────────────────────────────────────
    // Collect form data into JSON
    // ──────────────────────────────────────────────
    function collectFormData() {
        const body = document.querySelector('.inline-edit-body');
        if (!body) return null;

        const file = body.getAttribute('data-file');
        const original = body.getAttribute('data-original');
        let data;
        try { data = JSON.parse(original); } catch(e) { return null; }

        const isArray = Array.isArray(data);

        if (isArray) {
            const items = body.querySelectorAll('.inline-item-card');
            data = Array.from(items).map(card => {
                const obj = {};
                card.querySelectorAll('.inline-input').forEach(input => {
                    const name = input.getAttribute('name');
                    const key = name.substring(name.lastIndexOf('_') + 1);
                    obj[key] = input.value;
                });
                return obj;
            });
        } else {
            body.querySelectorAll('.inline-section-group').forEach(group => {
                const category = group.querySelector('.inline-items')?.getAttribute('data-category');
                if (!category || !data[category]) return;

                const items = group.querySelectorAll('.inline-item-card');
                data[category] = Array.from(items).map(card => {
                    const obj = {};
                    card.querySelectorAll('.inline-input').forEach(input => {
                        const name = input.getAttribute('name');
                        const key = name.substring(name.lastIndexOf('_') + 1);
                        obj[key] = input.value;
                    });
                    return obj;
                });
            });
        }

        return data;
    }

    // ──────────────────────────────────────────────
    // Save changes via AJAX
    // ──────────────────────────────────────────────
    window.saveInlineChanges = function(file, sectionId) {
        const data = collectFormData();
        if (!data) {
            showStatus('Error collecting form data', 'error');
            return;
        }

        const jsonStr = JSON.stringify(data, null, 4);
        const status = document.getElementById('inlineEditStatus');
        if (status) {
            status.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
            status.className = 'inline-edit-status saving';
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('content', jsonStr);

        fetch('admin/ajax_save.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(result => {
            if (result.success) {
                showStatus('<i class="fa fa-check-circle"></i> ' + result.message, 'success');
                // Reload the section after a short delay
                setTimeout(() => {
                    if (sectionId) {
                        // Trigger a soft reload of the section
                        const section = document.getElementById(sectionId);
                        if (section) {
                            // Reload the page to reflect changes
                            window.location.reload();
                        }
                    }
                }, 800);
            } else {
                showStatus('<i class="fa fa-exclamation-circle"></i> ' + result.message, 'error');
            }
        })
        .catch(err => {
            showStatus('<i class="fa fa-exclamation-circle"></i> Network error: ' + err.message, 'error');
        });
    };

    // ──────────────────────────────────────────────
    // Preview changes without saving
    // ──────────────────────────────────────────────
    window.previewInlineChanges = function(file, sectionId) {
        const data = collectFormData();
        if (!data) return;

        const jsonStr = JSON.stringify(data, null, 4);

        // Open preview in a new window
        const w = window.open('', 'inlinePreview', 'width=1000,height=800,scrollbars=yes');
        if (w) {
            w.document.write(`
                <html><head>
                <title>Preview: ${file}</title>
                <link href="../css/bootstrap.min.css" rel="stylesheet">
                <link href="../style/custom.css" rel="stylesheet">
                <style>
                    body { padding: 2rem; background: var(--bg-color); }
                    pre { background: rgba(0,0,0,0.03); padding: 1rem; border-radius: 8px; overflow-x: auto; }
                </style>
                </head><body>
                <div class="container">
                    <h4 class="mb-4">Preview: <span class="text-primary">${file}</span></h4>
                    <div class="alert alert-info">This is a preview of the raw JSON data. Changes are NOT saved yet.</div>
                    <pre>${escapeHtml(jsonStr)}</pre>
                    <hr>
                    <p class="text-muted small">Close this window to return to the editor.</p>
                </div>
                </body></html>
            `);
            w.document.close();
        }
    };

    // ──────────────────────────────────────────────
    // Add item (array root)
    // ──────────────────────────────────────────────
    window.addArrayItem = function(file, sectionId) {
        const body = document.querySelector('.inline-edit-body');
        const original = body?.getAttribute('data-original');
        if (!original) return;
        let data;
        try { data = JSON.parse(original); } catch(e) { return; }

        // Create template from first item
        let template = {};
        if (data.length > 0) {
            Object.keys(data[0]).forEach(k => template[k] = '');
        } else {
            template = { title: '', details: '' };
        }
        data.unshift(template);
        body.setAttribute('data-original', JSON.stringify(data));
        renderInlineForm(JSON.stringify(data), file, sectionId);
    };

    // ──────────────────────────────────────────────
    // Add item to a category
    // ──────────────────────────────────────────────
    window.addInlineItem = function(category, file, sectionId) {
        const body = document.querySelector('.inline-edit-body');
        const original = body?.getAttribute('data-original');
        if (!original) return;
        let data;
        try { data = JSON.parse(original); } catch(e) { return; }

        if (!data[category] || !Array.isArray(data[category])) {
            data[category] = [];
        }

        let template = {};
        if (data[category].length > 0) {
            Object.keys(data[category][0]).forEach(k => template[k] = '');
        } else {
            template = { title: '', details: '' };
        }
        data[category].unshift(template);
        body.setAttribute('data-original', JSON.stringify(data));
        renderInlineForm(JSON.stringify(data), file, sectionId);
    };

    // ──────────────────────────────────────────────
    // Delete item
    // ──────────────────────────────────────────────
    window.deleteInlineItem = function(btn, category, index) {
        if (!confirm('Delete this entry permanently?')) return;

        const card = btn.closest('.inline-item-card');
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                const body = document.querySelector('.inline-edit-body');
                const original = body?.getAttribute('data-original');
                if (!original) return;
                let data;
                try { data = JSON.parse(original); } catch(e) { return; }

                const isArray = Array.isArray(data);
                if (isArray) {
                    data.splice(index, 1);
                } else if (data[category]) {
                    data[category].splice(index, 1);
                }

                body.setAttribute('data-original', JSON.stringify(data));
                renderInlineForm(JSON.stringify(data), body.getAttribute('data-file'), '');
            }, 300);
        }
    };

    // ──────────────────────────────────────────────
    // Show status message
    // ──────────────────────────────────────────────
    function showStatus(msg, type) {
        const status = document.getElementById('inlineEditStatus');
        if (status) {
            status.innerHTML = msg;
            status.className = 'inline-edit-status ' + type;
        }
    }

    // ──────────────────────────────────────────────
    // Simple drag-to-reorder (click-based: move up/down)
    // ──────────────────────────────────────────────
    function makeSortable(container, file, sectionId) {
        // For simplicity, add move up/down buttons via CSS pseudo-elements
        // Full Sortable integration would require additional library
    }

    // ──────────────────────────────────────────────
    // Escape HTML
    // ──────────────────────────────────────────────
    function escapeHtml(text) {
        if (text === null || typeof text === 'undefined') return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ──────────────────────────────────────────────
    // Keyboard: Escape closes modal
    // ──────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeInlineEditor();
        }
    });

    // ──────────────────────────────────────────────
    // Click outside modal closes it
    // ──────────────────────────────────────────────
    document.addEventListener('click', function(e) {
        if (activeModal && e.target === activeModal) {
            closeInlineEditor();
        }
    });

})();
