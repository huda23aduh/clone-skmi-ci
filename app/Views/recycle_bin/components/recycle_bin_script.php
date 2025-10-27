<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllCheckboxMain');
    const checkboxes = document.querySelectorAll('.selectItemCheckbox');
    const restoreBtn = document.getElementById('restoreSelectedBtn');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const bulkActionsInfo = document.getElementById('bulkActionsInfo');
    const selectedItemsCount = document.getElementById('selectedItemsCount');

    // Get selected items
    function getSelectedItems() {
        return Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => ({
                id: cb.getAttribute('data-id'),
                type: cb.getAttribute('data-type')
            }));
    }

    // Update selected count and UI
    function updateSelectedUI() {
        const selectedItems = getSelectedItems();
        const count = selectedItems.length;
        
        selectedItemsCount.textContent = count;
        
        if (count > 0) {
            bulkActionsInfo.style.display = 'block';
            restoreBtn.disabled = false;
            deleteBtn.disabled = false;
        } else {
            bulkActionsInfo.style.display = 'none';
            restoreBtn.disabled = true;
            deleteBtn.disabled = true;
        }
        
        // Update select all checkbox
        selectAll.checked = count > 0 && count === checkboxes.length;
        selectAll.indeterminate = count > 0 && count < checkboxes.length;
    }

    // Select All
    selectAll.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedUI();
    });

    // Individual checkbox changes
    checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedUI));

    // Bulk Restore
    restoreBtn.addEventListener('click', async () => {
        const selectedItems = getSelectedItems();
        if (selectedItems.length === 0) return;

        const confirmMessage = `Restore ${selectedItems.length} item(s) from recycle bin?`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            // Show loading
            restoreBtn.disabled = true;
            restoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Restoring...';

            const response = await fetch('<?= base_url('/bulk/restore') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ items: selectedItems })
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', result.message);
                restoreBtn.disabled = false;
                restoreBtn.innerHTML = '<i class="fas fa-undo-alt me-1"></i> Restore Selected';
            }

        } catch (error) {
            console.error('Restore error:', error);
            showToast('error', 'Error during restore operation');
            restoreBtn.disabled = false;
            restoreBtn.innerHTML = '<i class="fas fa-undo-alt me-1"></i> Restore Selected';
        }
    });

    // Bulk Delete Permanently
    deleteBtn.addEventListener('click', async () => {
        const selectedItems = getSelectedItems();
        if (selectedItems.length === 0) return;

        const confirmMessage = `Permanently delete ${selectedItems.length} item(s)? This action cannot be undone!`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            // Show loading
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';

            const response = await fetch('<?= base_url('/bulk/purge') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ items: selectedItems })
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', result.message);
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Delete Permanently';
            }

        } catch (error) {
            console.error('Delete error:', error);
            showToast('error', 'Error during delete operation');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Delete Permanently';
        }
    });

    // Single item restore
    document.querySelectorAll('.restore-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemType = this.getAttribute('data-type');
            const itemName = this.getAttribute('data-name');

            if (confirm(`Restore ${itemType} "${itemName}"?`)) {
                restoreSingleItem(itemId, itemType);
            }
        });
    });

    // Single item delete
    document.querySelectorAll('.delete-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemType = this.getAttribute('data-type');
            const itemName = this.getAttribute('data-name');

            if (confirm(`Permanently delete ${itemType} "${itemName}"? This cannot be undone!`)) {
                deleteSingleItem(itemId, itemType);
            }
        });
    });

    // Restore single item
    async function restoreSingleItem(itemId, itemType) {
        try {
            const response = await fetch('<?= base_url('/bulk/restore') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ 
                    items: [{ id: itemId, type: itemType }] 
                })
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', result.message);
            }

        } catch (error) {
            console.error('Single restore error:', error);
            showToast('error', 'Error during restore operation');
        }
    }

    // Delete single item
    async function deleteSingleItem(itemId, itemType) {
        try {
            const response = await fetch('<?= base_url('/bulk/purge') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ 
                    items: [{ id: itemId, type: itemType }] 
                })
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', result.message);
            }

        } catch (error) {
            console.error('Single delete error:', error);
            showToast('error', 'Error during delete operation');
        }
    }

    // Search filter
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        document.querySelectorAll('#recycleTable tbody tr').forEach(row => {
            const itemName = row.querySelector('.item-name').textContent.toLowerCase();
            row.style.display = itemName.includes(searchTerm) ? '' : 'none';
        });
    });

    // Type filter
    typeFilter.addEventListener('change', () => {
        const type = typeFilter.value;
        document.querySelectorAll('#recycleTable tbody tr').forEach(row => {
            if (type === 'all' || row.dataset.type === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Toast notification function
    function showToast(type, message) {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }

    // Initialize UI
    updateSelectedUI();
});
</script>