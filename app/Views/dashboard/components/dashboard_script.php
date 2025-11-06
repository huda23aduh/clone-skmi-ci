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

    /* ---------------- Share Link Functionality ---------------- */
    const initShareLinkFunctionality = () => {
        const shareModalElement = document.getElementById('shareLinkModal');
        const shareModal = new bootstrap.Modal(shareModalElement);
        const shareItemName = document.getElementById('shareItemName');
        const shareLink = document.getElementById('shareLink');
        const copyShareLinkBtn = document.getElementById('copyShareLinkBtn');
        const generateShareLinkBtn = document.getElementById('generateShareLinkBtn');
        const enablePasswordProtection = document.getElementById('enablePasswordProtection');
        const passwordSection = document.getElementById('passwordSection');
        const sharePassword = document.getElementById('sharePassword');
        const confirmSharePassword = document.getElementById('confirmSharePassword');
        
        let currentItemId, currentItemType, currentItemName;

        // Handle share link clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('share-link-item') || e.target.closest('.share-link-item')) {
                const shareBtn = e.target.classList.contains('share-link-item') ? e.target : e.target.closest('.share-link-item');
                
                currentItemId = shareBtn.getAttribute('data-item-id');
                currentItemType = shareBtn.getAttribute('data-item-type');
                currentItemName = shareBtn.getAttribute('data-item-name');
                
                // Set modal content
                shareItemName.value = currentItemName;
                shareLink.value = 'Click "Generate Link" to create shareable link';
                
                // Reset form
                enablePasswordProtection.checked = false;
                passwordSection.style.display = 'none';
                sharePassword.value = '';
                confirmSharePassword.value = '';
                
                // Update generate button text
                generateShareLinkBtn.innerHTML = '<i class="fas fa-key me-1"></i>Generate Link';
                generateShareLinkBtn.disabled = false;
                
                // Show modal
                shareModal.show();
                
                // Focus on generate button for better accessibility
                setTimeout(() => {
                    generateShareLinkBtn.focus();
                }, 500);
            }
        });

        // Toggle password protection
        enablePasswordProtection.addEventListener('change', function() {
            passwordSection.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                setTimeout(() => {
                    sharePassword.focus();
                }, 300);
            }
        });

        // Generate share link
        generateShareLinkBtn.addEventListener('click', async function() {
            const isPasswordProtected = enablePasswordProtection.checked;
            let password = null;
            
            // Validate passwords if protection is enabled
            if (isPasswordProtected) {
                password = sharePassword.value.trim();
                const confirmPassword = confirmSharePassword.value.trim();
                
                if (!password) {
                    showToast('error', 'Please enter a password');
                    sharePassword.focus();
                    return;
                }
                
                if (password !== confirmPassword) {
                    showToast('error', 'Passwords do not match');
                    confirmSharePassword.focus();
                    return;
                }
                
                if (password.length < 4) {
                    showToast('error', 'Password must be at least 4 characters long');
                    sharePassword.focus();
                    return;
                }
            }
            
            // Show loading state
            generateShareLinkBtn.disabled = true;
            generateShareLinkBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating...';
            
            try {
                // Generate shareable link (similar to preview)
                const baseUrl = window.location.origin;
                let shareableUrl;
                
                if (currentItemType === 'file') {
                    // For files, use the preview URL
                    shareableUrl = `${baseUrl}/preview/${currentItemId}`;
                } else {
                    // For folders, you might want to create a different endpoint
                    shareableUrl = `${baseUrl}/folder/share/${currentItemId}`;
                }
                
                // If password protected, you might want to generate a token
                if (isPasswordProtected) {
                    // Here you would typically make an API call to generate a secure share link
                    // For now, we'll just append a parameter (in real app, use proper authentication)
                    shareableUrl += `?token=${btoa(currentItemId + ':' + password)}`;
                }
                
                // Update the share link field
                shareLink.value = shareableUrl;
                
                showToast('success', 'Shareable link generated successfully');
                
                // Focus on copy button after generation
                setTimeout(() => {
                    copyShareLinkBtn.focus();
                }, 300);
                
            } catch (error) {
                console.error('Error generating share link:', error);
                showToast('error', 'Error generating share link');
            } finally {
                // Reset button state
                generateShareLinkBtn.disabled = false;
                generateShareLinkBtn.innerHTML = '<i class="fas fa-key me-1"></i>Generate Link';
            }
        });

        // Copy share link to clipboard
        copyShareLinkBtn.addEventListener('click', function() {
            if (!shareLink.value || shareLink.value === 'Click "Generate Link" to create shareable link') {
                showToast('warning', 'Please generate a link first');
                generateShareLinkBtn.focus();
                return;
            }
            
            navigator.clipboard.writeText(shareLink.value).then(() => {
                showToast('success', 'Link copied to clipboard!');
                
                // Visual feedback
                const originalHtml = copyShareLinkBtn.innerHTML;
                copyShareLinkBtn.innerHTML = '<i class="fas fa-check"></i>';
                copyShareLinkBtn.classList.remove('btn-outline-secondary');
                copyShareLinkBtn.classList.add('btn-success');
                
                setTimeout(() => {
                    copyShareLinkBtn.innerHTML = originalHtml;
                    copyShareLinkBtn.classList.remove('btn-success');
                    copyShareLinkBtn.classList.add('btn-outline-secondary');
                }, 2000);
                
            }).catch(err => {
                console.error('Failed to copy: ', err);
                showToast('error', 'Failed to copy link');
            });
        });

        // Proper modal event handlers to prevent focus issues
        shareModalElement.addEventListener('show.bs.modal', function() {
            // Reset any previous states
            document.body.classList.add('modal-open');
        });

        shareModalElement.addEventListener('hidden.bs.modal', function() {
            // Ensure modal is properly closed
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Remove backdrop if it exists
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Reset form
            shareItemName.value = '';
            shareLink.value = '';
            enablePasswordProtection.checked = false;
            passwordSection.style.display = 'none';
            sharePassword.value = '';
            confirmSharePassword.value = '';
        });

        // Handle escape key and backdrop click properly
        shareModalElement.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                shareModal.hide();
            }
        });

        // Close button handler
        const closeButtons = shareModalElement.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                shareModal.hide();
            });
        });
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
        // Remove any existing click listeners first to prevent duplicates
        document.querySelectorAll('.star-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        
        // Now add fresh event listeners
        document.querySelectorAll('.star-btn').forEach(btn => {
            const { itemId, itemType } = btn.dataset;
            
            // Update initial star status
            updateStarStatus(itemId, itemType, btn);
            
            // Add click event listener with proper event delegation
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Prevent multiple rapid clicks
                if (this.classList.contains('processing')) return;
                this.classList.add('processing');
                
                toggleStar(itemId, itemType, this);
                
                // Remove processing class after delay
                setTimeout(() => {
                    this.classList.remove('processing');
                }, 1000);
            });
        });
    };

    const updateStarStatus = async (id, type, button) => {
        const data = await fetchJSON(`<?= base_url('/starred/check') ?>?item_id=${id}&item_type=${type}`);
        if (!data) return;
        
        if (data.is_starred) {
            button.innerHTML = '<i class="fas fa-star text-warning"></i>';
            button.classList.add('btn-warning');
            button.classList.remove('btn-outline-warning');
        } else {
            button.innerHTML = '<i class="far fa-star"></i>';
            button.classList.remove('btn-warning');
            button.classList.add('btn-outline-warning');
        }
    };

    const toggleStar = async (id, type, button) => {
        // Show loading state
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        try {
            const data = await fetchJSON('<?= base_url('/starred/toggle') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({ 
                    item_id: id, 
                    item_type: type 
                })
            });
            
            if (!data) return;
            
            // Update button appearance based on response
            if (data.is_starred) {
                button.innerHTML = '<i class="fas fa-star text-warning"></i>';
                button.classList.add('btn-warning');
                button.classList.remove('btn-outline-warning');
                showToast('success', data.message || 'Item starred');
            } else {
                button.innerHTML = '<i class="far fa-star"></i>';
                button.classList.remove('btn-warning');
                button.classList.add('btn-outline-warning');
                showToast('info', data.message || 'Star removed');
            }
            
        } catch (error) {
            console.error('Toggle star error:', error);
            showToast('error', 'Failed to toggle star');
            // Revert to original state on error
            button.innerHTML = originalHTML;
        } finally {
            button.disabled = false;
        }
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

    state.bulkDeleteBtn?.addEventListener('click', handleBulkDelete);
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

    /* ---------------- Public Toggle Functionality ---------------- */
    const initPublicToggleFunctionality = () => {
        // Handle toggle public clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('toggle-public-item') || e.target.closest('.toggle-public-item')) {
                const toggleBtn = e.target.classList.contains('toggle-public-item') ? e.target : e.target.closest('.toggle-public-item');
                
                const itemId = toggleBtn.getAttribute('data-item-id');
                const itemType = toggleBtn.getAttribute('data-item-type');
                const itemName = toggleBtn.getAttribute('data-item-name');
                const isCurrentlyPublic = toggleBtn.getAttribute('data-is-public') === '1';
                
                togglePublicAccess(itemId, itemType, itemName, isCurrentlyPublic, toggleBtn);
            }
            
            // Handle copy public link clicks
            if (e.target.classList.contains('copy-public-link') || e.target.closest('.copy-public-link')) {
                const copyBtn = e.target.classList.contains('copy-public-link') ? e.target : e.target.closest('.copy-public-link');
                const publicUrl = copyBtn.getAttribute('data-public-url');
                copyToClipboard(publicUrl);
            }
        });
    };

    const togglePublicAccess = async (itemId, itemType, itemName, isCurrentlyPublic, buttonElement) => {
        const action = isCurrentlyPublic ? 'private' : 'public';
        const confirmMessage = isCurrentlyPublic 
            ? `Make "${itemName}" private? This will revoke public access.`
            : `Make "${itemName}" public? Anyone with the link will be able to access it.`;

        if (!confirm(confirmMessage)) {
            return;
        }

        // Show loading state on the button
        const originalHTML = buttonElement.innerHTML;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        buttonElement.disabled = true;

        try {
            const endpoint = itemType === 'file' 
                ? `<?= base_url('/file/share/') ?>${itemId}`
                : `<?= base_url('/folder/share/') ?>${itemId}`;

            const formData = new FormData();
            formData.append('is_public', isCurrentlyPublic ? 0 : 1);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const data = await fetchJSON(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (data && data.success) {
                showToast('success', data.message || `${itemName} is now ${isCurrentlyPublic ? 'private' : 'public'}`);
                
                // Update UI immediately
                updatePublicUI(itemId, itemType, !isCurrentlyPublic, data.public_url);
                
            } else {
                showToast('error', data?.message || `Failed to make ${itemName} ${action}`);
            }

        } catch (error) {
            console.error('Toggle public error:', error);
            showToast('error', `Error making ${itemName} ${action}`);
        } finally {
            // Reset button state
            buttonElement.innerHTML = originalHTML;
            buttonElement.disabled = false;
        }
    };

    const updatePublicUI = (itemId, itemType, isPublic, publicUrl = null) => {
        // Update the toggle button in dropdown
        const toggleButtons = document.querySelectorAll(`.toggle-public-item[data-item-id="${itemId}"]`);
        toggleButtons.forEach(btn => {
            btn.innerHTML = `<i class="fas ${isPublic ? 'fa-lock-open' : 'fa-link'} me-2"></i>${isPublic ? 'Make Private' : 'Make Public'}`;
            btn.setAttribute('data-is-public', isPublic ? '1' : '0');
        });

        // Update the row data attribute
        const itemRows = document.querySelectorAll(`.item-row[data-id="${itemId}"]`);
        itemRows.forEach(row => {
            row.setAttribute('data-is-public', isPublic ? '1' : '0');
        });

        // Update the public link icon next to the name
        const nameElements = document.querySelectorAll(`.item-row[data-id="${itemId}"] .fa-link`);
        
        if (isPublic) {
            // Add or show the public link icon
            nameElements.forEach(icon => {
                icon.style.display = 'inline-block';
                icon.classList.remove('text-muted');
                icon.classList.add('text-success');
            });
            
            // If no icon exists, add one (for grid view)
            const nameContainers = document.querySelectorAll(`.item-row[data-id="${itemId}"] .fw-semibold, .item-row[data-id="${itemId}"] a.text-decoration-none`);
            nameContainers.forEach(container => {
                if (!container.querySelector('.fa-link')) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-link text-success ms-1';
                    icon.title = 'Publicly shared';
                    container.appendChild(icon);
                }
            });
            
            // Add copy link button to dropdown if public URL is provided
            if (publicUrl) {
                toggleButtons.forEach(btn => {
                    const dropdownMenu = btn.closest('.dropdown-menu');
                    const existingCopyBtn = dropdownMenu.querySelector('.copy-public-link');
                    
                    if (!existingCopyBtn) {
                        const copyItem = document.createElement('li');
                        copyItem.innerHTML = `
                            <a class="dropdown-item copy-public-link" href="javascript:void(0)" 
                            data-public-url="${publicUrl}">
                                <i class="fas fa-copy me-2"></i>Copy Public Link
                            </a>
                        `;
                        
                        // Insert after the toggle public button
                        const toggleLi = btn.closest('li');
                        toggleLi.parentNode.insertBefore(copyItem, toggleLi.nextSibling);
                    } else {
                        existingCopyBtn.setAttribute('data-public-url', publicUrl);
                    }
                });
            }
        } else {
            // Hide or remove the public link icon
            nameElements.forEach(icon => {
                icon.style.display = 'none';
            });
            
            // Remove copy link buttons from dropdown
            toggleButtons.forEach(btn => {
                const dropdownMenu = btn.closest('.dropdown-menu');
                const copyBtn = dropdownMenu.querySelector('.copy-public-link');
                if (copyBtn) {
                    copyBtn.remove();
                }
            });
        }
    };

    const copyToClipboard = (text) => {
        navigator.clipboard.writeText(text).then(() => {
            showToast('success', 'Public link copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            showToast('error', 'Failed to copy link');
        });
    };

    /* ---------------- Init ---------------- */
    initStarButtons();
    initRenameFunctionality();
    initPreviewFunctionality();
    initBulkDelete();
    initPublicToggleFunctionality();
    initShareLinkFunctionality();
    updateCheckboxListeners();
});
</script>