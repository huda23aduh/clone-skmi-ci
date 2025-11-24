<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?= $this->include('components/toast') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h3 class="mb-0"><i class="fas fa-trash-alt me-2 text-danger"></i><?= lang('app.recyclebin') ?></h3>
  <div>
    <button id="restoreSelectedBtn" class="btn btn-success btn-sm me-2" disabled>
      <i class="fas fa-undo-alt me-1"></i> <?= app_lang('app.restoreselected') ?>
    </button>
    <button id="deleteSelectedBtn" class="btn btn-danger btn-sm me-2" disabled>
      <i class="fas fa-trash me-1"></i> <?= app_lang('app.deletepermanently') ?>
    </button>
    <form method="post" action="<?= base_url('/recycle-bin/empty_all') ?>" style="display:inline;">
      <button type="submit" class="btn btn-outline-danger btn-sm"
        onclick="return confirm('Permanently delete ALL items in Recycle Bin? This cannot be undone.')">
        <i class="fas fa-ban me-1"></i> <?= app_lang('app.emptyrecyclebin') ?>
      </button>
    </form>
  </div>
</div>

<!-- Bulk Actions Info -->
<div id="bulkActionsInfo" class="alert alert-info" style="display: none;">
  <i class="fas fa-info-circle me-2"></i>
  <span id="selectedItemsCount">0</span> item(s) selected
</div>

<!-- Search & Filter -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="row g-2 align-items-center">
      <div class="col-md-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="selectAllCheckboxMain">
          <label class="form-check-label" for="selectAllCheckboxMain"><?= app_lang('app.selectall') ?></label>
        </div>
      </div>
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="fas fa-search text-muted"></i>
          </span>
          <input type="text" class="form-control border-start-0" id="searchInput" placeholder="<?= app_lang('app.searchdeleteditems') ?>...">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" id="typeFilter">
          <option value="all"><?= app_lang('app.allitems') ?></option>
          <option value="folder"><?= app_lang('app.folderonly') ?></option>
          <option value="file"><?= app_lang('app.fileonly') ?></option>
        </select>
      </div>
    </div>
  </div>
</div>

<!-- Deleted Items Table -->
<div class="card shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-hover align-middle" id="recycleTable">
      <thead class="table-light">
        <tr>
          <th style="width: 40px;"></th>
          <th><?= app_lang('app.name') ?></th>
          <th><?= app_lang('app.type') ?></th>
          <th><?= app_lang('app.size') ?></th>
          <th>Deleted At</th>
          <th><?= app_lang('app.actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($folders as $item): ?>
          <tr data-type="folder" data-name="<?= esc($item['name']) ?>">
            <td><input type="checkbox" class="selectItemCheckbox" data-id="<?= $item['id'] ?>" data-type="folder"></td>
            <td>
              <i class="fas fa-folder text-warning me-2"></i>
              <span class="item-name"><?= esc(strlen($item['name']) > 25 ? substr($item['name'], 0, 25) . '...' : $item['name']) ?></span>
            </td>
            <td><span class="badge bg-warning">Folder</span></td>
            <td>-</td>
            <td><?= date('M j, Y g:i A', strtotime($item['deleted_at'])) ?></td>
            <td>
              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success restore-single" 
                        data-id="<?= $item['id'] ?>" data-type="folder" data-name="<?= esc($item['name']) ?>">
                  <i class="fas fa-undo"></i> Restore
                </button>
                <button type="button" class="btn btn-outline-danger delete-single"
                        data-id="<?= $item['id'] ?>" data-type="folder" data-name="<?= esc($item['name']) ?>">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php foreach ($files as $item): ?>
          <tr data-type="file" data-name="<?= esc($item['original_name']) ?>">
            <td><input type="checkbox" class="selectItemCheckbox" data-id="<?= $item['id'] ?>" data-type="file"></td>
            <td>
              <i class="fas fa-file text-secondary me-2"></i>
              <span class="item-name"><?= esc(strlen($item['original_name']) > 25 ? substr($item['original_name'], 0, 25) . '...' : $item['original_name']) ?></span>
            </td>
            <td><span class="badge bg-secondary">File</span></td>
            <td>
              <?php
              if ($item['size'] == 0) {
                  echo '0 Bytes';
              } else {
                  $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                  $i = floor(log($item['size']) / log(1024));
                  echo round($item['size'] / pow(1024, $i), 2) . ' ' . $sizes[$i];
              }
              ?>
            </td>
            <td><?= date('M j, Y g:i A', strtotime($item['deleted_at'])) ?></td>
            <td>
              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success restore-single"
                        data-id="<?= $item['id'] ?>" data-type="file" data-name="<?= esc($item['original_name']) ?>">
                  <i class="fas fa-undo"></i> Restore
                </button>
                <button type="button" class="btn btn-outline-danger delete-single"
                        data-id="<?= $item['id'] ?>" data-type="file" data-name="<?= esc($item['original_name']) ?>">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($folders) && empty($files)): ?>
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">
              <i class="fas fa-inbox fa-3x mb-3"></i>
              <p class="mb-0"><?= app_lang('app.nodata') ?></p>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('recycle_bin/components/recycle_bin_script') ?>
<?= $this->endSection() ?>