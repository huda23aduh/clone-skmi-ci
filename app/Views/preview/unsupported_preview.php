<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div class="text-center p-5">
    <div class="mb-4">
        <i class="fas fa-file fa-5x text-muted mb-3"></i>
        <h4>Preview Not Available</h4>
    </div>
    <p class="text-muted mb-4">
        This file format cannot be previewed in the browser.
    </p>
    <a href="<?= base_url('file/download/' . $file['id']) ?>" class="btn btn-primary">
        <i class="fas fa-download me-2"></i>Download File
    </a>
</div>
<?= $this->endSection() ?>