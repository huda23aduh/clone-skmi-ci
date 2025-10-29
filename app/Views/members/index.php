<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Include Components -->
<?= $this->include('components/toast') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2"></i><?= lang('app.member_management') ?>
            </h1>
            <p class="text-muted mb-0"><?= lang('app.manage_monitor_members') ?></p>
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
        <p class="mt-2 text-muted">Loading member data...</p>
    </div>

    <!-- Members Content -->
    <div id="membersContent" style="display: none;">
        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <!-- Total Members -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <?= lang('app.total_active_members') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMembers">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Storage Used -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <?= lang('app.total_storage_used') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStorageUsed">0 MB</div>
                                <div class="text-xs text-muted mt-1"><?= lang('app.across_all_members') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hdd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Storage Per Member -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    <?= lang('app.avg_storage_per_member') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgStoragePerMember">0 MB</div>
                                <div class="text-xs text-muted mt-1">Average usage</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Files -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    <?= lang('app.total_files') ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFiles">0</div>
                                <div class="text-xs text-muted mt-1"><?= lang('app.across_all_members') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-list me-2"></i><?= lang('app.member_list') ?>
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" class="form-control" placeholder="Search members..." id="searchMembers">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3"><?= lang('app.member') ?></th>
                                        <th><?= lang('app.join_date') ?></th>
                                        <th><?= lang('app.last_login') ?></th>
                                        <th><?= lang('app.storage_used') ?></th>
                                        <th><?= lang('app.files') ?></th>
                                        <th class="text-center"><?= lang('app.status') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="membersTableBody">
                                    <!-- Members will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center p-3 border-top">
                            <div class="text-muted small" id="paginationInfo">
                                Showing 0 to 0 of 0 entries
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="pagination">
                                    <!-- Pagination will be loaded here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-line me-2"></i><?= str_replace('{days}', '30', lang('app.member_activities')) ?>
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary active" data-days="7">7 Days</button>
                            <button type="button" class="btn btn-outline-primary" data-days="30">30 Days</button>
                            <button type="button" class="btn btn-outline-primary" data-days="90">90 Days</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="activitiesChart"></canvas>
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
    const membersContent = document.getElementById('membersContent');
    const searchMembers = document.getElementById('searchMembers');
    const searchBtn = document.getElementById('searchBtn');
    
    let currentPage = 1;
    let currentSearch = '';
    let currentDays = 30;

    // Format file size
    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Load members data
    async function loadMembersData(page = 1, search = '') {
        try {
            const params = new URLSearchParams({
                page: page,
                per_page: 10,
                search: search
            });

            const response = await fetch(`/members/data?${params}`);
            const result = await response.json();

            if (result.success) {
                displayMembersData(result.data);
                membersContent.style.display = 'block';
                loadingSpinner.style.display = 'none';
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error loading members data:', error);
            loadingSpinner.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load members data: ${error.message}
                </div>
            `;
        }
    }

    // Display members data
    function displayMembersData(data) {
        // Update stats cards
        document.getElementById('totalMembers').textContent = data.total_members.toLocaleString();
        document.getElementById('totalStorageUsed').textContent = formatBytes(data.total_storage_used);
        document.getElementById('avgStoragePerMember').textContent = formatBytes(
            data.total_members > 0 ? data.total_storage_used / data.total_members : 0
        );
        
        // Calculate total files
        const totalFiles = data.members.reduce((sum, member) => sum + member.file_count, 0);
        document.getElementById('totalFiles').textContent = totalFiles.toLocaleString();

        // Update members table
        updateMembersTable(data.members);
        
        // Update pagination
        updatePagination(data.pagination);
    }

    // Update members table
    function updateMembersTable(members) {
        const tbody = document.getElementById('membersTableBody');
        
        if (members.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted"> <!-- Changed colspan back to 6 -->
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>No members found</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        members.forEach(member => {
            const statusBadge = member.status === 'Active' ? 
                '<span class="badge bg-success">Active</span>' : 
                '<span class="badge bg-secondary">Inactive</span>';
            
            html += `
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${member.name}</div>
                                <small class="text-muted">${member.email}</small>
                            </div>
                        </div>
                    </td>
                    <td>${member.join_date}</td>
                    <td>${member.last_login_display}</td>
                    <td>
                        <div class="fw-semibold">${member.storage_used_display}</div>
                        <small class="text-muted">${member.file_count} files</small>
                    </td>
                    <td>${member.file_count.toLocaleString()}</td>
                    <td class="text-center">
                        ${statusBadge}
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Update pagination
    function updatePagination(pagination) {
        const paginationElement = document.getElementById('pagination');
        const infoElement = document.getElementById('paginationInfo');
        
        // Update info
        const start = ((pagination.page - 1) * pagination.per_page) + 1;
        const end = Math.min(pagination.page * pagination.per_page, pagination.total);
        infoElement.textContent = `Showing ${start} to ${end} of ${pagination.total.toLocaleString()} entries`;
        
        // Update pagination buttons
        let html = '';
        
        // Previous button
        html += `
            <li class="page-item ${pagination.page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.page - 1}">Previous</a>
            </li>
        `;
        
        // Page numbers
        const totalPages = pagination.total_pages;
        const currentPage = pagination.page;
        const maxVisiblePages = 5;
        
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Next button
        html += `
            <li class="page-item ${pagination.page === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.page + 1}">Next</a>
            </li>
        `;
        
        paginationElement.innerHTML = html;
        
        // Add event listeners to pagination buttons
        paginationElement.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    loadMembersData(currentPage, currentSearch);
                }
            });
        });
    }

    // Load activities chart
    async function loadActivitiesChart(days = 30) {
        try {
            const response = await fetch(`/members/activities?days=${days}`);
            const result = await response.json();

            if (result.success) {
                createActivitiesChart(result.data.activities, days);
            }
        } catch (error) {
            console.error('Error loading activities chart:', error);
        }
    }

    // Create activities chart
    function createActivitiesChart(activities, days) {
        const ctx = document.getElementById('activitiesChart').getContext('2d');
        
        // Format dates for labels
        const labels = activities.map(item => {
            const date = new Date(item.date);
            return days <= 30 ? 
                date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) :
                date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });

        const datasets = [
            {
                label: 'File Uploads',
                data: activities.map(item => item.file_upload),
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'File Downloads',
                data: activities.map(item => item.file_download),
                borderColor: '#4BC0C0',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'File Previews',
                data: activities.map(item => item.file_preview),
                borderColor: '#FF6384',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Total Activities',
                data: activities.map(item => item.total),
                borderColor: '#FF9F40',
                backgroundColor: 'rgba(255, 159, 64, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }
        ];

        // Destroy existing chart if it exists
        if (window.activitiesChart) {
            window.activitiesChart.destroy();
        }

        window.activitiesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Activities'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    }

    // Event listeners
    searchBtn.addEventListener('click', function() {
        currentSearch = searchMembers.value;
        currentPage = 1;
        loadMembersData(currentPage, currentSearch);
    });

    searchMembers.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            currentSearch = this.value;
            currentPage = 1;
            loadMembersData(currentPage, currentSearch);
        }
    });

    // Period buttons for activities chart
    document.querySelectorAll('[data-days]').forEach(button => {
        button.addEventListener('click', function() {
            const days = parseInt(this.getAttribute('data-days'));
            currentDays = days;
            
            document.querySelectorAll('[data-days]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            loadActivitiesChart(days);
        });
    });

    // Initialize
    loadMembersData(currentPage, currentSearch);
    loadActivitiesChart(currentDays);
});
</script>

<?= $this->endSection() ?>