<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>
<div class="text-center">
    <div class="mb-4">
        <img src="<?= $fileUrl ?>" 
             alt="<?= $file['original_name'] ?>" 
             class="img-fluid rounded shadow"
             style="max-height: 70vh; object-fit: contain; max-width: 100%;">
    </div>
    
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>File Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>File Name:</strong><br><?= esc($file['original_name']) ?></p>
                            <p><strong>File Size:</strong><br><?= format_file_size($file['size']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>File Type:</strong><br><?= strtoupper($fileExtension) ?></p>
                            <p><strong>Uploaded:</strong><br><?= date('M j, Y g:i A', strtotime($file['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="<?= $fileUrl ?>" 
                           class="btn btn-primary"
                           download="<?= $file['original_name'] ?>">
                            <i class="fas fa-download me-2"></i>Download Original
                        </a>
                        <!-- <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Print
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>