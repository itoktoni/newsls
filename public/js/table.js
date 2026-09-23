window.currentSortField = window.currentSortField || '';
window.currentSortDir = window.currentSortDir || 'asc';
window.mSelected = window.mSelected || new Set();

window.initTable = function(sortField, sortDir) {
    window.currentSortField = sortField;
    window.currentSortDir = sortDir;
};

window.getModulePath = function() {
    var segments = window.location.pathname.split('/').filter(Boolean);
    segments.pop();
    return '/' + segments.join('/');
};

window.buildUrl = function() {
    var params = new URLSearchParams();
    var q = (document.getElementById('searchInput') || {}).value || '';
    q = typeof q === 'string' ? q.trim() : '';
    var fieldEl = document.getElementById('filterField');
    var field = fieldEl ? fieldEl.value : '';
    var perPage = (document.getElementById('perPage') || {}).value || '25';

    if (q) {
        if (field === 'price') {
            params.set('filters[price][$eq]', q);
        } else {
            params.set('filters[' + field + '][$contains]', q);
        }
        params.set('_field', field);
        params.set('_q', q);
    }

    document.querySelectorAll('[data-field]').forEach(function(input) {
        var fieldName = input.dataset.field;
        var opEl = document.querySelector('[data-op="' + fieldName + '"]');
        var operator = opEl ? opEl.value : '$eq';
        var value = input.tagName === 'SELECT' ? input.value : input.value.trim();
        if (value) {
            params.set('filters[' + fieldName + '][' + operator + ']', value);
            params.set('filter_op[' + fieldName + ']', operator);
        }
    });

    document.querySelectorAll('[data-op]').forEach(function(select) {
        var fieldName = select.dataset.op;
        if (!params.has('filters[' + fieldName + ']')) {
            params.set('filter_op[' + fieldName + ']', select.value);
        }
    });

    if (window.currentSortField) params.set('sort[0]', window.currentSortField + ':' + window.currentSortDir);
    params.set('per_page', perPage);

    window.location.href = window.getModulePath() + '/table?' + params.toString();
};

window.doSort = function(col) {
    if (window.currentSortField === col) {
        window.currentSortDir = window.currentSortDir === 'asc' ? 'desc' : 'asc';
    } else {
        window.currentSortField = col;
        window.currentSortDir = 'asc';
    }
    window.buildUrl();
};

window.updateFilterOp = function(fieldName) {};

window.applyAdvanced = function() {
    var el = document.getElementById('advFilter');
    if (el) el.classList.add('hidden');
    window.buildUrl();
};

window.resetAdvanced = function() {
    document.querySelectorAll('[data-field]').forEach(function(input) { input.value = ''; });
    document.querySelectorAll('[data-op]').forEach(function(select) { select.value = '$eq'; });
    window.applyAdvanced();
};

window.toggleAll = function(el) {
    document.querySelectorAll('tbody input[type="checkbox"]').forEach(function(c) { c.checked = el.checked; });
};

window.mToggle = function(el) {
    var id = el.dataset.id;
    var icon = el.querySelector('[data-check]');
    if (window.mSelected.has(id)) {
        window.mSelected.delete(id);
        el.style.backgroundColor = '';
        if (icon) icon.className = 'icon-[tabler--circle] size-5 text-base-content/20 shrink-0';
    } else {
        window.mSelected.add(id);
        el.style.backgroundColor = 'rgba(0,0,0,0.03)';
        if (icon) icon.className = 'icon-[tabler--circle-check-filled] size-5 text-primary shrink-0';
    }
    window.updateMSel();
};

window.mToggleAll = function() {
    var items = document.querySelectorAll('#mBody > div[data-id]');
    if (window.mSelected.size) {
        window.mSelected.clear();
        items.forEach(function(el) {
            el.style.backgroundColor = '';
            var ic = el.querySelector('[data-check]');
            if (ic) ic.className = 'icon-[tabler--circle] size-5 text-base-content/20 shrink-0';
        });
    } else {
        items.forEach(function(el) {
            window.mSelected.add(el.dataset.id);
            el.style.backgroundColor = 'rgba(0,0,0,0.03)';
            var ic = el.querySelector('[data-check]');
            if (ic) ic.className = 'icon-[tabler--circle-check-filled] size-5 text-primary shrink-0';
        });
    }
    window.updateMSel();
};

window.updateMSel = function() {
    var countEl = document.getElementById('mSelCount');
    var toggleEl = document.getElementById('mToggleAll');
    if (countEl) countEl.textContent = window.mSelected.size ? window.mSelected.size + ' selected' : '';
    if (toggleEl) toggleEl.textContent = window.mSelected.size ? 'Unselect' : 'Select All';
};

window.deleteSelected = function() {
    var desktopIds = Array.from(document.querySelectorAll('tbody input[type="checkbox"]:checked')).map(function(c) { return c.value; });
    var ids = desktopIds.length ? desktopIds : Array.from(window.mSelected);
    if (!ids.length) return alert('No items selected');
    if (!confirm('Delete ' + ids.length + ' item(s)?')) return;
    var form = document.createElement('form');
    form.method = 'POST'; form.action = window.getModulePath() + '/delete';
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) form.innerHTML += '<input type="hidden" name="_token" value="' + csrfMeta.content + '">';
    ids.forEach(function(id) { form.innerHTML += '<input type="hidden" name="ids[]" value="' + id + '">'; });
    document.body.appendChild(form); form.submit();
};
