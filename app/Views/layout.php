<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= esc($title ?? 'Digital Storage') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url('/') ?>">Digital Storage</a>
    <div class="d-flex">
      <?php if (session()->get('user')): ?>
        <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-outline-light me-2">Dashboard</a>
        <a href="<?= base_url('/recycle-bin') ?>" class="btn btn-sm btn-outline-warning me-2">Recycle Bin</a>
        <a href="<?= base_url('/logout') ?>" class="btn btn-sm btn-danger">Logout</a>
      <?php else: ?>
        <a href="<?= base_url('/login') ?>" class="btn btn-sm btn-outline-light">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container">
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <?= $this->renderSection('content') ?>
</div>

</body>
</html>
