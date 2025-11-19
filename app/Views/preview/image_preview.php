<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>"><i class="fas fa-home me-1"></i>Home</a></li>
            <li class="breadcrumb-item active">Preview: <?= $file['original_name'] ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- File Preview Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-eye me-2"></i>File Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center p-4">
                        <img src="<?= $fileUrl ?>" 
                             alt="<?= $file['original_name'] ?>" 
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 60vh; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>

        <!-- File Details Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>File Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Name</label>
                        <p class="form-control bg-light"><?= esc($file['original_name']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Size</label>
                        <p class="form-control bg-light"><?= format_file_size($file['size']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Type</label>
                        <p class="form-control bg-light"><?= strtoupper($fileExtension) ?> (<?= $fileType ?>)</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Uploaded At</label>
                        <p class="form-control bg-light"><?= date('M j, Y g:i A', strtotime($file['created_at'])) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Modified</label>
                        <p class="form-control bg-light"><?= date('M j, Y g:i A', strtotime($file['updated_at'])) ?></p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('file/download/' . $file['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download File
                        </a>
                        <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('components/right_click_protection') ?>

<?= $this->endSection() ?>