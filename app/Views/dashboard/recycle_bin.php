<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h3 class="mb-0"><i class="fas fa-trash-alt me-2 text-danger"></i>Recycle Bin</h3>
  <div>
    <button id="restoreSelectedBtn" class="btn btn-success btn-sm me-2" disabled>
      <i class="fas fa-undo-alt me-1"></i> Restore Selected
    </button>
    <button id="deleteSelectedBtn" class="btn btn-danger btn-sm me-2" disabled>
      <i class="fas fa-trash me-1"></i> Delete Selected
    </button>
    <form method="post" action="<?= base_url('/recycle-bin/empty_all') ?>" style="display:inline;">
      <button type="submit" class="btn btn-outline-danger btn-sm"
        onclick="return confirm('Permanently delete ALL items in Recycle Bin? This cannot be undone.')">
        <i class="fas fa-ban me-1"></i> Empty Recycle Bin
      </button>
    </form>
  </div>
</div>

<!-- Search & Filter -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="row g-2 align-items-center">
      <div class="col-md-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="selectAllCheckboxMain">
          <label class="form-check-label" for="selectAllCheckboxMain">Select All</label>
        </div>
      </div>
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="fas fa-search text-muted"></i>
          </span>
          <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search deleted items...">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" id="typeFilter">
          <option value="all">All Items</option>
          <option value="folder">Folders Only</option>
          <option value="file">Files Only</option>
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
          <th>Name</th>
          <th>Type</th>
          <th>Deleted At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($folders as $item): ?>
          <tr data-type="folder">
            <td><input type="checkbox" class="selectItemCheckbox" data-id="<?= $item['id'] ?>" data-type="folder"></td>
            <td><i class="fas fa-folder text-warning me-2"></i><?= esc($item['name']) ?></td>
            <td>Folder</td>
            <td><?= esc($item['deleted_at']) ?></td>
            <td>
              <form method="post" action="<?= base_url('/folder/restore/' . $item['id']) ?>" style="display:inline;">
                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-undo"></i> Restore</button>
              </form>
              <form method="post" action="<?= base_url('/folder/purge/' . $item['id']) ?>" style="display:inline;">
                <button type="submit" class="btn btn-sm btn-danger"
                  onclick="return confirm('Permanently delete this folder?')">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php foreach ($files as $item): ?>
          <tr data-type="file">
            <td><input type="checkbox" class="selectItemCheckbox" data-id="<?= $item['id'] ?>" data-type="file"></td>
            <td><i class="fas fa-file text-secondary me-2"></i><?= esc($item['original_name']) ?></td>
            <td>File</td>
            <td><?= esc($item['deleted_at']) ?></td>
            <td>
              <form method="post" action="<?= base_url('/file/restore/' . $item['id']) ?>" style="display:inline;">
                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-undo"></i> Restore</button>
              </form>
              <form method="post" action="<?= base_url('/file/purge/' . $item['id']) ?>" style="display:inline;">
                <button type="submit" class="btn btn-sm btn-danger"
                  onclick="return confirm('Permanently delete this file?')">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Bulk Actions Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('selectAllCheckboxMain');
  const checkboxes = document.querySelectorAll('.selectItemCheckbox');
  const restoreBtn = document.getElementById('restoreSelectedBtn');
  const deleteBtn = document.getElementById('deleteSelectedBtn');
  const searchInput = document.getElementById('searchInput');
  const typeFilter = document.getElementById('typeFilter');

  // Select All
  selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    toggleActionButtons();
  });

  // Enable/disable bulk buttons
  function toggleActionButtons() {
    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    restoreBtn.disabled = deleteBtn.disabled = !anyChecked;
  }

  checkboxes.forEach(cb => cb.addEventListener('change', toggleActionButtons));

  // Search filter
  searchInput.addEventListener('input', () => {
    const searchTerm = searchInput.value.toLowerCase();
    document.querySelectorAll('#recycleTable tbody tr').forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(searchTerm) ? '' : 'none';
    });
  });

  // Type filter
  typeFilter.addEventListener('change', () => {
    const type = typeFilter.value;
    document.querySelectorAll('#recycleTable tbody tr').forEach(row => {
      if (type === 'all' || row.dataset.type === type) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
});
</script>

<?= $this->endSection() ?>
