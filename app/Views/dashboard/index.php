<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>
<?= $this->include('components/create_folder_modal') ?>
<?= $this->include('components/upload_file_modal') ?>
<?= $this->include('dashboard/components/rename_modal') ?>

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
        <button type="button" class="btn btn-outline-secondary" id="toggleViewBtn" title="Switch View">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</div>

<!-- Search and Filter Card -->
<?= $this->include('dashboard/components/search_filter_section') ?>


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
        <div class="table-responsive w-100" id="listContainer">
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
                                            <a class="dropdown-item rename-item" href="javascript:void(0)" 
                                            data-item-id="<?= $folder['id'] ?>" 
                                            data-item-type="folder"
                                            data-item-name="<?= esc($folder['name']) ?>">
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
                                        <div class="fw-semibold text-truncate file-name-cell" style="max-width: 300px; cursor: pointer;">
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
                                                <a class="dropdown-item preview-item" href="javascript:void(0)" 
                                                data-file-id="<?= $file['id'] ?>">
                                                    <i class="fas fa-eye me-2"></i>Preview
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item rename-item" href="javascript:void(0)" 
                                                data-item-id="<?= $file['id'] ?>" 
                                                data-item-type="file"
                                                data-item-name="<?= esc($file['original_name']) ?>">
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
        <!-- Grid View -->
        <div id="gridView" class="row row-cols-2 row-cols-md-4 g-3 p-3" style="display: none;">
            <?php foreach ($folders as $folder): ?>
                <div class="col item-row" data-type="folder" data-id="<?= $folder['id'] ?>">
                    <div class="card shadow-sm h-100 position-relative p-3">
                        <input type="checkbox" class="form-check-input item-checkbox position-absolute top-0 end-0 m-2" 
                            value="<?= $folder['id'] ?>" data-type="folder" style="transform: scale(1.3);">

                        <div class="d-flex align-items-center mb-2 mt-2">
                            <i class="fas fa-folder text-warning fa-2x me-2"></i>
                            <div class="fw-semibold text-truncate"><?= esc($folder['name']) ?></div>
                        </div>
                        <div class="small text-muted mb-2">
                            <?= isset($folder['updated_at']) ? date('M j, Y g:i A', strtotime($folder['updated_at'])) : 'Unknown' ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('/folder/view/' . $folder['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-folder-open"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-warning star-btn" 
                                data-item-id="<?= $folder['id'] ?>" data-item-type="folder">
                                <i class="far fa-star"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($files as $file): ?>
                <?php
                $file_ext = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                $file_icon = 'fa-file text-muted';
                if (in_array($file_ext, ['pdf'])) $file_icon = 'fa-file-pdf text-danger';
                elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'fa-file-word text-primary';
                elseif (in_array($file_ext, ['xls', 'xlsx'])) $file_icon = 'fa-file-excel text-success';
                elseif (in_array($file_ext, ['jpg','jpeg','png','gif'])) $file_icon = 'fa-file-image text-info';
                elseif (in_array($file_ext, ['mp4','avi','mkv'])) $file_icon = 'fa-file-video text-danger';
                ?>
                <div class="col item-row" data-type="file" data-id="<?= $file['id'] ?>">
                    <div class="card shadow-sm h-100 position-relative p-3">
                        <input type="checkbox" class="form-check-input item-checkbox position-absolute top-0 end-0 m-2" 
                            value="<?= $file['id'] ?>" data-type="file" style="transform: scale(1.3);">

                        <div class="d-flex align-items-center mb-2 mt-2">
                            <i class="fas <?= $file_icon ?> fa-2x me-2"></i>
                            <div class="fw-semibold text-truncate" title="<?= esc($file['original_name']) ?>">
                                <?= esc($file['original_name']) ?>
                            </div>
                        </div>
                        <div class="small text-muted mb-2">
                            <?= isset($file['updated_at']) ? date('M j, Y g:i A', strtotime($file['updated_at'])) : 'Unknown' ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('file/download/' . $file['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-warning star-btn" 
                                data-item-id="<?= $file['id'] ?>" data-item-type="file">
                                <i class="far fa-star"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->include('dashboard/components/dashboard_script') ?>


<?= $this->endSection() ?>