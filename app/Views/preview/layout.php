<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'File Preview' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github.min.css" rel="stylesheet">
    <style>
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .preview-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .preview-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .file-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .preview-iframe {
            width: 100%;
            border: none;
            background: white;
        }
        .code-container {
            max-height: 70vh;
            overflow: auto;
        }
        .media-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
            background: #f8f9fa;
        }
        .media-container img,
        .media-container video,
        .media-container audio {
            max-width: 100%;
            max-height: 70vh;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <!-- Header -->
        <div class="preview-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><?= $title ?? 'File Preview' ?></h4>
                    <p class="text-muted mb-0" id="fileInfo">
                        <i class="fas fa-file me-1"></i>
                        <?= $file['original_name'] ?? 'Unknown file' ?>
                        <?php if (isset($file['size'])): ?>
                            <span class="ms-2">
                                (<?= format_file_size($file['size']) ?>)
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('file/download/' . ($file['id'] ?? '')) ?>" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                    <button onclick="window.close()" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Content -->
        <div class="preview-content">
            <?= $this->renderSection('preview_content') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>
        // Initialize syntax highlighting
        if (typeof hljs !== 'undefined') {
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('pre code').forEach((block) => {
                    hljs.highlightElement(block);
                });
            });
        }

        // Handle escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>