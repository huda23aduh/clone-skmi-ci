<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?= $this->include('components/toast') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user me-2"></i>My Profile
            </h1>
            <p class="text-muted mb-0">Manage your profile information and preferences</p>
        </div>
        <a href="<?= base_url('activity-log') ?>" class="btn btn-primary">
            <i class="fas fa-history me-1"></i>View Activity Log
        </a>
    </div>

    <div class="row">
        <!-- Left Column - Profile Info & Settings -->
        <div class="col-lg-4">
            <!-- Profile Image Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-id-card me-2"></i>Profile Information
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
                        <i class="fas fa-globe me-2"></i>Language Preference
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
                        <i class="fas fa-chart-bar me-2"></i>Activity Statistics
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
            <!-- Edit Profile Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-edit me-2"></i>Edit Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('profile/update') ?>" method="post" id="profileForm">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name', $user['name'] ?? '') ?>" 
                                       placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $user['email'] ?? '') ?>" 
                                       placeholder="Enter your email address">
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <h6 class="mb-3 text-muted">Change Password (Optional)</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password"
                                       placeholder="Current password">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                       placeholder="New password" minlength="8">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                       placeholder="Confirm new password">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Profile
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
                        <i class="fas fa-chart-line me-2"></i>Activity Overview (Last 30 Days)
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
                        <i class="fas fa-history me-2"></i>Recent Activities
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
                            <p class="text-muted">No recent activities found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('profile/components/profile_script') ?>
<?= $this->endSection() ?>