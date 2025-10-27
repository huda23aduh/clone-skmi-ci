<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div class="media-container p-4">
    <audio controls style="width: 100%; max-width: 600px;">
        <source src="<?= $fileUrl ?>" type="<?= $mimeType ?>">
        Your browser does not support the audio element.
    </audio>
</div>
<?= $this->endSection() ?>