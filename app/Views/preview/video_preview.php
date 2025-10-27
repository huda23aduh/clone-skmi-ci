<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div class="media-container p-4">
    <video controls style="max-width: 100%; max-height: 70vh;">
        <source src="<?= $fileUrl ?>" type="<?= $mimeType ?>">
        Your browser does not support the video element.
    </video>
</div>
<?= $this->endSection() ?>