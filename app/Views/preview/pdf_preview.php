<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div style="height: 80vh;">
    <iframe src="<?= $fileUrl ?>#toolbar=0&view=FitH" 
            class="preview-iframe" 
            style="height: 100%;">
        <p>Your browser does not support PDF preview. 
           <a href="<?= $fileUrl ?>">Download the PDF</a> instead.</p>
    </iframe>
</div>
<?= $this->endSection() ?>