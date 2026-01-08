<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Public File' ?> | <?= env('app.name', 'File Storage') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .public-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .public-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .file-info-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .preview-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
  <div class="app-wrapper">
    <!-- Public Header -->
    <!-- <header class="public-header">
        <div class="public-container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-file-alt me-2"></i>
                        <?= env('app.name', 'File Storage') ?>
                    </h1>
                    <p class="mb-0 opacity-75">Public File Viewer</p>
                </div>
                <div>
                    <?php if (session()->has('user')): ?>
                        <a href="<?= base_url('/dashboard') ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('/login') ?>" class="btn btn-outline-light btn-sm me-2">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header> -->

    <main class="public-container">
        <!-- File Information -->
        <?php if (isset($file)): ?>
        <div class="file-info-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2">
                        <i class="fas fa-file me-2"></i>
                        <?= esc($file['original_name']) ?>
                    </h4>
                    <div class="d-flex flex-wrap gap-3 text-muted">
                        <span><i class="fas fa-hdd me-1"></i> <?= format_file_size($file['size']) ?></span>
                        <span><i class="far fa-calendar me-1"></i> <?= date('M d, Y', strtotime($file['created_at'])) ?></span>
                        <span><i class="fas fa-download me-1"></i> <?= $file['download_count'] ?? 0 ?> downloads</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= base_url('view-file/download/' . $file['storage_name']) ?>" 
                       class="btn btn-primary px-4">
                        <i class="fas fa-download me-2"></i> Download File
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="preview-container">
            <?= $this->renderSection('content') ?>
        </div>

        <!-- Footer -->
        <?= $this->include('partials/footer') ?>
    </main>

  </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
  
</body>
</html>