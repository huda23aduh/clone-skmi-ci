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
        <div class="table-responsive" id="listContainer">
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
                        <tr><td colspan="6" class="text-center py-4 text-muted">This folder is empty</td></tr>
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
