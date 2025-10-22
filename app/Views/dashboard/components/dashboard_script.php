<script>
document.addEventListener('DOMContentLoaded', () => {
    const state = {
        searchInput: document.getElementById('searchInput'),
        typeFilter: document.getElementById('typeFilter'),
        sortBy: document.getElementById('sortBy'),
        selectAllCheckbox: document.getElementById('selectAllCheckboxMain'),
        itemCheckboxes: document.querySelectorAll('.item-checkbox'),
        bulkActions: document.getElementById('bulkActions'),
        selectAllBtn: document.getElementById('selectAllBtn'),
        clearSelectionBtn: document.getElementById('clearSelectionBtn'),
        bulkDeleteBtn: document.getElementById('bulkDeleteBtn'),
        bulkZipBtn: document.getElementById('bulkZipBtn'),
        selectedCount: document.getElementById('selectedCount'),
        contentTable: document.getElementById('contentTable')
    };

    /* ---------------- Helper Functions ---------------- */
    const showToast = (type, message) => {
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        const toastContainer = document.getElementById('toastContainer');
        if (toastContainer) {
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            new bootstrap.Toast(document.getElementById(toastId)).show();
        }
    };

    const fetchJSON = async (url, options = {}) => {
        try {
            const res = await fetch(url, options);
            return await res.json();
        } catch (err) {
            console.error(err);
            showToast('error', 'Network error');
            return null;
        }
    };

    const getSelectedItems = () => Array.from(state.itemCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => ({ id: cb.value, type: cb.dataset.type }));

    /* ---------------- Folder / File Operations ---------------- */
    window.uploadToFolder = folderId => showUploadFileModal(folderId);
    window.createSubfolder = parentId => showCreateFolderModal(parentId);

    /* ---------------- Star Buttons ---------------- */
    const initStarButtons = () => {
        document.querySelectorAll('.star-btn').forEach(btn => {
            const { itemId, itemType } = btn.dataset;
            updateStarStatus(itemId, itemType, btn);
            btn.addEventListener('click', () => toggleStar(itemId, itemType, btn));
        });
    };

    const updateStarStatus = async (id, type, button) => {
        const data = await fetchJSON(`<?= base_url('/starred/check') ?>?item_id=${id}&item_type=${type}`);
        if (!data) return;
        button.innerHTML = data.is_starred ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
        button.classList.toggle('active', data.is_starred);
    };

    const toggleStar = async (id, type, button) => {
        const data = await fetchJSON('<?= base_url('/starred/toggle') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ item_id: id, item_type: type })
        });
        if (!data) return;
        button.innerHTML = data.is_starred ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
        button.classList.toggle('active', data.is_starred);
        showToast(data.is_starred ? 'success' : 'warning', data.message);
    };

    /* ---------------- Selection / Bulk Actions ---------------- */
    const updateSelectedCount = () => {
        const count = getSelectedItems().length;
        state.selectedCount.textContent = `${count} item${count !== 1 ? 's' : ''} selected`;
        state.bulkActions.style.display = count > 0 ? 'block' : 'none';
    };

    const toggleAllCheckboxes = checked => state.itemCheckboxes.forEach(cb => cb.checked = checked);

    state.selectAllCheckbox?.addEventListener('change', e => {
        toggleAllCheckboxes(e.target.checked);
        updateSelectedCount();
    });

    state.itemCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));
    state.selectAllBtn?.addEventListener('click', () => {
        toggleAllCheckboxes(true);
        if (state.selectAllCheckbox) state.selectAllCheckbox.checked = true;
        updateSelectedCount();
    });
    state.clearSelectionBtn?.addEventListener('click', () => {
        toggleAllCheckboxes(false);
        if (state.selectAllCheckbox) state.selectAllCheckbox.checked = false;
        updateSelectedCount();
    });

    const bulkAction = async (url, confirmMsg) => {
        const items = getSelectedItems();
        if (!items.length) return showToast('warning', 'No items selected');
        if (!confirm(confirmMsg.replace('{count}', items.length))) return;

        const data = await fetchJSON(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ items })
        });
        if (data?.success) {
            showToast('success', data.message || 'Action completed');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data?.message || 'Action failed');
        }
    };

    state.bulkDeleteBtn?.addEventListener('click', () => bulkAction('<?= base_url('/bulk/delete') ?>', 'Move {count} item(s) to trash?'));
    state.bulkZipBtn?.addEventListener('click', () => bulkAction('<?= base_url('/file/compress') ?>', 'Compress {count} item(s) into a ZIP file?'));

    /* ---------------- Filter & Sort ---------------- */
    const filterAndSort = () => {
        const rows = Array.from(document.querySelectorAll('.item-row'));
        const search = state.searchInput.value.toLowerCase();
        const type = state.typeFilter.value;
        const sort = state.sortBy.value;

        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const rowType = row.dataset.type;
            row.style.display = (name.includes(search) && (type === 'all' || type === rowType)) ? '' : 'none';
        });

        const visibleRows = rows.filter(row => row.style.display !== 'none');
        visibleRows.sort((a, b) => {
            switch (sort) {
                case 'name_asc': return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc': return b.dataset.name.localeCompare(a.dataset.name);
                case 'date_asc': return new Date(a.dataset.date) - new Date(b.dataset.date);
                case 'date_desc': return new Date(b.dataset.date) - new Date(a.dataset.date);
                case 'size_asc': return (parseInt(a.dataset.size) || 0) - (parseInt(b.dataset.size) || 0);
                case 'size_desc': return (parseInt(b.dataset.size) || 0) - (parseInt(a.dataset.size) || 0);
                default: return 0;
            }
        });

        visibleRows.forEach(row => state.contentTable.appendChild(row));
    };

    state.searchInput?.addEventListener('input', filterAndSort);
    state.typeFilter?.addEventListener('change', filterAndSort);
    state.sortBy?.addEventListener('change', filterAndSort);

    /* ---------------- Init ---------------- */
    initStarButtons();
});
</script>
