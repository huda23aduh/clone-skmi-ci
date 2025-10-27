<?= $this->extend('preview/layout') ?>

<?= $this->section('preview_content') ?>
<div class="code-container">
    <pre class="p-3 mb-0" style="background: #f6f8fa; border-radius: 0;"><code class="language-<?= $language ?>"><?= $content ?></code></pre>
</div>
<?= $this->endSection() ?>