<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?= $this->include('components/toast') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2"></i><?= app_lang('app.controlcenter') ?>
            </h1>
            <p class="text-muted mb-0"><?= app_lang('app.managemembersandsetting') ?></p>
        </div>
    </div>

    <!-- Members Management Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-users me-2"></i><?= app_lang('app.membersmanagement') ?>
            </h5>
        </div>
        <div class="card-body">
            <!-- Add Member Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <form id="addMemberForm" class="row g-3">
                        <?= csrf_field() ?>
                        <div class="col-md-4">
                            <label class="form-label"><?= app_lang('app.name') ?></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= app_lang('app.email_address') ?></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= app_lang('app.password') ?></label>
                            <input type="password" class="form-control" name="password" required minlength="8">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" id="addMemberBtn">
                                <i class="fas fa-plus me-1"></i><?= app_lang('app.create') ?>
                            </button>
                        </div>
                    </form>
                    <div id="formErrors" class="alert alert-danger mt-2" style="display: none;"></div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" 
                               placeholder="<?= app_lang('app.searchmember') ?>...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="all"><?= app_lang('app.allstatus') ?></option>
                        <option value="1"><?= app_lang('app.active') ?></option>
                        <option value="0"><?= app_lang('app.inactive') ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortSelect">
                        <option value="created_at_desc"><?= app_lang('app.newestfirst') ?></option>
                        <option value="created_at_asc"><?= app_lang('app.oldestfirst') ?></option>
                        <option value="name_asc"><?= app_lang('app.name') ?> A-Z</option>
                        <option value="name_desc"><?= app_lang('app.name') ?> Z-A</option>
                        <option value="email_asc"><?= app_lang('app.email_address') ?> A-Z</option>
                        <option value="email_desc"><?= app_lang('app.email_address') ?> Z-A</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="resetFilters">
                        <i class="fas fa-refresh me-1"></i>Reset
                    </button>
                </div>
            </div>

            <!-- Members Table -->
            <div id="membersTableContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading members...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('control_center/components/delete_user_modal') ?>

<?= $this->include('control_center/components/control_center_script') ?>


<?= $this->endSection() ?>
