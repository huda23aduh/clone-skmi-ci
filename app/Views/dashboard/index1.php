
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h3>Welcome, <?= esc($user['name'] ?? $user['email']) ?></h3>

<p>Hello, <?= esc($user['name'] ?? 'Guest') ?>!</p>

<hr>

<h5>Create Folder</h5>
<form method="post" action="<?= base_url('/folder/create') ?>" class="mb-3">
  <div class="input-group">
    <input type="text" name="name" class="form-control" placeholder="Folder name" required>
    <button type="submit" class="btn btn-primary">Create</button>
  </div>
</form>

<h5>Upload File</h5>
<form method="post" action="<?= base_url('/file/upload') ?>" enctype="multipart/form-data" class="mb-3">
    <div class="input-group mb-2">
        <input type="file" name="file" class="form-control" required>
    </div>
    <div class="input-group mb-2">
        <select name="folder_id" class="form-select">
            <option value="">-- Upload to Root --</option>
            <?php foreach($folders as $f): ?>
                <option value="<?= $f['id'] ?>"><?= esc($f['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Upload</button>
</form>


<h5>Your Folders</h5>
<table class="table table-striped">
  <thead><tr><th>Name</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach ($folders as $f): ?>
      <tr>
        <td><?= esc($f['name']) ?></td>
        <td>
          <form method="post" action="<?= base_url('/folder/delete/' . $f['id']) ?>" style="display:inline;">
            <button type="submit" class="btn btn-sm btn-danger">Move to Trash</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h5>Your Files</h5>
<table class="table table-striped">
  <thead><tr><th>Name</th><th>Size</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach ($files as $f): ?>
      <tr>
        <td><?= esc($f['original_name']) ?></td>
        <td><?= number_format($f['size']/1024, 2) ?> KB</td>
        <td>
          <form method="post" action="<?= base_url('/file/delete/' . $f['id']) ?>" style="display:inline;">
            <button type="submit" class="btn btn-sm btn-danger">Move to Trash</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Folders</h2>


<?php if(count($folders) > 0): ?>
  <?php foreach($folders as $folder): ?>
    <div style="margin-bottom: 20px;">
        <h3>📁 <?= esc($folder['name'] ?? 'Unnamed Folder') ?></h3>
        
        <ul>
            <?php 
            $folderFiles = array_filter($files, fn($f) => $f['folder_id'] == $folder['id']); 
            ?>
            <?php if(count($folderFiles) > 0): ?>
                <?php foreach($folderFiles as $file): ?>
                    <li><?= esc($file['original_name'] ?? 'Unnamed File') ?>
                        <a href="<?= base_url('file/download/' . $file['id']) ?>">Download</a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><em>No files</em></li>
            <?php endif; ?>
        </ul>
    </div>
<?php endforeach; ?>

<?php else: ?>
    <p>No folders yet</p>
<?php endif; ?>

<h2>Root Files</h2>
<ul>
    <?php 
    $rootFiles = array_filter($files, fn($f) => $f['folder_id'] === null); 
    ?>
    <?php if(count($rootFiles) > 0): ?>
        <?php foreach($rootFiles as $file): ?>
            <li><?= esc($file['original_name']) ?>
                <a href="<?= base_url('file/download/' . $file['id']) ?>">Download</a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li><em>No files in root</em></li>
    <?php endif; ?>
</ul>


<?= $this->endSection() ?>
