<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h3>Recycle Bin</h3>

<h5>Deleted Folders</h5>
<table class="table table-bordered">
  <thead><tr><th>Name</th><th>Deleted At</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach ($folders as $f): ?>
      <tr>
        <td><?= esc($f['name']) ?></td>
        <td><?= esc($f['deleted_at']) ?></td>
        <td>
          <form method="post" action="<?= base_url('/folder/restore/' . $f['id']) ?>" style="display:inline;">
            <button type="submit" class="btn btn-sm btn-success">Restore</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h5>Deleted Files</h5>
<table class="table table-bordered">
  <thead><tr><th>Name</th><th>Deleted At</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach ($files as $f): ?>
      <tr>
        <td><?= esc($f['original_name']) ?></td>
        <td><?= esc($f['deleted_at']) ?></td>
        <td>
          <form method="post" action="<?= base_url('/file/restore/' . $f['id']) ?>" style="display:inline;">
            <button type="submit" class="btn btn-sm btn-success">Restore</button>
          </form>
          <form method="post" action="<?= base_url('/file/purge/' . $f['id']) ?>" style="display:inline;">
            <button type="submit" class="btn btn-sm btn-danger">Delete Permanently</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?= $this->endSection() ?>
