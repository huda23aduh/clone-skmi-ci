<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-pie me-2"></i><?= lang('app.storage_summary') ?>
            </h1>
                <p class="text-muted mb-0"><?= lang('app.overview_storage_usage') ?></p>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('/') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i><?= lang('app.back_to_dashboard') ?>
            </a>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading summary data...</p>
    </div>

    <!-- Summary Content -->
    <div id="summaryContent" style="display: none;">
        <!-- Basic Stats Row -->
        <div class="row mb-4">
            <!-- Total Files -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <?= lang('app.total_files') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFiles">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Folders -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <?= lang('app.total_folders') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFolders">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Used -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    <?= lang('app.storage_used') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="storageUsed">0 MB</div>
                                <div class="text-xs text-muted mt-1" id="storageMax">of 500 GB</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hdd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Percentage -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    <?= lang('app.storage_used') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="storagePercentage">0%</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div id="storageProgressBar" class="progress-bar bg-warning" 
                                         role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- File Type Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-pie me-2"></i><?= lang('app.file_type_distribution') ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="fileTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploads Per Month -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-line me-2"></i><?= str_replace('{year}', date('Y'), lang('app.uploads_in_year')) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="uploadsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Usage Per Month -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-area me-2"></i><?= str_replace('{year}', date('Y'), lang('app.storage_usage_in_year')) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="storageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const summaryContent = document.getElementById('summaryContent');

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Load summary data
    async function loadSummaryData() {
        try {
            const response = await fetch('<?= base_url('summary/data') ?>');
            const result = await response.json();

            if (result.success) {
                displaySummaryData(result.data);
                summaryContent.style.display = 'block';
                loadingSpinner.style.display = 'none';
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading summary data:', error);
            loadingSpinner.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load summary data: ${error.message}
                </div>
            `;
        }
    }

    // Display summary data
    function displaySummaryData(data) {
        // Basic stats
        document.getElementById('totalFiles').textContent = data.totalFiles.toLocaleString();
        document.getElementById('totalFolders').textContent = data.totalFolders.toLocaleString();
        document.getElementById('storageUsed').textContent = formatFileSize(data.totalStorageUsed);
        document.getElementById('storagePercentage').textContent = data.storagePercentage + '%';

        // Format percentage to 4 decimal places
        const formattedPercentage = formatPercentage(data.storagePercentage);
        document.getElementById('storagePercentage').textContent = formattedPercentage;

        // Charts
        createFileTypeChart(data.fileTypeDistribution);
        createUploadsChart(data.uploadsPerMonth);
        createStorageChart(data.storagePerMonth);
    }

    // Format percentage to 4 decimal places
    function formatPercentage(percentage) {
        if (percentage === 0) return '0%';
        
        // Round to 4 decimal places
        const rounded = Math.round(percentage * 10000) / 10000;
        
        // If after rounding it becomes 0, show 0.0001% as minimum
        if (rounded === 0) {
            return '0.0001%';
        }
        
        // Ensure exactly 4 decimal places
        return rounded.toFixed(4) + '%';
    }

    // File Type Distribution Chart
    function createFileTypeChart(distribution) {
        const ctx = document.getElementById('fileTypeChart').getContext('2d');
        const labels = Object.keys(distribution).map(key => 
            key.charAt(0).toUpperCase() + key.slice(1)
        );
        const counts = Object.values(distribution).map(item => item.count);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} files (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Uploads Per Month Chart
    function createUploadsChart(uploadsData) {
        const ctx = document.getElementById('uploadsChart').getContext('2d');
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const uploads = Object.values(uploadsData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'File Uploads',
                    data: uploads,
                    backgroundColor: '#36A2EB',
                    borderColor: '#36A2EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Uploads'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    }
                }
            }
        });
    }

    // Storage Usage Per Month Chart
    function createStorageChart(storageData) {
        const ctx = document.getElementById('storageChart').getContext('2d');
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const storageMB = Object.values(storageData).map(bytes => Math.round(bytes / (1024 * 1024)));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Storage Used (MB)',
                    data: storageMB,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Storage Used (MB)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    }
                }
            }
        });
    }

    // Initialize
    loadSummaryData();
});
</script>

<?= $this->endSection() ?>