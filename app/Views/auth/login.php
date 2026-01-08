<!doctype html>
<html lang="en">
<head>
    <title><?= app_lang('app.login') ?> | AdminLTE</title>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <!--end::Fonts-->
    
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?= base_url('adminlte/css/adminlte.css') ?>">
    <!--end::Required Plugin(AdminLTE)-->
    
    <style>
        .language-switcher {
            position: absolute;
            top: 15px;
            right: 15px;
        }
        .language-switcher .btn-sm {
            padding: 0.15rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="login-page bg-body-secondary">
    <!-- Language Switcher -->
    <div class="language-switcher">
        <div class="btn-group" role="group">
            <a href="<?= base_url('auth/set-language/english') ?>" 
               class="btn btn-sm btn-outline-secondary">
                🇺🇸 EN
            </a>
            <a href="<?= base_url('auth/set-language/bahasa') ?>" 
               class="btn btn-sm btn-outline-secondary">
                🇮🇩 ID
            </a>
        </div>
    </div>
    
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="<?= base_url() ?>" class="link-dark d-inline-block">
                    <img src="<?= base_url('adminlte/assets/img/AdminLTELogo.png') ?>"
                         alt="AdminLTE Logo"
                         height="120"
                         width="190">
                </a>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg"><?= app_lang('app.login') ?></p>
                
                <!-- Display Messages -->
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('/login') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input id="loginEmail" type="email" name="email" 
                                   class="form-control" 
                                   placeholder="<?= app_lang('app.email_address') ?>"
                                   value="<?= old('email', 'admin@example.com') ?>"
                                   required>
                            <label for="loginEmail"><?= app_lang('app.email_address') ?></label>
                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-envelope"></span>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input id="loginPassword" name="password" type="password" 
                                   class="form-control" 
                                   placeholder="<?= app_lang('app.member_password') ?>"
                                   value="<?= old('password', '12341234') ?>"
                                   required>
                            <label for="loginPassword"><?= app_lang('app.member_password') ?></label>
                        </div>
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-8">
                            <div class="form-check">
                                <!-- <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    <?= app_lang('app.remember_me') ?>
                                </label> -->
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <?= app_lang('app.login') ?>
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="text-center">
                    <p class="mb-1">
                        <a href="<?= base_url('/forgotpassword') ?>">
                            <?= app_lang('app.go_to_forgotpassword') ?>
                        </a>
                    </p>
                    <!-- <p class="mb-0">
                        <?= app_lang('app.no_account') ?>
                        <a href="<?= base_url('/register') ?>">
                            <?= app_lang('app.register_link') ?>
                        </a>
                    </p> -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('loginPassword');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>