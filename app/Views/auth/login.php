<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h3>Login</h3>
<form method="post" action="<?= base_url('/login') ?>" class="mt-3">
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Login</button>
  <a href="<?= base_url('/register') ?>" class="btn btn-link">Register</a>
</form>
<?= $this->endSection() ?>
