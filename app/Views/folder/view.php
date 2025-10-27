<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Modals -->
<?= $this->include('components/toast') ?>
<?= $this->include('components/create_folder_modal') ?>
<?= $this->include('components/upload_file_modal') ?>

<!-- Header with Actions -->
<?= view('dashboard/components/topbar_section', [
    'isSubFolder' => true,
    'currentFolder' => $currentFolder,
]) ?>


<!-- Reuse the same table component -->
<?= view('dashboard/document_table', [
    'folders' => $folders,
    'files' => $files
]) ?>

<?= $this->endSection() ?>
