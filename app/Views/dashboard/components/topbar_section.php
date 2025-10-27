<?php
/**
 * Reusable dashboard topbar
 * Variables expected:
 * - $isSubFolder (bool)
 * - $currentFolder (array|null)
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <?php if (!empty($isSubFolder) && !empty($currentFolder)): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('/dashboard') ?>">
                            <i class="fas fa-home me-1"></i> Root
                        </a>
                    </li>

                    <?php
                    // Build breadcrumb segments dynamically
                    $segments = explode('/', trim($currentFolder['path'], '/'));
                    $path = '';

                    foreach ($segments as $segment) {
                        $path .= $segment . '/';
                        $folder = (new \App\Models\FolderModel())
                            ->where('path', $path)
                            ->first();

                        if ($folder && $folder['id'] != $currentFolder['id']) {
                            echo '<li class="breadcrumb-item"><a href="' . base_url('/folder/view/' . $folder['id']) . '">' . esc($folder['name']) . '</a></li>';
                        }
                    }
                    ?>

                    <li class="breadcrumb-item active"><?= esc($currentFolder['name']) ?></li>

                <?php else: ?>
                    <li class="breadcrumb-item active">
                        <i class="fas fa-home me-1"></i> Root
                    </li>
                <?php endif; ?>

            </ol>
        </nav>
    </div>

    <div class="btn-group">
        <!-- Upload -->
        <button type="button"
                class="btn btn-success"
                onclick="showUploadFileModal(<?= isset($currentFolder['id']) ? $currentFolder['id'] : 'null' ?>)">
            <i class="fas fa-upload me-1"></i>
        </button>

        <!-- Create Folder -->
        <button type="button"
                class="btn btn-primary"
                onclick="showCreateFolderModal(<?= isset($currentFolder['id']) ? $currentFolder['id'] : 'null' ?>)">
            <i class="fas fa-folder-plus me-1"></i>
        </button>

        <!-- View Toggle -->
        <button type="button"
                class="btn btn-outline-secondary"
                id="toggleViewBtn"
                title="Switch View">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</div>
