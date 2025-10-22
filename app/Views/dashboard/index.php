<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>
<?= $this->include('components/create_folder_modal') ?>
<?= $this->include('components/upload_file_modal') ?>

<!-- Header with Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active"><i class="fas fa-home me-1"></i> Root</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success" onclick="showUploadFileModal()">
            <i class="fas fa-upload me-1"></i> 
        </button>
        <button type="button" class="btn btn-primary" onclick="showCreateFolderModal()">
            <i class="fas fa-folder-plus me-1"></i>
        </button>
    </div>
</div>

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

<!-- Bulk Actions (Hidden by default) -->
<div class="card shadow-sm mb-4" id="bulkActions" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted" id="selectedCount">0 items selected</span>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm" id="bulkZipBtn">
                    <i class="fas fa-file-archive me-1"></i> Compress (ZIP)
                </button>

                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Move to Trash
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSelectionBtn">
                    <i class="fas fa-times me-1"></i> Clear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="card shadow-sm d-flex flex-column" style="height: calc(100vh - 330px);">
    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-folder-open me-2"></i>Root Directory
            <span class="badge bg-secondary ms-2"><?= count($folders) + count($files) ?> items</span>
        </h5>
    </div>
    <div class="card-body p-0 w-100" style="overflow-y: auto; max-height: 100%;">
        <div class="table-responsive w-100">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3">
                            <input type="checkbox" id="selectAllCheckboxMain">
                        </th>
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
                        <tr class="item-row" data-type="folder" data-name="<?= esc($folder['name']) ?>" data-date="<?= $folder['created_at'] ?? '' ?>" data-id="<?= $folder['id'] ?>">
                            <td class="ps-3">
                                <input type="checkbox" class="item-checkbox" value="<?= $folder['id'] ?>" data-type="folder">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-folder text-warning me-3 fs-5"></i>
                                    <div>
                                        <a href="<?= base_url('/folder/view/' . $folder['id']) ?>" class="text-decoration-none text-dark fw-semibold">
                                            <?= esc($folder['name']) ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">Folder</span>
                            </td>
                            <td class="text-muted">-</td>
                            <td class="text-muted small">
                                <?= isset($folder['updated_at']) ? date('M j, Y g:i A', strtotime($folder['updated_at'])) : 'Unknown' ?>
                            </td>
                            <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('/folder/view/' . $folder['id']) ?>">
                                                <i class="fas fa-folder-open me-2"></i>Open
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="uploadToFolder(<?= $folder['id'] ?>)">
                                                <i class="fas fa-upload me-2"></i>Upload Here
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="createSubfolder(<?= $folder['id'] ?>)">
                                                <i class="fas fa-folder-plus me-2"></i>Create Subfolder
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-edit me-2"></i>Rename
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-share-alt me-2"></i>Share
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="post" action="<?= base_url('/folder/delete/' . $folder['id']) ?>" class="d-inline">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i>Move to Trash
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                <button class="btn btn-sm btn-outline-warning flex-shrink-0 d-flex align-items-center justify-content-center star-btn" 
                                        id="star-btn"
                                        style="width: 32px; height: 32px;"
                                        data-item-id="<?= $folder['id'] ?>" 
                                        data-item-type="folder"
                                        title="Add to starred">
                                    <i class="far fa-star"></i>
                                </button>
                            </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <!-- Files -->
                    <?php foreach ($files as $file): ?>
                        <tr class="item-row" data-type="file" data-name="<?= esc($file['original_name']) ?>" data-date="<?= $file['created_at'] ?? '' ?>" data-size="<?= $file['size'] ?>" data-id="<?= $file['id'] ?>">
                            <td class="ps-3">
                                <input type="checkbox" class="item-checkbox" value="<?= $file['id'] ?>" data-type="file">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php
                                    $file_icon = 'fa-file text-muted';
                                    $file_ext = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                                    
                                    // Set appropriate icon based on file type
                                    if (in_array($file_ext, ['pdf'])) {
                                        $file_icon = 'fa-file-pdf text-danger';
                                    } elseif (in_array($file_ext, ['doc', 'docx'])) {
                                        $file_icon = 'fa-file-word text-primary';
                                    } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
                                        $file_icon = 'fa-file-excel text-success';
                                    } elseif (in_array($file_ext, ['ppt', 'pptx'])) {
                                        $file_icon = 'fa-file-powerpoint text-warning';
                                    } elseif (in_array($file_ext, ['zip', 'rar', 'tar', 'gz'])) {
                                        $file_icon = 'fa-file-archive text-secondary';
                                    } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                        $file_icon = 'fa-file-image text-info';
                                    } elseif (in_array($file_ext, ['mp3', 'wav', 'ogg'])) {
                                        $file_icon = 'fa-file-audio text-info';
                                    } elseif (in_array($file_ext, ['mp4', 'avi', 'mov', 'mkv'])) {
                                        $file_icon = 'fa-file-video text-danger';
                                    }
                                    ?>
                                    <i class="fas <?= $file_icon ?> me-3 fs-5"></i>
                                    <div>
                                        <div class="fw-semibold text-truncate" style="max-width: 300px;">
                                            <?= esc($file['original_name']) ?>
                                        </div>
                                        <small class="text-muted"><?= strtoupper($file_ext) ?> file</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= strtoupper($file_ext) ?></span>
                            </td>
                            <td class="text-muted">
                                <?php
                                if ($file['size'] == 0) {
                                    echo '0 Bytes';
                                } else {
                                    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                                    $i = floor(log($file['size']) / log(1024));
                                    echo round($file['size'] / pow(1024, $i), 2) . ' ' . $sizes[$i];
                                }
                                ?>
                            </td>
                            <td class="text-muted small">
                                <?= isset($file['updated_at']) ? date('M j, Y g:i A', strtotime($file['updated_at'])) : 'Unknown' ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('file/download/' . $file['id']) ?>">
                                                    <i class="fas fa-download me-2"></i>Download
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('file/preview/' . $file['id']) ?>">
                                                    <i class="fas fa-eye me-2"></i>Preview
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-edit me-2"></i>Rename
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-share-alt me-2"></i>Share
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="post" action="<?= base_url('/file/delete/' . $file['id']) ?>" class="d-inline">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Move to Trash
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php if (strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION)) === 'zip'): ?>
                                                <li>
                                                    <a class="dropdown-item" href="<?= base_url('file/extract/' . $file['id']) ?>">
                                                        <i class="fas fa-file-zipper me-2"></i>Extract
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                        </ul>
                                    </div>
                                        <button class="btn btn-sm btn-outline-warning flex-shrink-0 d-flex align-items-center justify-content-center star-btn" 
                                            id="star-btn"
                                            style="width: 32px; height: 32px;"
                                            data-item-id="<?= $file['id'] ?>" 
                                            data-item-type="file"
                                            title="Add to starred">
                                        <i class="far fa-star"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($folders) && empty($files)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">This folder is empty</h5>
                                    <p class="text-muted mb-3">Get started by uploading your first file or creating a folder</p>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success" onclick="showUploadFileModal()">
                                            <i class="fas fa-upload me-1"></i> Upload Files
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="showCreateFolderModal()">
                                            <i class="fas fa-folder-plus me-1"></i> Create Folder
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
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
    const bulkZipBtn = document.getElementById('bulkZipBtn');
    const selectedCount = document.getElementById('selectedCount');
    const contentTable = document.getElementById('contentTable');

    initializeStarButtons();
    
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

    // Bulk ZIP button
    if (bulkZipBtn) {
        bulkZipBtn.addEventListener('click', function() {
            const selectedItems = Array.from(itemCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({id: cb.value, type: cb.dataset.type}));

            if (selectedItems.length === 0) {
                showToast('warning', 'No items selected to compress');
                return;
            }

            if (confirm(`Compress ${selectedItems.length} item(s) into a ZIP file?`)) {
                fetch('<?= base_url('/file/compress') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({items: selectedItems})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Error compressing files');
                });
            }
        });
    }

    function initializeStarButtons() {
        document.querySelectorAll('.star-btn').forEach(btn => {
            const itemId = btn.dataset.itemId;
            const itemType = btn.dataset.itemType;
            
            // Check initial star status
            checkStarStatus(itemId, itemType, btn);
            
            // Add click event
            btn.addEventListener('click', function() {
                toggleStar(itemId, itemType, btn);
            });
        });
    }

    function checkStarStatus(itemId, itemType, button) {
        fetch(`<?= base_url('/starred/check') ?>?item_id=${itemId}&item_type=${itemType}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.is_starred) {
                    button.innerHTML = '<i class="fas fa-star"></i>';
                    button.classList.add('active');
                } else {
                    button.innerHTML = '<i class="far fa-star"></i>';
                    button.classList.remove('active');
                }
            });
    }

    function toggleStar(itemId, itemType, button) {
        fetch('<?= base_url('/starred/toggle') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                item_id: itemId,
                item_type: itemType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_starred) {
                    button.innerHTML = '<i class="fas fa-star"></i>';
                    button.classList.add('active');
                    showToast('success', data.message);
                } else {
                    button.innerHTML = '<i class="far fa-star"></i>';
                    button.classList.remove('active');
                    showToast('warning', data.message);
                }
            } else {
                showToast('error', data.message);
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

<?= $this->endSection() ?>