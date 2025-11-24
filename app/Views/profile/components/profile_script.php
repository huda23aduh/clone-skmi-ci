<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart Variables - Define at the top
    let activityChart = null;
    let chartCtx = null;

    // Initialize chart context first
    function initializeChartContext() {
        const chartElement = document.getElementById('activityChart');
        if (chartElement) {
            chartCtx = chartElement.getContext('2d');
            console.log('Chart context initialized');
        } else {
            console.error('Activity chart element not found');
        }
    }

    // Load Activity Chart Function
    function loadActivityChart(period = 30, startDate = null, endDate = null) {
        // Make sure chart context is initialized
        if (!chartCtx) {
            initializeChartContext();
        }

        // Show loading state if chart context exists
        if (chartCtx && chartCtx.canvas) {
            chartCtx.clearRect(0, 0, chartCtx.canvas.width, chartCtx.canvas.height);
            chartCtx.fillStyle = '#6c757d';
            chartCtx.font = '14px Arial';
            chartCtx.textAlign = 'center';
            chartCtx.fillText('Loading chart data...', chartCtx.canvas.width / 2, chartCtx.canvas.height / 2);
        }

        // Build URL with parameters
        let url = `<?= base_url('profile/activity-chart-data') ?>?period=${period}`;
        if (startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        console.log('Loading chart from URL:', url);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateActivityChart(data.chart_data);
                } else {
                    console.error('Failed to load chart data:', data.message);
                    showChartError('Failed to load chart data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                showChartError('Error loading chart data');
            });
    }

    function showChartError(message) {
        if (!chartCtx || !chartCtx.canvas) return;
        
        chartCtx.clearRect(0, 0, chartCtx.canvas.width, chartCtx.canvas.height);
        chartCtx.fillStyle = '#dc3545';
        chartCtx.font = '14px Arial';
        chartCtx.textAlign = 'center';
        chartCtx.fillText(message, chartCtx.canvas.width / 2, chartCtx.canvas.height / 2);
    }

    function updateActivityChart(chartData) {
        if (!chartCtx) {
            console.error('Chart context not available');
            return;
        }

        // Destroy existing chart if it exists
        if (activityChart) {
            activityChart.destroy();
        }

        activityChart = new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: chartData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Activities',
                    data: chartData.map(item => item.count),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    // Simple Date Range Implementation
    function initializeSimpleDateRange() {
        console.log('Initializing date range...');
        
        // Initialize chart context first
        initializeChartContext();
        
        // Set default dates (last 30 days)
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 29);
        
        // Format dates for input fields (YYYY-MM-DD)
        const formatDateForInput = (date) => {
            return date.toISOString().split('T')[0];
        };
        
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const applyButton = document.getElementById('applyDateRange');
        const clearButton = document.getElementById('clearDateRange');
        const dateRangeLabel = document.getElementById('dateRangeLabel');

        if (startDateInput && endDateInput) {
            startDateInput.value = formatDateForInput(startDate);
            endDateInput.value = formatDateForInput(endDate);
            
            console.log('Date inputs found and set:', startDateInput.value, endDateInput.value);
        } else {
            console.error('Date input elements not found');
            return;
        }

        // Apply button handler
        if (applyButton) {
            applyButton.addEventListener('click', function() {
                console.log('Apply button clicked');
                const startDateVal = startDateInput.value;
                const endDateVal = endDateInput.value;
                
                if (startDateVal && endDateVal) {
                    console.log('Loading chart with custom range:', startDateVal, endDateVal);
                    
                    // Update chart with custom date range
                    loadActivityChart('custom', startDateVal, endDateVal);
                    
                    // Update label
                    const startFormatted = new Date(startDateVal).toLocaleDateString('en', { month: 'short', day: 'numeric' });
                    const endFormatted = new Date(endDateVal).toLocaleDateString('en', { month: 'short', day: 'numeric', year: 'numeric' });
                    if (dateRangeLabel) {
                        dateRangeLabel.textContent = '(' + startFormatted + ' - ' + endFormatted + ')';
                    }
                    
                    showToast('success', 'Chart updated with selected date range');
                } else {
                    showToast('error', 'Please select both start and end dates');
                }
            });
        } else {
            console.error('Apply button not found');
        }

        // Clear button handler
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                console.log('Clear button clicked');
                // Reset to default (last 30 days)
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - 29);
                
                startDateInput.value = formatDateForInput(startDate);
                endDateInput.value = formatDateForInput(endDate);
                
                // Load default chart
                loadActivityChart(30);
                
                // Update label
                if (dateRangeLabel) {
                    dateRangeLabel.textContent = '(Last 30 Days)';
                }
                
                showToast('info', 'Date range reset to last 30 days');
            });
        } else {
            console.error('Clear button not found');
        }

        // Load initial chart data
        loadActivityChart(30);
    }

    // Initialize date range functionality
    initializeSimpleDateRange();

    // Rest of your existing code (Profile Image, Email Management, etc.)
    // Profile Image Upload Handling
    function handleImageSelection(input) {
        const uploadBtn = document.getElementById('uploadImageBtn');
        const imagePreview = document.getElementById('imagePreview');
        const fileName = document.getElementById('fileName');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSize = file.size / 1024 / 1024; // MB
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!validTypes.includes(file.type)) {
                showToast('error', 'Please select a valid image file (JPG, PNG, GIF, WebP).');
                input.value = '';
                return;
            }
            
            if (fileSize > 2) {
                showToast('error', 'Image size should not exceed 2MB.');
                input.value = '';
                return;
            }
            
            fileName.textContent = file.name;
            imagePreview.style.display = 'block';
            uploadBtn.style.display = 'block';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = document.querySelector('.profile-image');
                if (!img || img.tagName !== 'IMG') {
                    const placeholder = document.querySelector('.rounded-circle.bg-secondary');
                    if (placeholder) {
                        img = document.createElement('img');
                        img.className = 'rounded-circle shadow-sm profile-image';
                        img.style.width = '150px';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.style.border = '3px solid #dee2e6';
                        placeholder.parentNode.replaceChild(img, placeholder);
                    }
                }
                if (img) {
                    img.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Form submission handling
    const profileImageForm = document.getElementById('profileImageForm');
    if (profileImageForm) {
        profileImageForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('profileImageInput');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                showToast('error', 'Please select an image to upload.');
                return;
            }
            
            const uploadBtn = document.getElementById('uploadImageBtn');
            const originalText = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
            uploadBtn.disabled = true;
        });
    }

    // Make function global for inline onclick
    window.handleImageSelection = handleImageSelection;

    // Password Validation
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;

            if (newPassword || confirmPassword || currentPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    showToast('error', 'Please enter your current password to change password.');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    showToast('error', 'New password and confirm password do not match.');
                    return;
                }

                if (newPassword.length < 8) {
                    e.preventDefault();
                    showToast('error', 'New password must be at least 8 characters long.');
                    return;
                }
            }
        });
    }

    // Email management functionality
    const addEmailForm = document.getElementById('addEmailForm');
    if (addEmailForm) {
        addEmailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addNewEmail(this);
        });
    }

    // Set primary email
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('set-primary-btn') || e.target.closest('.set-primary-btn')) {
            const btn = e.target.classList.contains('set-primary-btn') ? e.target : e.target.closest('.set-primary-btn');
            const emailId = btn.getAttribute('data-email-id');
            const email = btn.getAttribute('data-email');
            setPrimaryEmail(emailId, email, btn);
        }
    });

    // Delete email
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-email-btn') || e.target.closest('.delete-email-btn')) {
            const btn = e.target.classList.contains('delete-email-btn') ? e.target : e.target.closest('.delete-email-btn');
            const emailId = btn.getAttribute('data-email-id');
            const email = btn.getAttribute('data-email');
            deleteEmail(emailId, email, btn);
        }
    });

    async function addNewEmail(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                form.reset();
                
                // Fetch updated email list from the server
                await refreshEmailList();
            } else {
                showToast('error', result.message);
            }
        } catch (error) {
            console.error('Error adding email:', error);
            showToast('error', 'Error adding email');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }

    async function refreshEmailList() {
        try {
            const response = await fetch('/profile/emails', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                updateEmailListUI(result.emails);
            } else {
                console.error('Failed to refresh email list');
                showToast('error', 'Failed to refresh email list');
            }
        } catch (error) {
            console.error('Error refreshing email list:', error);
            showToast('error', 'Error refreshing email list');
            // Fallback: reload the page if AJAX fails
            setTimeout(() => location.reload(), 1500);
        }
    }

    function updateEmailListUI(emails) {
        const emailList = document.getElementById('emailList');
        
        if (!emails || emails.length === 0) {
            emailList.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-envelope fa-2x mb-2"></i>
                    <p>No backup emails added yet.</p>
                </div>
            `;
            return;
        }
        
        let emailListHTML = '<div class="list-group">';
        
        emails.forEach(email => {
            // Convert string values to boolean if needed
            const isPrimary = email.is_primary === true || email.is_primary === '1' || email.is_primary === 1;
            const isVerified = email.is_verified === true || email.is_verified === '1' || email.is_verified === 1;
            
            emailListHTML += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            ${isPrimary ? 
                                '<span class="badge bg-primary">Primary</span>' : 
                                isVerified ? 
                                '<span class="badge bg-success">Verified</span>' : 
                                '<span class="badge bg-warning">Pending</span>'
                            }
                        </div>
                        <div>
                            <div class="fw-semibold">${escapeHtml(email.email)}</div>
                            ${!isVerified && !isPrimary ? 
                                '<small class="text-muted">Verification required</small>' : 
                                ''
                            }
                        </div>
                    </div>
                    <div class="btn-group">
                        ${!isPrimary && isVerified ? 
                            `<button type="button" class="btn btn-outline-primary btn-sm set-primary-btn" 
                                    data-email-id="${email.id}" 
                                    data-email="${escapeHtml(email.email)}">
                                <i class="fas fa-star me-1"></i>Set Primary
                            </button>` : 
                            ''
                        }
                        
                        ${!isPrimary ? 
                            `<button type="button" class="btn btn-outline-danger btn-sm delete-email-btn" 
                                    data-email-id="${email.id}" 
                                    data-email="${escapeHtml(email.email)}">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>` : 
                            ''
                        }
                    </div>
                </div>
            `;
        });
        
        emailListHTML += '</div>';
        emailList.innerHTML = emailListHTML;
    }

    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    async function setPrimaryEmail(emailId, email, button) {
        if (!confirm(`Set ${email} as your primary email?`)) {
            return;
        }

        try {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Setting...';

            const response = await fetch(`/profile/email/set-primary/${emailId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                // Refresh the email list to show updated primary status
                await refreshEmailList();
            } else {
                showToast('error', result.message);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-star me-1"></i>Set Primary';
            }
        } catch (error) {
            console.error('Error setting primary email:', error);
            showToast('error', 'Error setting primary email');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-star me-1"></i>Set Primary';
        }
    }

    async function deleteEmail(emailId, email, button) {
        if (!confirm(`Delete ${email}? This action cannot be undone.`)) {
            return;
        }

        try {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';

            const response = await fetch(`/profile/email/remove/${emailId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', result.message);
                // Remove the email item from the list
                button.closest('.list-group-item').remove();
                
                // If no emails left, show empty state
                const emailList = document.getElementById('emailList');
                const emailItems = emailList.querySelectorAll('.list-group-item');
                if (emailItems.length === 0) {
                    emailList.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-envelope fa-2x mb-2"></i>
                            <p>No backup emails added yet.</p>
                        </div>
                    `;
                }
            } else {
                showToast('error', result.message);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
            }
        } catch (error) {
            console.error('Error deleting email:', error);
            showToast('error', 'Error deleting email');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
        }
    }
});
</script>