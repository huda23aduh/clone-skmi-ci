<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>"><i class="fas fa-home me-1"></i> Root</a></li>
                <li class="breadcrumb-item active"><i class="fas fa-star text-warning me-1"></i> Starred Items</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Content Card -->
<div class="card shadow-sm">
    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-star text-warning me-2"></i>Starred Items
            <span class="badge bg-warning ms-2"><?= count($starredItems) ?> items</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th width="120">Type</th>
                        <th width="120">Size</th>
                        <th width="180">Starred Date</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($starredItems)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-star fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No starred items</h5>
                                    <p class="text-muted">Star important files and folders to see them here</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($starredItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($item['type'] === 'folder'): ?>
                                            <i class="fas fa-folder text-warning me-3 fs-5"></i>
                                        <?php else: ?>
                                            <?php
                                            $file_icon = 'fa-file text-muted';
                                            $file_ext = pathinfo($item['name'], PATHINFO_EXTENSION);
                                            
                                            if (in_array($file_ext, ['pdf'])) {
                                                $file_icon = 'fa-file-pdf text-danger';
                                            } elseif (in_array($file_ext, ['doc', 'docx'])) {
                                                $file_icon = 'fa-file-word text-primary';
                                            } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
                                                $file_icon = 'fa-file-excel text-success';
                                            } elseif (in_array($file_ext, ['zip', 'rar'])) {
                                                $file_icon = 'fa-file-archive text-secondary';
                                            } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                $file_icon = 'fa-file-image text-info';
                                            }
                                            ?>
                                            <i class="fas <?= $file_icon ?> me-3 fs-5"></i>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-semibold">
                                                <?php if ($item['type'] === 'folder'): ?>
                                                    <a href="<?= base_url('/folder/view/' . $item['id']) ?>" class="text-decoration-none text-dark">
                                                        <?= esc($item['name']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?= esc($item['name']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= ucfirst($item['type']) ?>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?php if ($item['type'] === 'file' && $item['size']): ?>
                                        <?php
                                        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                                        $i = floor(log($item['size']) / log(1024));
                                        echo round($item['size'] / pow(1024, $i), 2) . ' ' . $sizes[$i];
                                        ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?= date('M j, Y g:i A', strtotime($item['created_at'])) ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning" 
                                            onclick="toggleStar(<?= $item['id'] ?>, '<?= $item['type'] ?>')"
                                            title="Remove star">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <?php if ($item['type'] === 'file'): ?>
                                        <a href="<?= base_url('file/download/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleStar(itemId, itemType) {
    fetch('<?= base_url('/starred/toggle') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            item_id: itemId,
            item_type: itemType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.is_starred ? 'success' : 'warning', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error updating star');
    });
}
</script>

<?= $this->endSection() ?>