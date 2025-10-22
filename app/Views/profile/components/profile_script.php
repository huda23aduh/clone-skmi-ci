<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile Image Upload Handling
    function handleImageSelection(input) {
        const uploadBtn = document.getElementById('uploadImageBtn');
        const imagePreview = document.getElementById('imagePreview');
        const fileName = document.getElementById('fileName');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSize = file.size / 1024 / 1024; // MB
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            // Validate file type
            if (!validTypes.includes(file.type)) {
                showToast('Please select a valid image file (JPG, PNG, GIF, WebP).', 'error');
                input.value = '';
                return;
            }
            
            // Validate file size
            if (fileSize > 2) {
                showToast('Image size should not exceed 2MB.', 'error');
                input.value = '';
                return;
            }
            
            // Show file name and upload button
            fileName.textContent = file.name;
            imagePreview.style.display = 'block';
            uploadBtn.style.display = 'block';
            
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = document.querySelector('.profile-image');
                if (!img || img.tagName !== 'IMG') {
                    // Replace placeholder with image
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
                showToast('Please select an image to upload.', 'error');
                return;
            }
            
            // Show loading state
            const uploadBtn = document.getElementById('uploadImageBtn');
            const originalText = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
            uploadBtn.disabled = true;
            
            // The form will submit normally, we're just adding visual feedback
        });
    }

    // Make function global for inline onclick
    window.handleImageSelection = handleImageSelection;

    // Password Validation (existing code)
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;

            if (newPassword || confirmPassword || currentPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    showToast('Please enter your current password to change password.', 'error');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    showToast('New password and confirm password do not match.', 'error');
                    return;
                }

                if (newPassword.length < 8) {
                    e.preventDefault();
                    showToast('New password must be at least 8 characters long.', 'error');
                    return;
                }
            }
        });
    }

    // Activity Chart (existing code)
    let activityChart;
    const chartCtx = document.getElementById('activityChart');
    let currentPeriod = 30;

    function loadChartData(period = 30) {
        fetch(`<?= base_url('profile/activity-chart-data') ?>?period=${period}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && chartCtx) {
                    updateActivityChart(data.chart_data);
                }
            })
            .catch(error => console.error('Error loading chart data:', error));
    }

    function updateActivityChart(chartData) {
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

    // Period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            currentPeriod = period;
            
            document.querySelectorAll('[data-period]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            loadChartData(period);
        });
    });

    loadChartData(currentPeriod);

    // Toast notification function
    function showToast(message, type = 'success') {
        // Check if Bootstrap toast is available
        if (typeof bootstrap === 'undefined') {
            alert(message);
            return;
        }

        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            // Create toast container if it doesn't exist
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        document.querySelector('.toast-container').insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    // Make showToast globally available
    window.showToast = showToast;
});
</script>