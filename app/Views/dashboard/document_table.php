<!-- Search and Filter Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-center">
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckboxMain">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" id="selectAllBtn">
                    <i class="fas fa-check-square me-1"></i> Select All
                </button>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search files and folders...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="typeFilter">
                    <option value="all">All Items</option>
                    <option value="folder">Folders Only</option>
                    <option value="file">Files Only</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="sortBy">
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="date_asc">Date (Oldest)</option>
                    <option value="date_desc">Date (Newest)</option>
                    <option value="size_asc">Size (Smallest)</option>
                    <option value="size_desc">Size (Largest)</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-folder-open me-2"></i>
            <?= isset($title) ? esc($title) : 'Folder' ?>
            <span class="badge bg-secondary ms-2"><?= count($folders) + count($files) ?> items</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAllCheckboxMain"></th>
                        <th>Name</th>
                        <th width="120">Type</th>
                        <th width="120">Size</th>
                        <th width="180">Modified</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="contentTable">
                    <!-- Folders -->
                    <?php foreach ($folders as $folder): ?>
                        <tr class="item-row" data-type="folder" data-name="<?= esc($folder['name']) ?>" data-id="<?= $folder['id'] ?>">
                            <td class="ps-3"><input type="checkbox" class="item-checkbox" value="<?= $folder['id'] ?>" data-type="folder"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-folder text-warning me-3 fs-5"></i>
                                    <a href="<?= base_url('/folder/view/' . $folder['id']) ?>" class="fw-semibold text-dark text-decoration-none"><?= esc($folder['name']) ?></a>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">Folder</span></td>
                            <td class="text-muted">-</td>
                            <td class="text-muted small"><?= date('M j, Y g:i A', strtotime($folder['updated_at'] ?? 'now')) ?></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="<?= base_url('/folder/view/' . $folder['id']) ?>"><i class="fas fa-folder-open me-2"></i>Open</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="showUploadFileModal(<?= $folder['id'] ?>)"><i class="fas fa-upload me-2"></i>Upload Here</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="showCreateFolderModal(<?= $folder['id'] ?>)"><i class="fas fa-folder-plus me-2"></i>Create Subfolder</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="post" action="<?= base_url('/folder/delete/' . $folder['id']) ?>" class="d-inline">
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Move to Trash</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Files -->
                    <?php foreach ($files as $file): ?>
                        <tr class="item-row" data-type="file" data-name="<?= esc($file['original_name']) ?>" data-id="<?= $file['id'] ?>">
                            <td class="ps-3"><input type="checkbox" class="item-checkbox" value="<?= $file['id'] ?>" data-type="file"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file text-muted me-3 fs-5"></i>
                                    <div><?= esc($file['original_name']) ?></div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark"><?= pathinfo($file['original_name'], PATHINFO_EXTENSION) ?></span></td>
                            <td class="text-muted"><?= number_format($file['size'] / 1024, 2) ?> KB</td>
                            <td class="text-muted small"><?= date('M j, Y g:i A', strtotime($file['updated_at'] ?? 'now')) ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('/file/download/' . $file['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($folders) && empty($files)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">This folder is empty</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Helper functions for folder and file operations
function uploadToFolder(folderId) {
    showUploadFileModal(folderId);
}

function createSubfolder(parentId) {
    showCreateFolderModal(parentId);
}

// Bulk selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const sortBy = document.getElementById('sortBy');
    const selectAllCheckbox = document.getElementById('selectAllCheckboxMain');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    const contentTable = document.getElementById('contentTable');
    
    // Update selected count
    function updateSelectedCount() {
        const selectedCountValue = document.querySelectorAll('.item-checkbox:checked').length;
        selectedCount.textContent = `${selectedCountValue} item${selectedCountValue !== 1 ? 's' : ''} selected`;
        bulkActions.style.display = selectedCountValue > 0 ? 'block' : 'none';
    }
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Select all button
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            if (selectAllCheckbox) selectAllCheckbox.checked = true;
            updateSelectedCount();
        });
    }
    
    // Clear selection button
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            updateSelectedCount();
        });
    }
    
    // Bulk delete button
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedItems = Array.from(itemCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({id: cb.value, type: cb.dataset.type}));
            
            if (selectedItems.length > 0) {
                if (confirm(`Are you sure you want to move ${selectedItems.length} item(s) to trash?`)) {
                    // Send request to server for bulk deletion
                    fetch('<?= base_url('/bulk/delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({items: selectedItems})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', `Successfully moved ${selectedItems.length} item(s) to trash`);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast('error', data.message || 'Error deleting items');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Error deleting items');
                    });
                }
            }
        });
    }
    
    // Search and filter functionality
    function filterAndSortContent() {
        const rows = Array.from(document.querySelectorAll('.item-row'));
        const searchTerm = searchInput.value.toLowerCase();
        const filterType = typeFilter.value;
        const sortValue = sortBy.value;
        
        // Filter rows
        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const type = row.dataset.type;
            
            const matchesSearch = name.includes(searchTerm);
            const matchesType = filterType === 'all' || type === filterType;
            
            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
        
        // Sort rows
        const visibleRows = rows.filter(row => row.style.display !== 'none');
        
        visibleRows.sort((a, b) => {
            switch(sortValue) {
                case 'name_asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                case 'date_asc':
                    return new Date(a.dataset.date) - new Date(b.dataset.date);
                case 'date_desc':
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
                case 'size_asc':
                    return (parseInt(a.dataset.size) || 0) - (parseInt(b.dataset.size) || 0);
                case 'size_desc':
                    return (parseInt(b.dataset.size) || 0) - (parseInt(a.dataset.size) || 0);
                default:
                    return 0;
            }
        });
        
        // Reorder rows in table
        visibleRows.forEach(row => contentTable.appendChild(row));
    }
    
    // Event listeners for search and filter
    if (searchInput) searchInput.addEventListener('input', filterAndSortContent);
    if (typeFilter) typeFilter.addEventListener('change', filterAndSortContent);
    if (sortBy) sortBy.addEventListener('change', filterAndSortContent);
});

// Toast function (if not defined elsewhere)
function showToast(type, message, title = null) {
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.getElementById('toastContainer');
    if (toastContainer) {
        toastContainer.innerHTML += toastHtml;
        const toastElement = new bootstrap.Toast(document.getElementById(toastId));
        toastElement.show();
    }
}
</script>