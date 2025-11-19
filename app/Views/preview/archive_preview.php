<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-archive me-2"></i>
                    Archive Preview: <?= esc($file['original_name']) ?>
                </h4>
                <span class="badge bg-light text-dark">
                    <?= count($archiveFiles) ?> files
                </span>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">
                                <i class="fas fa-file me-2"></i>File Name
                            </th>
                            <th width="180">
                                <i class="fas fa-weight-hanging me-2"></i>Size
                            </th>
                            <th width="150">
                                <i class="fas fa-compress-alt me-2"></i>Compressed
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($archiveFiles as $index => $f): ?>
                            <tr class="<?= $index % 2 === 0 ? '' : 'table-light' ?>">
                                <td class="ps-4 text-truncate" style="max-width: 400px;" title="<?= esc($f['name']) ?>">
                                    <i class="fas fa-file me-2 text-muted"></i>
                                    <?= esc($f['name']) ?>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-secondary">
                                        <?= number_format((float)$f['size']) ?> bytes
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($f['compressed'])): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            <?= esc($f['compressed']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-minus me-1"></i>
                                            N/A
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if (!empty($archiveFiles)): ?>
                    <tfoot class="table-light">
                        <tr>
                            <td class="fw-bold ps-4">Total</td>
                            <td class="fw-bold">
                                <?php 
                                $totalSize = array_sum(array_column($archiveFiles, 'size'));
                                echo number_format($totalSize) . ' bytes';
                                ?>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
            
            <?php if (empty($archiveFiles)): ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No files found in archive</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= base_url('files') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Files
                </a>
                <a href="<?= base_url('download/'.$file['id']) ?>" class="btn btn-success btn-lg">
                    <i class="fas fa-download me-2"></i>Download Entire Archive
                </a>
            </div>
        </div>
    </div>

<?= $this->include('components/right_click_protection') ?>

<?= $this->endSection() ?>