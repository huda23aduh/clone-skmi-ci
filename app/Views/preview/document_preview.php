<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div class="text-center p-5">
    <div class="mb-4">
        <i class="fas fa-file-word fa-5x text-primary mb-3"></i>
        <h4>Document Preview</h4>
    </div>
    <p class="text-muted mb-4">
        This document format cannot be previewed directly in the browser.
    </p>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Please download the file to view its contents.
    </div>
    <a href="<?= $fileUrl ?>" class="btn btn-primary" download>
        <i class="fas fa-download me-2"></i>Download Document
    </a>
</div>
<?= $this->endSection() ?>