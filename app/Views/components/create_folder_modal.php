<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">
                    <i class="fas fa-folder-plus mr-2"></i>Create New Folder
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createFolderForm" method="post" action="<?= base_url('/folder/create') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="folderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderName" name="name" placeholder="Enter folder name" required>
                        <small class="form-text text-muted">Folder names cannot contain special characters.</small>
                    </div>
                    
                    <?php if(isset($current_folder) && $current_folder): ?>
                        <input type="hidden" name="parent_id" value="<?= $current_folder['id'] ?>">
                    <?php endif; ?>
                    
                    <div id="folderError" class="alert alert-danger mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createFolderBtn">
                        <i class="fas fa-plus me-1"></i> Create Folder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCreateFolderModal(parentId = null) {
    // Reset form
    const form = document.getElementById('createFolderForm');
    if (form) form.reset();
    
    const folderError = document.getElementById('folderError');
    if (folderError) {
        folderError.style.display = 'none';
        folderError.textContent = '';
    }
    
    // Set parent ID if provided
    if (parentId) {
        // Remove existing parent_id input if any
        const existingParentId = form.querySelector('input[name="parent_id"]');
        if (existingParentId) existingParentId.remove();
        
        // Add new parent_id input
        const parentInput = document.createElement('input');
        parentInput.type = 'hidden';
        parentInput.name = 'parent_id';
        parentInput.value = parentId;
        form.appendChild(parentInput);
    }
    
    const modalElement = document.getElementById('createFolderModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    const folderName = document.getElementById('folderName');
    if (folderName) folderName.focus();
}

// Form submission with AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createFolderForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('createFolderBtn');
            const folderError = document.getElementById('folderError');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
            
            fetch(form.action, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modalElement = document.getElementById('createFolderModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                    
                    if (typeof showToast === 'function') {
                        showToast('success', 'Folder created successfully!');
                    } else {
                        alert('Folder created successfully!');
                    }
                    
                    // Reload page after delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (folderError) {
                        folderError.textContent = data.message || 'Error creating folder';
                        folderError.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (folderError) {
                    folderError.textContent = 'An error occurred while creating the folder';
                    folderError.style.display = 'block';
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
    
    // Clear error when typing
    const folderName = document.getElementById('folderName');
    if (folderName) {
        folderName.addEventListener('input', function() {
            const folderError = document.getElementById('folderError');
            if (folderError) {
                folderError.style.display = 'none';
                folderError.textContent = '';
            }
        });
    }
});
</script>