<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Modals -->
<?= $this->include('components/toast') ?>
<?= $this->include('components/create_folder_modal') ?>
<?= $this->include('components/upload_file_modal') ?>

<!-- Header with Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>"><i class="fas fa-home me-1"></i> Root</a></li>
                <?php
                // Generate breadcrumb path dynamically
                $segments = explode('/', trim($currentFolder['path'], '/'));
                $path = '';
                foreach ($segments as $segment) {
                    $path .= $segment . '/';
                    $folder = (new \App\Models\FolderModel())->where('path', $path)->first();
                    if ($folder && $folder['id'] != $currentFolder['id']) {
                        echo '<li class="breadcrumb-item"><a href="' . base_url('/folder/view/' . $folder['id']) . '">' . esc($folder['name']) . '</a></li>';
                    }
                }
                ?>
                <li class="breadcrumb-item active"><?= esc($currentFolder['name']) ?></li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success" onclick="showUploadFileModal(<?= $currentFolder['id'] ?>)">
            <i class="fas fa-upload me-1"></i> 
        </button>
        <button type="button" class="btn btn-primary" onclick="showCreateFolderModal(<?= $currentFolder['id'] ?>)">
            <i class="fas fa-folder-plus me-1"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="toggleViewBtn" title="Switch View">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</div>

<!-- Reuse the same table component -->
<?= $this->include('dashboard/document_table', [
    'folders' => $folders,
    'files' => $files
]) ?>

<?= $this->endSection() ?>
