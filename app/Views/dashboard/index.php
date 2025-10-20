<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3c8dbc;
            --secondary-color: #f4f4f4;
            --border-color: #ddd;
        }
        
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .welcome-message {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .file-manager-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--secondary-color);
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .table-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .table-controls select, .table-controls input {
            padding: 5px 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }
        
        .file-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .file-table th {
            background-color: var(--secondary-color);
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }
        
        .file-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .file-table tr:last-child td {
            border-bottom: none;
        }
        
        .file-table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .file-icon {
            margin-right: 8px;
            color: #666;
        }
        
        .folder-icon {
            color: #ffc107;
        }
        
        .file-actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 0.85rem;
        }
        
        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-color);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
        }
        
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .table-controls {
                flex-wrap: wrap;
            }
            
            .file-table {
                font-size: 0.9rem;
            }
            
            .file-table th, .file-table td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <div>
        <!-- Header Section -->
        <div class="header-section">
            <div class="action-buttons">
                <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="fas fa-folder-plus"></i> 
                </button>
                <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                    <i class="fas fa-upload"></i>
                </button>
                <button type="button" class="btn btn-outline-primary btn-action" id="deleteSelectedBtn">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>
        </div>

        <!-- File Manager Card -->
        <div class="file-manager-card">
            <div class="card-header">
                <h3 class="card-title">Root</h3>
                <div class="table-controls">
                    <select id="filterSelect" class="form-select form-select-sm">
                        <option value="all">All Items</option>
                        <option value="folder">Folders Only</option>
                        <option value="file">Files Only</option>
                    </select>
                    <select id="sortSelect" class="form-select form-select-sm">
                        <option value="name">Sort by Name</option>
                        <option value="type">Sort by Type</option>
                        <option value="size">Sort by Size</option>
                        <option value="date">Sort by Date</option>
                    </select>
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            
            <div class="card-body p-0">
                <table class="file-table">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Folders -->
                        <?php foreach ($folders as $f): ?>
                        <tr class="file-item" data-type="folder" data-name="<?= esc($f['name']) ?>" data-date="<?= date('Y-m-d', strtotime($f['created_at'] ?? 'now')) ?>">
                            <td>
                                <input type="checkbox" class="item-checkbox" data-id="<?= $f['id'] ?>" data-type="folder">
                            </td>
                            <td>
                                <i class="fas fa-folder folder-icon file-icon"></i>
                                <?= esc($f['name']) ?>
                            </td>
                            <td>Folder</td>
                            <td><?= isset($f['size']) ? number_format($f['size']/1024, 2) . ' KB' : '--' ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($f['created_at'] ?? 'now')) ?></td>
                            <td class="file-actions">
                                <button class="btn btn-sm btn-outline-primary" title="Open">
                                    <i class="fas fa-folder-open"></i>
                                </button>
                                <form method="post" action="<?= base_url('/folder/delete/' . $f['id']) ?>" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Move to Trash">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Files -->
                        <?php foreach ($files as $f): ?>
                        <tr class="file-item" data-type="file" data-name="<?= esc($f['original_name']) ?>" data-size="<?= $f['size'] ?>" data-date="<?= date('Y-m-d', strtotime($f['created_at'] ?? 'now')) ?>">
                            <td>
                                <input type="checkbox" class="item-checkbox" data-id="<?= $f['id'] ?>" data-type="file">
                            </td>
                            <td>
                                <?php
                                $fileExtension = pathinfo($f['original_name'], PATHINFO_EXTENSION);
                                $iconClass = 'fa-file';
                                
                                switch(strtolower($fileExtension)) {
                                    case 'pdf': $iconClass = 'fa-file-pdf'; break;
                                    case 'doc': 
                                    case 'docx': $iconClass = 'fa-file-word'; break;
                                    case 'xls':
                                    case 'xlsx': $iconClass = 'fa-file-excel'; break;
                                    case 'png':
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'gif': $iconClass = 'fa-file-image'; break;
                                    case 'zip':
                                    case 'rar': $iconClass = 'fa-file-archive'; break;
                                }
                                ?>
                                <i class="fas <?= $iconClass ?> file-icon"></i>
                                <?= esc($f['original_name']) ?>
                            </td>
                            <td><?= strtoupper($fileExtension) ?> File</td>
                            <td><?= number_format($f['size']/1024, 2) ?> KB</td>
                            <td><?= date('Y-m-d H:i', strtotime($f['created_at'] ?? 'now')) ?></td>
                            <td class="file-actions">
                                <a href="<?= base_url('file/download/' . $f['id']) ?>" class="btn btn-sm btn-outline-primary" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form method="post" action="<?= base_url('/file/delete/' . $f['id']) ?>" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Move to Trash">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= base_url('/folder/create') ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" name="name" placeholder="Enter folder name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload File Modal -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Upload File</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= base_url('/file/upload') ?>" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Select File</label>
                            <input class="form-control" type="file" id="fileInput" name="file" required>
                        </div>
                        <div class="mb-3">
                            <label for="folderSelect" class="form-label">Destination Folder</label>
                            <select class="form-select" id="folderSelect" name="folder_id">
                                <option value="">-- Upload to Root --</option>
                                <?php foreach($folders as $f): ?>
                                    <option value="<?= $f['id'] ?>"><?= esc($f['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select All Checkbox
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
            
            // Delete Selected Button
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            deleteSelectedBtn.addEventListener('click', function() {
                const selectedItems = Array.from(itemCheckboxes).filter(cb => cb.checked);
                
                if (selectedItems.length === 0) {
                    alert('Please select at least one item to delete.');
                    return;
                }
                
                if (confirm(`Are you sure you want to move ${selectedItems.length} item(s) to trash?`)) {
                    // In a real implementation, you would send a request to delete all selected items
                    // For now, we'll just submit the forms individually
                    selectedItems.forEach(checkbox => {
                        const form = checkbox.closest('tr').querySelector('form');
                        if (form) {
                            form.submit();
                        }
                    });
                }
            });
            
            // Filter Functionality
            const filterSelect = document.getElementById('filterSelect');
            const fileItems = document.querySelectorAll('.file-item');
            
            filterSelect.addEventListener('change', function() {
                const filterValue = this.value;
                
                fileItems.forEach(item => {
                    if (filterValue === 'all') {
                        item.style.display = '';
                    } else if (filterValue === 'folder' && item.dataset.type === 'folder') {
                        item.style.display = '';
                    } else if (filterValue === 'file' && item.dataset.type === 'file') {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Search Functionality
            const searchInput = document.getElementById('searchInput');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                fileItems.forEach(item => {
                    const fileName = item.dataset.name.toLowerCase();
                    if (fileName.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Sort Functionality
            const sortSelect = document.getElementById('sortSelect');
            const tableBody = document.querySelector('.file-table tbody');
            
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                
                rows.sort((a, b) => {
                    let aValue, bValue;
                    
                    switch(sortValue) {
                        case 'name':
                            aValue = a.dataset.name.toLowerCase();
                            bValue = b.dataset.name.toLowerCase();
                            return aValue.localeCompare(bValue);
                            
                        case 'type':
                            aValue = a.dataset.type;
                            bValue = b.dataset.type;
                            return aValue.localeCompare(bValue);
                            
                        case 'size':
                            aValue = parseFloat(a.dataset.size) || 0;
                            bValue = parseFloat(b.dataset.size) || 0;
                            return aValue - bValue;
                            
                        case 'date':
                            aValue = new Date(a.dataset.date);
                            bValue = new Date(b.dataset.date);
                            return bValue - aValue;
                            
                        default:
                            return 0;
                    }
                });
                
                // Re-append rows in sorted order
                rows.forEach(row => tableBody.appendChild(row));
            });
        });
    </script>
</body>
</html>

<?= $this->endSection() ?>
