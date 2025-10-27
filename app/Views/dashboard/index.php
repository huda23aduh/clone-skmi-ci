<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>
<?= $this->include('components/create_folder_modal') ?>
<?= $this->include('components/upload_file_modal') ?>
<?= $this->include('dashboard/components/rename_modal') ?>

<!-- Header with Actions -->
<?= view('dashboard/components/topbar_section', [
    'isSubFolder' => false,
    'currentFolder' => null,
]) ?>

<!-- Content Card -->
<?= view('dashboard/document_table', [
    'folders' => $folders,
    'files' => $files
]) ?>

<?= $this->include('dashboard/components/dashboard_script') ?>


<?= $this->endSection() ?>