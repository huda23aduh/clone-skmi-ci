<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div class="media-container p-4">
    <img src="<?= $fileUrl ?>" 
         alt="<?= $file['original_name'] ?>" 
         class="img-fluid"
         style="max-height: 70vh; object-fit: contain;">
</div>
<?= $this->endSection() ?>