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
        <!-- Document Preview Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-file-word me-2"></i>Document Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center p-5">
                        <?php
                        $icon = 'fa-file text-muted';
                        $color = 'text-muted';
                        if (in_array($fileExtension, ['doc', 'docx'])) {
                            $icon = 'fa-file-word';
                            $color = 'text-primary';
                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                            $icon = 'fa-file-excel';
                            $color = 'text-success';
                        } elseif (in_array($fileExtension, ['ppt', 'pptx'])) {
                            $icon = 'fa-file-powerpoint';
                            $color = 'text-warning';
                        }
                        ?>
                        <div class="mb-4">
                            <i class="fas <?= $icon ?> fa-5x <?= $color ?> mb-3"></i>
                            <h4>Document Preview</h4>
                        </div>
                        <p class="text-muted mb-4">
                            This document format cannot be previewed directly in the browser.
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Please download the file to view its contents.
                        </div>
                        <a href="<?= base_url('file/download/' . $file['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download Document
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Details Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i><?= lang('app.file_details') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('app.name') ?></label>
                        <p class="form-control bg-light"><?= esc($file['original_name']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('app.size') ?></label>
                        <p class="form-control bg-light"><?= format_file_size($file['size']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('app.type') ?></label>
                        <p class="form-control bg-light"><?= strtoupper($fileExtension) ?> Document</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('app.uploaded_at') ?></label>
                        <p class="form-control bg-light"><?= date('M j, Y g:i A', strtotime($file['created_at'])) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('app.last_modified') ?></label>
                        <p class="form-control bg-light"><?= date('M j, Y g:i A', strtotime($file['updated_at'])) ?></p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('file/download/' . $file['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i><?= lang('app.download') ?>
                        </a>
                        <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i><?= lang('app.back_to_dashboard') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('components/right_click_protection') ?>

<?= $this->endSection() ?>