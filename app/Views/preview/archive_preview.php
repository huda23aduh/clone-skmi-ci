<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h3>Archive Preview: <?= esc($file['original_name']) ?></h3>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Name</th>
            <th>Size (Bytes)</th>
            <th>Compressed</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($archiveFiles as $f): ?>
            <tr>
                <td><?= esc($f['name']) ?></td>
                <td><?= esc($f['size']) ?></td>
                <td><?= esc($f['compressed'] ?? '-') ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<a href="<?= base_url('download/'.$file['id']) ?>" class="btn btn-primary">
    Download Entire Archive
</a>

<?= $this->endSection() ?>
