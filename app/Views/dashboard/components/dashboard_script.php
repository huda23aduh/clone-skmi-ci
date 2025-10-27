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

    /* ---------------- Rename Functionality ---------------- */
    const initRenameFunctionality = () => {
        const renameModal = new bootstrap.Modal(document.getElementById('renameModal'));
        const renameForm = document.getElementById('renameForm');
        const renameError = document.getElementById('renameError');
        const confirmRenameBtn = document.getElementById('confirmRename');
        const newNameInput = document.getElementById('newName');
        
        let currentItemId, currentItemType;

        // Handle rename item clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('rename-item') || e.target.closest('.rename-item')) {
                const renameBtn = e.target.classList.contains('rename-item') ? e.target : e.target.closest('.rename-item');
                
                currentItemId = renameBtn.getAttribute('data-item-id');
                currentItemType = renameBtn.getAttribute('data-item-type');
                const currentName = renameBtn.getAttribute('data-item-name');
                
                // Set modal title and input value
                document.getElementById('renameModalLabel').textContent = `Rename ${currentItemType}`;
                newNameInput.value = currentName;
                document.getElementById('renameItemId').value = currentItemId;
                document.getElementById('renameItemType').value = currentItemType;
                
                // Clear previous errors
                renameError.style.display = 'none';
                renameError.textContent = '';
                
                // Show modal
                renameModal.show();
                
                // Focus and select the input text
                setTimeout(() => {
                    newNameInput.focus();
                    newNameInput.select();
                }, 500);
            }
        });

        // Handle rename confirmation
        confirmRenameBtn.addEventListener('click', function() {
            const newName = newNameInput.value.trim();
            
            if (!newName) {
                showRenameError('Please enter a name');
                return;
            }
            
            // Validate file extension for files
            if (currentItemType === 'file') {
                const originalName = newNameInput.defaultValue;
                const originalExt = originalName.split('.').pop();
                const newExt = newName.split('.').pop();
                
                if (originalExt !== newExt) {
                    if (!confirm('Changing the file extension might make the file unusable. Are you sure you want to continue?')) {
                        return;
                    }
                }
            }
            
            performRename(currentItemId, currentItemType, newName);
        });

        // Handle Enter key in rename input
        newNameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmRenameBtn.click();
            }
        });

        // Clear error when modal is hidden
        renameModal._element.addEventListener('hidden.bs.modal', function() {
            renameError.style.display = 'none';
            renameError.textContent = '';
            confirmRenameBtn.disabled = false;
            confirmRenameBtn.innerHTML = 'Rename';
        });

        // Perform the rename via AJAX
        async function performRename(itemId, itemType, newName) {
            const formData = new FormData();
            formData.append('new_name', newName);
            formData.append('item_id', itemId);
            formData.append('item_type', itemType);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            // Show loading state
            confirmRenameBtn.disabled = true;
            confirmRenameBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Renaming...';
            
            const endpoint = itemType === 'file' ? `<?= base_url('/file/rename/') ?>${itemId}` : `<?= base_url('/folder/rename/') ?>${itemId}`;
            
            try {
                const data = await fetchJSON(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (data && data.success) {
                    // Show success message
                    showToast('success', data.message);
                    
                    // Close modal
                    renameModal.hide();
                    
                    // Update the item name in the UI
                    updateItemInUI(itemId, itemType, newName);
                    
                    // Reload the page after a short delay to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showRenameError(data?.message || 'Failed to rename item');
                }
            } catch (error) {
                console.error('Error:', error);
                showRenameError('An error occurred while renaming the item');
            } finally {
                // Reset button state
                confirmRenameBtn.disabled = false;
                confirmRenameBtn.innerHTML = 'Rename';
            }
        }

        // Update item name in UI without full page reload
        function updateItemInUI(itemId, itemType, newName) {
            // Find the item row
            const itemRow = document.querySelector(`.item-row[data-id="${itemId}"]`);
            if (!itemRow) return;
            
            if (itemType === 'file') {
                // Update file name
                const fileNameElement = itemRow.querySelector('.fw-semibold');
                const fileTypeElement = itemRow.querySelector('.text-muted small');
                
                if (fileNameElement) {
                    fileNameElement.textContent = newName;
                }
                
                // Update file extension in badge
                const fileExt = newName.split('.').pop();
                const badgeElement = itemRow.querySelector('.badge');
                if (badgeElement) {
                    badgeElement.textContent = fileExt.toUpperCase();
                }
                
                // Update file type text
                if (fileTypeElement) {
                    fileTypeElement.textContent = `${fileExt.toUpperCase()} file`;
                }
                
                // Update data attributes
                itemRow.setAttribute('data-name', newName);
                
            } else if (itemType === 'folder') {
                // Update folder name
                const folderLink = itemRow.querySelector('a.text-decoration-none');
                if (folderLink) {
                    folderLink.textContent = newName;
                }
                
                // Update data attributes
                itemRow.setAttribute('data-name', newName);
            }
            
            // Update the rename button data attribute
            const renameBtn = document.querySelector(`.rename-item[data-item-id="${itemId}"]`);
            if (renameBtn) {
                renameBtn.setAttribute('data-item-name', newName);
            }
        }

        function showRenameError(message) {
            renameError.textContent = message;
            renameError.style.display = 'block';
            newNameInput.focus();
        }
    };

    // --- View Toggle Logic ---
    const toggleBtn = document.getElementById("toggleViewBtn");
    const listContainer = document.getElementById("listContainer");
    const gridView = document.getElementById("gridView");
    const selectAllCheckbox = document.getElementById("selectAllCheckboxMain");
    const bulkActions = document.getElementById("bulkActions");
    const selectedCount = document.getElementById("selectedCount");

    let currentView = localStorage.getItem("dashboardView") || "list";
    applyView(currentView);

    toggleBtn.addEventListener("click", () => {
        currentView = currentView === "list" ? "grid" : "list";
        localStorage.setItem("dashboardView", currentView);
        applyView(currentView);
    });

    function applyView(view) {
  if (view === "grid") {
    listContainer.style.display = "none";
    gridView.style.display = "flex";
    toggleBtn.innerHTML = '<i class="fas fa-list"></i>';
    toggleBtn.title = "Switch to list view";
  } else {
    listContainer.style.display = "block";
    gridView.style.display = "none";
    toggleBtn.innerHTML = '<i class="fas fa-th-large"></i>';
    toggleBtn.title = "Switch to grid view";
  }
  updateCheckboxListeners();
}

    function updateCheckboxListeners() {
        const itemCheckboxes = document.querySelectorAll(".item-checkbox");

        itemCheckboxes.forEach(cb => {
            cb.addEventListener("change", updateBulkActions);
        });

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener("change", () => {
                itemCheckboxes.forEach(cb => (cb.checked = selectAllCheckbox.checked));
                updateBulkActions();
            });
        }
    }

    function updateBulkActions() {
        const checkedItems = document.querySelectorAll(".item-checkbox:checked");
        const count = checkedItems.length;

        if (count > 0) {
            bulkActions.style.display = "block";
            selectedCount.textContent = `${count} item${count > 1 ? 's' : ''} selected`;
        } else {
            bulkActions.style.display = "none";
        }
    }

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
    
    const initBulkDelete = () => {
        state.bulkDeleteBtn?.addEventListener('click', handleBulkDelete);
    };

    const handleBulkDelete = async () => {
        const items = getSelectedItems();
        if (!items.length) {
            showToast('warning', 'No items selected');
            return;
        }

        try {
            // Get detailed information about what will be deleted
            const deleteInfo = await fetchJSON('<?= base_url('/bulk/delete-info') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ items })
            });

            if (!deleteInfo?.success) {
                showToast('error', 'Failed to get delete information');
                return;
            }

            const { files, folders, total_items } = deleteInfo.counts;
            
            // Create detailed confirmation message
            let confirmMessage = `You are about to move ${total_items} item(s) to trash:\n\n`;
            
            if (files > 0) {
                confirmMessage += `• ${files} file(s)\n`;
            }
            if (folders > 0) {
                confirmMessage += `• ${folders} folder(s) and their contents\n`;
            }
            
            confirmMessage += `\nThis action will move all selected items to the recycle bin. You can restore them later if needed.\n\nAre you sure you want to continue?`;

            if (!confirm(confirmMessage)) {
                return;
            }

            // Show loading state
            state.bulkDeleteBtn.disabled = true;
            const originalText = state.bulkDeleteBtn.innerHTML;
            state.bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';

            // Perform the bulk delete
            const result = await fetchJSON('<?= base_url('/bulk/delete') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ items })
            });

            if (result?.success) {
                showToast('success', result.message);
                
                // Clear selection
                toggleAllCheckboxes(false);
                if (state.selectAllCheckbox) state.selectAllCheckbox.checked = false;
                updateSelectedCount();
                
                // Reload after delay to show the toast
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('error', result?.message || 'Delete failed');
            }

        } catch (error) {
            console.error('Bulk delete error:', error);
            showToast('error', 'Error during bulk delete');
        } finally {
            // Reset button state
            state.bulkDeleteBtn.disabled = false;
            state.bulkDeleteBtn.innerHTML = originalText;
        }
    };

    state.bulkDeleteBtn?.addEventListener('click', handleBulkDelete);
    // state.bulkDeleteBtn?.addEventListener('click', () => bulkAction('<?= base_url('/bulk/delete') ?>', 'Move {count} item(s) to trash?'));
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

    /* ---------------- Preview Functionality ---------------- */
    const initPreviewFunctionality = () => {
        // Handle preview clicks in dropdown menus
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('preview-item') || e.target.closest('.preview-item')) {
                const previewBtn = e.target.classList.contains('preview-item') ? e.target : e.target.closest('.preview-item');
                const fileId = previewBtn.getAttribute('data-file-id');

                if (fileId) {
                    openFilePreview(fileId);
                }
            }
        });

        // Handle direct clicks on file names for preview
        document.addEventListener('click', function(e) {
            // Only trigger preview if the click was inside .file-name-cell
            const fileNameCell = e.target.closest('.file-name-cell');
            if (fileNameCell) {
                const fileRow = fileNameCell.closest('.item-row[data-type="file"]');
                if (fileRow) {
                    const fileId = fileRow.getAttribute('data-id');
                    openFilePreview(fileId);
                }
            }
        });

    };

    const openFilePreview = async (fileId) => {
        try {
            // Show loading
            showToast('info', 'Loading preview...');
            
            // Get file info first to check if preview is available
            const fileInfo = await fetchJSON(`/preview/info/${fileId}`);
            
            if (fileInfo && fileInfo.success) {
                if (fileInfo.file.isPreviewable) {
                    // Open preview in new tab
                    window.open(fileInfo.file.previewUrl, '_blank');
                } else {
                    showToast('warning', 'Preview not available for this file type');
                    // Fallback to download
                    window.open(`/file/download/${fileId}`, '_blank');
                }
            } else {
                showToast('error', 'File not found');
            }
        } catch (error) {
            console.error('Preview error:', error);
            showToast('error', 'Error opening preview');
        }
    };

    /* ---------------- Init ---------------- */
    initStarButtons();
    initRenameFunctionality();
    initPreviewFunctionality();
    initBulkDelete();
    updateCheckboxListeners();
});
</script>