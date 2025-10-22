<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history me-2"></i>Activity Log
            </h1>
            <p class="text-muted mb-0">Track your file management activities</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i class="fas fa-broom me-1"></i>Clear Logs
            </button>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('activity-log/export?format=csv') ?>">
                            <i class="fas fa-file-csv me-2"></i>Export as CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('activity-log/export?format=json') ?>">
                            <i class="fas fa-file-code me-2"></i>Export as JSON
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Activities
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_activities']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Last 30 Days
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['last_30_days']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['today']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                File Uploads
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['file_uploads']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-upload fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('activity-log') ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search Activities</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" name="search" 
                               value="<?= esc($search) ?>" placeholder="Search by activity, file name, or description...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter by Type</label>
                    <select class="form-select" name="filter">
                        <?php foreach ($activityTypes as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $filter === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Activities Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-list me-2"></i>Activity History
                <span class="badge bg-secondary ms-2"><?= number_format($totalActivities) ?> activities</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($activities)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($activities as $activity): 
                        $icon = $this->include('activity_log/activity_icon', ['activity' => $activity]);
                        $badgeClass = $this->include('activity_log/activity_badge', ['activity' => $activity]);
                    ?>
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3 mt-1">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-dark" 
                                         style="width: 40px; height: 40px;">
                                        <i class="<?= $icon ?>"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0"><?= esc($activity['description']) ?></h6>
                                        <span class="badge <?= $badgeClass ?> ms-2">
                                            <?= ucfirst(str_replace('_', ' ', $activity['activity_type'])) ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($activity['item_name'])): ?>
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-tag me-1"></i>
                                            <?= esc($activity['item_name']) ?>
                                            <?php if ($activity['item_type']): ?>
                                                <span class="badge bg-light text-dark ms-1">
                                                    <?= ucfirst($activity['item_type']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= formatDateTimeGMT7_24h($activity['created_at']) ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-globe me-1"></i>
                                            <?= $activity['ip_address'] ?? 'Unknown' ?>
                                        </small>
                                    </div>

                                    <?php if (!empty($activity['metadata'])): 
                                        $metadata = json_decode($activity['metadata'], true); ?>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <?php if (isset($metadata['file_size'])): ?>
                                                    Size: <?= formatFileSize($metadata['file_size']) ?>
                                                <?php endif; ?>
                                                <?php if (isset($metadata['extracted_files_count'])): ?>
                                                    • Extracted files: <?= $metadata['extracted_files_count'] ?>
                                                <?php endif; ?>
                                                <?php if (isset($metadata['zipped_items_count'])): ?>
                                                    • Items zipped: <?= $metadata['zipped_items_count'] ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pager->getPageCount() > 1): ?>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing <?= (($currentPage - 1) * $perPage) + 1 ?> to 
                                <?= min($currentPage * $perPage, $totalActivities) ?> of 
                                <?= number_format($totalActivities) ?> entries
                            </div>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No activities found</h5>
                    <p class="text-muted mb-0">
                        <?php if (!empty($search) || $filter !== 'all'): ?>
                            Try adjusting your search or filter criteria
                        <?php else: ?>
                            Your activity log will appear here as you use the system
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-broom me-2"></i>Clear Activity Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= base_url('activity-log/clear') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>This will permanently delete activity logs older than the selected period.</p>
                    <div class="mb-3">
                        <label class="form-label">Clear logs older than:</label>
                        <select class="form-select" name="days">
                            <option value="7">7 days</option>
                            <option value="30" selected>30 days</option>
                            <option value="90">90 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clear Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>