<?php
/**
 * Nucleus - Service Hub Page
 * Version: 1.0.0
 * Description: Centralized service registry with live port detection.
 *   Lists every known service (icon, name, vhost, port), shows live Up/Down
 *   status from ss -tlnp, and provides CRUD management of the registry.
 */

// Load configuration and helpers
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
}

if (file_exists(__DIR__ . '/../includes/helpers.php')) {
    require_once __DIR__ . '/../includes/helpers.php';
}

include __DIR__ . '/../partials/layouts/layoutTop.php'; ?>
<div class="dashboard-main-body">
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <strong><p class="fw-semibold mb-0">Service Hub</p></strong>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Service Hub</li>
        </ul>
    </div>

    <div class="row mb-24">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success-main d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:database-bold" class="icon"></iconify-icon>
                        <span id="svc-total">0</span> services
                    </span>
                    <span class="badge bg-success-subtle text-success-main d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:socket-outline" class="icon"></iconify-icon>
                        <span id="svc-running">0</span> running
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary-main d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:socket-off-outline" class="icon"></iconify-icon>
                        <span id="svc-stopped">0</span> stopped
                    </span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-primary-100 text-primary-600" onclick="loadServiceHub()">
                        <iconify-icon icon="solar:refresh-bold" class="icon"></iconify-icon>
                        Refresh
                    </button>
                    <button type="button" class="btn btn-sm btn-primary text-white" onclick="openServiceModal()">
                        <iconify-icon icon="mdi:plus" class="icon"></iconify-icon>
                        Add Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border radius-12">
        <div class="card-body p-24">
            <div id="service-hub-list" class="hub-list">
                <p class="text-secondary-light text-center py-24 mb-0">Loading services...</p>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Add/Edit Service Modal -->
<div class="modal fade" id="serviceHubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="serviceHubModalTitle">Add Service</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="service-hub-form" class="row g-3">
                    <input type="hidden" name="id" id="svc-id" value="">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="svc-name" placeholder="e.g. ComfyUI" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon (Iconify)</label>
                        <input type="text" class="form-control" name="icon" id="svc-icon" placeholder="simple-icons:comfyui">
                        <div class="form-text">Paste an Iconify icon name (e.g. <code>simple-icons:comfyui</code>).</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Port <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="port" id="svc-port" placeholder="8188" required min="1" max="65535">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SSL Port</label>
                        <input type="number" class="form-control" name="ssl_port" id="svc-ssl_port" placeholder="(optional)" min="1" max="65535">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Color</label>
                        <select class="form-select" name="color" id="svc-color">
                            <option value="primary">Primary</option>
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                            <option value="secondary">Secondary</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Virtual Host (.local)</label>
                        <input type="text" class="form-control" name="vhost" id="svc-vhost" placeholder="comfyui.local">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Schema</label>
                        <select class="form-select" name="schema" id="svc-schema">
                            <option value="https">HTTPS</option>
                            <option value="http">HTTP</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="webui" id="svc-webui" checked>
                            <label class="form-check-label" for="svc-webui">Has Web UI</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="svc-description" placeholder="Short description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveService()">Save Service</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const listEl = document.getElementById('service-hub-list');
    const totEl = document.getElementById('svc-total');
    const runEl = document.getElementById('svc-running');
    const stopEl = document.getElementById('svc-stopped');

    window.loadServiceHub = function () {
        if (!listEl) return;
        listEl.innerHTML = '<tr><td colspan="6" class="text-center py-24 text-secondary-light">Loading services...</td></tr>';
        fetch('api/service_hub.php?action=list')
            .then(r => r.json())
            .then(d => {
                if (!d.success) throw new Error(d.error || 'Failed to load');
                render(d.data || []);
            })
            .catch(e => {
                listEl.innerHTML = '<tr><td colspan="6" class="text-center py-24 text-danger">Error: ' + (e.message || 'Could not load services') + '</td></tr>';
            });
    };

    function esc(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function card(r) {
        const running = r.status === 'running';
        const pill = running
            ? '<span class="pill pill--ok">Running</span>'
            : '<span class="pill pill--idle">Stopped</span>';
        const visit = (r.webui && r.url)
            ? '<a target="_blank" rel="noopener" class="hub-act" href="' + esc(r.url) + '" title="Visit">' +
              '<iconify-icon icon="solar:link-circle-bold"></iconify-icon></a>'
            : '';
        const meta = [];
        if (r.vhost) meta.push('<span class="hub-meta"><iconify-icon icon="solar:global-bold"></iconify-icon>' + esc(r.vhost) + '</span>');
        if (r.port) meta.push('<span class="hub-meta"><iconify-icon icon="solar:socket-outline"></iconify-icon>:' + esc(r.port) + '</span>');
        if (r.processes && r.processes.length) meta.push('<span class="hub-meta">' + esc(r.processes.join(', ')) + '</span>');
        return '' +
            '<article class="hub-card" data-status="' + (running ? 'ok' : 'idle') + '">' +
                '<div class="hub-top">' +
                    '<span class="hub-icon"><iconify-icon icon="' + esc(r.icon) + '"></iconify-icon></span>' +
                    pill +
                '</div>' +
                '<h3 class="hub-name">' + esc(r.name) + '</h3>' +
                (r.description ? '<p class="hub-desc">' + esc(r.description) + '</p>' : '') +
                '<div class="hub-meta-row">' + meta.join('') + '</div>' +
                '<div class="hub-actions">' +
                    visit +
                    '<button type="button" class="hub-act" onclick="openServiceModal(\'' + esc(r.id) + '\')" title="Edit">' +
                        '<iconify-icon icon="solar:pen-bold"></iconify-icon></button>' +
                    '<button type="button" class="hub-act hub-act--danger" onclick="deleteService(\'' + esc(r.id) + '\')" title="Delete">' +
                        '<iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button>' +
                '</div>' +
            '</article>';
    }

    function section(title, rows) {
        if (!rows.length) {
            return '<section class="hub-group"><header><span class="hub-sec">' + title +
                   '</span><span class="hub-count">0</span></header>' +
                   '<p class="text-secondary-light text-sm mb-0">Nothing here.</p></section>';
        }
        return '<section class="hub-group"><header><span class="hub-sec">' + title +
               '</span><span class="hub-count">' + rows.length + '</span></header>' +
               '<div class="hub-grid">' + rows.map(card).join('') + '</div></section>';
    }

    function render(rows) {
        const running = rows.filter(r => r.status === 'running');
        const stopped = rows.filter(r => r.status !== 'running');
        totEl.textContent = rows.length;
        runEl.textContent = running.length;
        stopEl.textContent = stopped.length;

        if (!rows.length) {
            listEl.innerHTML = '<p class="text-secondary-light text-center py-24 mb-0">No services registered yet.</p>';
            return;
        }

        listEl.innerHTML = section('Running', running) + section('Stopped', stopped);
    }

    window.openServiceModal = function (id) {
        const form = document.getElementById('service-hub-form');
        form.reset();
        document.getElementById('svc-webui').checked = true;
        document.getElementById('svc-schema').value = 'https';
        document.getElementById('serviceHubModalTitle').textContent = 'Add Service';
        if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('serviceHubModal')).hide();
        }
        if (id) {
            fetch('api/service_hub.php?action=list')
                .then(r => r.json())
                .then(d => {
                    const row = (d.data || []).find(s => s.id === id);
                    if (!row) return;
                    document.getElementById('svc-id').value = row.id;
                    document.getElementById('svc-name').value = row.name;
                    document.getElementById('svc-icon').value = row.icon || '';
                    document.getElementById('svc-port').value = row.port || '';
                    document.getElementById('svc-ssl_port').value = row.ssl_port || '';
                    document.getElementById('svc-color').value = row.color || 'primary';
                    document.getElementById('svc-vhost').value = row.vhost || '';
                    document.getElementById('svc-schema').value = row.schema || 'https';
                    document.getElementById('svc-webui').checked = !!row.webui;
                    document.getElementById('svc-description').value = row.description || '';
                    document.getElementById('serviceHubModalTitle').textContent = 'Edit Service';
                    openHubModal();
                });
            return;
        }
        openHubModal();
    };

    function openHubModal() {
        if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('serviceHubModal')).show();
        } else {
            // Fallback: simple toggle of .show
            const modal = document.getElementById('serviceHubModal');
            modal.classList.add('show');
            modal.style.display = 'block';
        }
    }

    window.saveService = function () {
        const form = document.getElementById('service-hub-form');
        const fd = new FormData(form);
        const data = {};
        fd.forEach((v, k) => { data[k] = v; });

        fetch('api/service_hub.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.csrfToken || ''
            },
            body: JSON.stringify(data)
        })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    if (typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('serviceHubModal')).hide();
                    }
                    showNotification('Service saved', 'success');
                    loadServiceHub();
                } else {
                    showNotification('Error: ' + (d.error || 'Failed to save service'), 'error');
                }
            })
            .catch(e => showNotification('Error: ' + e.message, 'error'));
    };

    window.deleteService = function (id) {
        if (!confirm('Delete this service from the registry?')) return;
        fetch('api/service_hub.php?action=delete&id=' + encodeURIComponent(id), {
            method: 'POST',
            headers: { 'X-CSRF-Token': window.csrfToken || '' }
        })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    showNotification('Service removed', 'success');
                    loadServiceHub();
                } else {
                    showNotification('Error: ' + (d.error || 'Failed to delete service'), 'error');
                }
            })
            .catch(e => showNotification('Error: ' + e.message, 'error'));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadServiceHub);
    } else {
        loadServiceHub();
    }
})();
</script>
<?php include __DIR__ . '/../partials/layouts/layoutBottom.php'; ?>