<?php
/**
 * Nucleus - Service Hub Page
 * Version: 2.0.0
 * Description: Centralized service registry as a live card grid.
 *   Every engine gets its own https://name.local link, live Up/Down
 *   status from ss -tlnp, search/filter, and CRUD management.
 */

// Load configuration and helpers
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
}

if (file_exists(__DIR__ . '/../includes/helpers.php')) {
    require_once __DIR__ . '/../includes/helpers.php';
}

include __DIR__ . '/../partials/layouts/layoutTop.php'; ?>
<style>
    @keyframes svcPulse{0%{box-shadow:0 0 0 0 rgba(39,174,96,.45)}70%{box-shadow:0 0 0 7px rgba(39,174,96,0)}100%{box-shadow:0 0 0 0 rgba(39,174,96,0)}}
    .svc-dot{width:8px;height:8px;border-radius:50%;display:inline-block}
    .svc-dot-running{background:#27ae60;animation:svcPulse 2s infinite}
    .svc-dot-stopped{background:#9aa5b8}
    .svc-card{transition:transform .15s ease, box-shadow .15s ease}
    .svc-card:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(16,24,40,.08)}
    .svc-stopped .card-body{opacity:.72}
    .svc-url{display:inline-flex;align-items:center;gap:5px;font-size:.82rem;font-weight:600;color:#3d6ce0;text-decoration:none;word-break:break-all}
    .svc-url:hover{text-decoration:underline;color:#1f47b5}
    .svc-chip{cursor:pointer;user-select:none}
    .svc-chip.active{font-weight:700}
</style>
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

    <!-- Filter toolbar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2" id="svc-filters">
            <button type="button" class="btn btn-sm svc-chip btn-primary text-white active" data-filter="all">All</button>
            <button type="button" class="btn btn-sm svc-chip btn-outline-success" data-filter="running">
                <span class="svc-dot svc-dot-running me-1"></span>Running
            </button>
            <button type="button" class="btn btn-sm svc-chip btn-outline-secondary" data-filter="stopped">
                <span class="svc-dot svc-dot-stopped me-1"></span>Offline
            </button>
        </div>
        <input type="search" id="svc-search" class="form-control w-auto min-w-200-px" placeholder="Search name, url, description...">
    </div>

    <!-- Card grid -->
    <div id="service-hub-list" class="row g-3">
        <div class="col-12 text-center py-24 text-secondary-light">Loading services...</div>
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
                        <div class="form-text">Bare hostname — served over HTTPS by the shared wildcard cert.</div>
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
    const searchEl = document.getElementById('svc-search');

    let svcRows = [];
    let svcFilter = 'all';

    window.loadServiceHub = function () {
        if (!listEl) return;
        listEl.innerHTML = '<div class="col-12 text-center py-24 text-secondary-light">Loading services...</div>';
        fetch('api/service_hub.php?action=list')
            .then(r => r.json())
            .then(d => {
                if (!d.success) throw new Error(d.error || 'Failed to load');
                svcRows = d.data || [];
                render();
            })
            .catch(e => {
                listEl.innerHTML = '<div class="col-12 text-center py-24 text-danger">Error: ' + (e.message || 'Could not load services') + '</div>';
            });
    };

    function visibleRows() {
        const q = (searchEl && searchEl.value ? searchEl.value : '').toLowerCase().trim();
        return svcRows.filter(r => {
            if (svcFilter === 'running' && r.status !== 'running') return false;
            if (svcFilter === 'stopped' && r.status !== 'stopped') return false;
            if (!q) return true;
            const hay = ((r.name || '') + ' ' + (r.vhost || '') + ' ' + (r.description || '')).toLowerCase();
            return hay.includes(q);
        });
    }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function render() {
        const running = svcRows.filter(r => r.status === 'running').length;
        totEl.textContent = svcRows.length;
        runEl.textContent = running;
        stopEl.textContent = svcRows.length - running;

        const rows = visibleRows();
        if (!rows.length) {
            listEl.innerHTML = '<div class="col-12 text-center py-24 text-secondary-light">No services match.</div>';
            return;
        }

        listEl.innerHTML = rows.map(r => {
            const color = ['info', 'success', 'warning', 'danger', 'secondary'].includes(r.color) ? r.color : 'primary';
            const runningState = r.status === 'running';
            const pill = runningState
                ? '<span class="badge bg-success-subtle text-success-main d-inline-flex align-items-center gap-1"><span class="svc-dot svc-dot-running"></span> Running</span>'
                : '<span class="badge bg-secondary-subtle text-secondary-main d-inline-flex align-items-center gap-1"><span class="svc-dot svc-dot-stopped"></span> Offline</span>';

            let linkCell;
            if (r.url && r.webui) {
                linkCell = '<a class="svc-url" target="_blank" rel="noopener" href="' + esc(r.url) + '" title="' + esc(r.url) + '">' +
                           '<iconify-icon icon="solar:link-circle-bold" class="icon"></iconify-icon>' + esc((r.schema || 'https') + '://' + (r.vhost || '')) + '</a>';
            } else if (r.vhost) {
                linkCell = '<span class="svc-url" style="color:inherit;opacity:.65">' + esc(r.vhost) + ' <span class="text-xs">(API)</span></span>';
            } else {
                linkCell = '<code class="text-xs">127.0.0.1:' + esc(r.port) + '</code>';
            }

            const visitBtn = (r.webui && r.url)
                ? '<a target="_blank" rel="noopener" class="btn btn-sm btn-primary-100 text-primary-600" href="' + esc(r.url) + '" title="Open ' + esc(r.name) + '"><iconify-icon icon="solar:link-circle-bold" class="icon"></iconify-icon> Open</a>'
                : '';
            const copyBtn = r.url
                ? '<button type="button" class="btn btn-sm btn-secondary-light text-secondary" title="Copy URL" onclick="copySvcUrl(\'' + esc(r.id) + '\')"><iconify-icon icon="solar:copy-bold" class="icon"></iconify-icon></button>'
                : '';
            const proc = (r.processes && r.processes.length) ? esc(r.processes.join(', ')) : '';

            return (
                '<div class="col-12 col-sm-6 col-xl-4">' +
                    '<div class="card shadow-none border radius-12 h-100 svc-card' + (runningState ? '' : ' svc-stopped') + '">' +
                        '<div class="card-body p-3 d-flex flex-column gap-2">' +
                            '<div class="d-flex align-items-start justify-content-between gap-2">' +
                                '<span class="w-44-px h-44-px rounded-circle bg-' + color + '-100 text-' + color + '-600 d-flex justify-content-center align-items-center flex-shrink-0">' +
                                    '<iconify-icon icon="' + esc(r.icon || 'tabler:flame') + '" class="text-lg"></iconify-icon>' +
                                '</span>' +
                                pill +
                            '</div>' +
                            '<div>' +
                                '<p class="fw-semibold mb-0">' + esc(r.name) + '</p>' +
                                (r.description ? '<p class="text-xs text-secondary-light mb-0">' + esc(r.description) + '</p>' : '') +
                                (proc ? '<p class="text-xs text-secondary-light mb-0 mt-1"><iconify-icon icon="tabler:cpu" class="icon"></iconify-icon> ' + proc + '</p>' : '') +
                            '</div>' +
                            '<div class="mt-auto d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">' +
                                linkCell +
                                '<div class="d-flex flex-wrap gap-1">' +
                                    visitBtn + copyBtn +
                                    '<button type="button" class="btn btn-sm btn-primary-100 text-primary-600" title="Edit" onclick="openServiceModal(\'' + esc(r.id) + '\')"><iconify-icon icon="solar:pen-bold" class="icon"></iconify-icon></button>' +
                                    '<button type="button" class="btn btn-sm btn-danger-100 text-danger-600" title="Remove" onclick="deleteService(\'' + esc(r.id) + '\')"><iconify-icon icon="solar:trash-bin-trash-bold" class="icon"></iconify-icon></button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }).join('');
    }

    window.copySvcUrl = function (id) {
        const r = svcRows.find(x => x.id === id);
        if (!r || !r.url) return;
        const done = () => showNotification('Copied ' + r.url, 'success');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(r.url).then(done).catch(() => {});
        } else {
            const ta = document.createElement('textarea');
            ta.value = r.url; document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); document.body.removeChild(ta); done();
        }
    };

    document.querySelectorAll('#svc-filters .svc-chip').forEach(btn => {
        btn.addEventListener('click', () => {
            svcFilter = btn.dataset.filter || 'all';
            document.querySelectorAll('#svc-filters .svc-chip').forEach(b => {
                b.classList.remove('btn-primary', 'text-white', 'active');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.add('active');
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary', 'text-white');
            render();
        });
    });
    if (searchEl) searchEl.addEventListener('input', render);

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
