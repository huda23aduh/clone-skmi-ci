<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?= $this->include('components/toast') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user me-2"></i><?= app_lang('app.profile_title') ?>
            </h1>
            <p class="text-muted mb-0"><?= app_lang('app.profile_description') ?></p>
        </div>
        <a href="<?= base_url('activity-log') ?>" class="btn btn-primary">
            <i class="fas fa-history me-1"></i><?= app_lang('app.view_activity_log') ?>
        </a>
    </div>

    <div class="row">
        <!-- Left Column - Profile Info & Settings -->
        <div class="col-lg-4">
            <!-- Profile Image Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-id-card me-2"></i><?= lang('app.profile_information') ?>
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- Profile Image -->
                    <div class="mb-4">
                        <?php if (!empty($user['profile_image']) && file_exists(FCPATH . $user['profile_image'])): ?>
                            <img src="<?= base_url($user['profile_image']) ?>" 
                                class="rounded-circle shadow-sm profile-image" 
                                alt="Profile Image"
                                style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center shadow-sm" 
                                style="width: 150px; height: 150px; border: 3px solid #dee2e6;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- User Info -->
                    <h4 class="mb-1"><?= esc($user['name'] ?? 'No Name') ?></h4>
                    <p class="text-muted mb-3"><?= esc($user['email']) ?></p>
                    
                    <!-- Upload Image Form -->
                    <form action="<?= base_url('profile/upload-image') ?>" method="post" enctype="multipart/form-data" id="profileImageForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <input type="file" name="profile_image" class="form-control" 
                                  accept="image/jpeg,image/png,image/gif,image/webp" 
                                  id="profileImageInput" style="display: none;"
                                  onchange="handleImageSelection(this)">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="document.getElementById('profileImageInput').click()">
                                <i class="fas fa-camera me-1"></i>Change Photo
                            </button>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <small class="text-muted">Selected: <span id="fileName"></span></small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100" id="uploadImageBtn" style="display: none;">
                            <i class="fas fa-upload me-1"></i>Upload Image
                        </button>
                    </form>
                    
                    <!-- Image Requirements -->
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB
                    </small>
                </div>
            </div>

            <!-- Language Settings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-globe me-2"></i><?= lang('app.language_preference') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('profile/update-language') ?>" method="post" id="languageForm">
                        <?= csrf_field() ?>
                        <div class="mb-0">
                            <select name="language" class="form-select" onchange="document.getElementById('languageForm').submit()">
                                <?php foreach ($languages as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($user['language'] ?? 'english') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Stats -->
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-bar me-2"></i><?= lang('app.activity_statistics') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h3 class="text-primary mb-1"><?= $activityStats['total_activities'] ?></h3>
                                <small class="text-muted">Total Activities</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h3 class="text-success mb-1"><?= $activityStats['last_30_days'] ?></h3>
                                <small class="text-muted">Last 30 Days</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 bg-light">
                                <h3 class="text-info mb-1"><?= $activityStats['file_uploads'] ?></h3>
                                <small class="text-muted">File Uploads</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 bg-light">
                                <h3 class="text-warning mb-1"><?= $activityStats['file_downloads'] ?></h3>
                                <small class="text-muted">File Downloads</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Forms & Activity -->
        <div class="col-lg-8">
            <!-- Email Management Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-envelope me-2"></i>Email Management
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Add New Email Form -->
                    <div class="mb-4">
                        <h6 class="mb-3">Add Backup Email</h6>
                        <form action="<?= base_url('profile/email/add') ?>" method="post" id="addEmailForm">
                            <?= csrf_field() ?>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <input type="email" class="form-control" name="email" 
                                           placeholder="Enter backup email address" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>Add Email
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                A verification email will be sent to this address.
                            </small>
                        </form>
                    </div>

                    <!-- Email List -->
                    <h6 class="mb-3">Your Email Addresses</h6>
                    <div id="emailList">
                        <?php if (!empty($userEmails)): ?>
                            <div class="list-group">
                                <?php foreach ($userEmails as $email): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <?php if ($email['is_primary']): ?>
                                                    <span class="badge bg-primary">Primary</span>
                                                <?php elseif ($email['is_verified']): ?>
                                                    <span class="badge bg-success">Verified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= esc($email['email']) ?></div>
                                                <?php if (!$email['is_verified'] && !$email['is_primary']): ?>
                                                    <small class="text-muted">Verification required</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <?php if (!$email['is_primary'] && $email['is_verified']): ?>
                                                <button type="button" class="btn btn-outline-primary btn-sm set-primary-btn" 
                                                        data-email-id="<?= $email['id'] ?>" 
                                                        data-email="<?= esc($email['email']) ?>">
                                                    <i class="fas fa-star me-1"></i>Set Primary
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if (!$email['is_primary']): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm delete-email-btn" 
                                                        data-email-id="<?= $email['id'] ?>" 
                                                        data-email="<?= esc($email['email']) ?>">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-envelope fa-2x mb-2"></i>
                                <p>No backup emails added yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-edit me-2"></i><?= app_lang('app.edit_profile_info') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('profile/update') ?>" method="post" id="profileForm">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label"><?= app_lang('app.full_name') ?></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name', $user['name'] ?? '') ?>" 
                                       placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label"><?= app_lang('app.email_address') ?></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $user['email'] ?? '') ?>" 
                                       placeholder="Enter your email address" readonly>
                                <small class="text-muted">Primary email cannot be changed here. Use email management above.</small>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <h6 class="mb-3 text-muted"><?= app_lang('app.change_password_optional') ?></h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label"><?= app_lang('app.current_password') ?></label>
                                <input type="password" class="form-control" id="current_password" name="current_password"
                                       placeholder="<?= app_lang('app.current_password') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label"><?= app_lang('app.new_password') ?></label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                       placeholder="<?= app_lang('app.new_password') ?>" minlength="8">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="confirm_password" class="form-label"><?= app_lang('app.confirm_password') ?></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                       placeholder="<?= app_lang('app.confirm_password') ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><?= app_lang('app.profile_update_profile') ?>
                            </button>
                        </div>
                    </form>

                    <!-- Validation Errors -->
                    <?php if (isset($errors) && $errors): ?>
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Graph -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-line me-2"></i><?= app_lang('app.activity_overview') ?> (Last 30 Days)
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" data-period="30">30 Days</button>
                        <button type="button" class="btn btn-outline-primary" data-period="7">7 Days</button>
                        <button type="button" class="btn btn-outline-primary" data-period="90">90 Days</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="120"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-history me-2"></i><?= lang('app.recent_activities') ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentActivities)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 text-truncate" style="max-width: 70%;">
                                                <?= esc($activity['description']) ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= $this->include('partials/time_ago', ['datetime' => $activity['created_at']]) ?>
                                            </small>
                                        </div>
                                        <?php if ($activity['item_name']): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-file me-1"></i><?= esc($activity['item_name']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-secondary ms-2"><?= $activity['activity_type'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted"><?= lang('app.no_activities') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('profile/components/profile_script') ?>
<?= $this->endSection() ?>