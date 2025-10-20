<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h3>Register</h3>
<form method="post" action="<?= base_url('/register') ?>" class="mt-3">
  <div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-success">Register</button>
  <a href="<?= base_url('/login') ?>" class="btn btn-link">Login</a>
</form>
<?= $this->endSection() ?>
