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
        <!-- CSV Preview Section -->
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-table me-2"></i>CSV Preview - <?= $file['original_name'] ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($csvData)): ?>
                        <div class="table-responsive" style="max-height: 70vh;">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <?php foreach ($headers as $header): ?>
                                            <th class="text-nowrap"><?= esc($header) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($csvData as $index => $row): ?>
                                        <tr>
                                            <?php foreach ($headers as $header): ?>
                                                <td class="text-nowrap" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                    <?= esc($row[$header]) ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3 text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing <?= count($csvData) ?> rows from the CSV file.
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>No data found in CSV file</h5>
                            <p class="text-muted">The CSV file appears to be empty or could not be read.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- File Details Section -->
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i><?= lang('app.file_details') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold"><?= lang('app.name') ?></label>
                            <p class="form-control bg-light"><?= esc($file['original_name']) ?></p>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-semibold"><?= lang('app.size') ?></label>
                            <p class="form-control bg-light"><?= format_file_size($file['size']) ?></p>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-semibold"><?= lang('app.type') ?></label>
                            <p class="form-control bg-light">CSV File</p>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-semibold">Total Rows</label>
                            <p class="form-control bg-light"><?= count($csvData) ?></p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Total Columns</label>
                            <p class="form-control bg-light"><?= count($headers) ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><?= lang('app.uploaded_at') ?></label>
                            <p class="form-control bg-light"><?= date('M j, Y g:i A', strtotime($file['created_at'])) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><?= lang('app.last_modified') ?></label>
                            <p class="form-control bg-light"><?= date('M j, Y g:i A', strtotime($file['updated_at'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="<?= base_url('file/download/' . $file['id']) ?>" class="btn btn-primary me-md-2">
                            <i class="fas fa-download me-1"></i>Download CSV
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