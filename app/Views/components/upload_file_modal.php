<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFileModalLabel">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Files
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadFileForm" method="post" action="<?= base_url('/file/upload') ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- File Drop Zone -->
                    <div class="file-drop-zone mb-3" id="fileDropZone">
                        <div class="file-drop-content">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5>Drag and drop files here</h5>
                            <p class="text-muted">or click to browse</p>
                            <input type="file" name="file" id="fileInput" multiple style="display: none;">
                            <button type="button" 
                                class="btn btn-outline-primary mt-2" 
                                onclick="event.stopPropagation(); document.getElementById('fileInput').click();">
                                <i class="fas fa-folder-open me-1"></i> Browse Files
                            </button>

                        </div>
                    </div>
                    
                    <!-- Selected Files Preview -->
                    <div id="selectedFiles" class="mb-3" style="display: none;">
                        <h6>Selected Files:</h6>
                        <div id="fileList" class="list-group" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;"></div>
                    </div>
                    
                    <!-- Upload Options -->
                    <div class="form-group mb-3">
                        <label for="uploadFolder" class="form-label">Upload to Folder</label>
                        <select name="folder_id" id="uploadFolder" class="form-control">
                            <option value="">-- Root Directory --</option>
                            <?php foreach($folders as $folder): ?>
                                <option value="<?= $folder['id'] ?>" <?= (isset($current_folder) && $current_folder['id'] == $folder['id']) ? 'selected' : '' ?>>
                                    <?= esc($folder['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div id="uploadProgress" class="progress mb-3" style="display: none; height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    
                    <div id="uploadError" class="alert alert-danger" style="display: none;"></div>
                    <div id="uploadSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadFileBtn" disabled>
                        <i class="fas fa-upload me-1"></i> Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables for file handling
let selectedFilesArray = [];
let currentFolderId = null 

function showUploadFileModal(folderId = null) {
    // Reset form and state
    const form = document.getElementById('uploadFileForm');
    if (form) form.reset();
    
    document.getElementById('selectedFiles').style.display = 'none';
    document.getElementById('fileList').innerHTML = '';
    document.getElementById('uploadProgress').style.display = 'none';
    document.getElementById('uploadError').style.display = 'none';
    document.getElementById('uploadSuccess').style.display = 'none';
    document.getElementById('uploadFileBtn').disabled = true;
    selectedFilesArray = [];
    
    // Set folder if provided
    if (folderId) {
        currentFolderId = folderId
        document.getElementById('uploadFolder').value = folderId;
    }
    
    const modalElement = document.getElementById('uploadFileModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// File handling
document.addEventListener('DOMContentLoaded', function() {
    const fileDropZone = document.getElementById('fileDropZone');
    const fileInput = document.getElementById('fileInput');
    const selectedFiles = document.getElementById('selectedFiles');
    const fileList = document.getElementById('fileList');
    const uploadBtn = document.getElementById('uploadFileBtn');
    const uploadForm = document.getElementById('uploadFileForm');

    if (!fileDropZone || !fileInput || !uploadForm) return;

    // Drag and drop functionality
    fileDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileDropZone.classList.add('file-drop-zone-active');
    });

    fileDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileDropZone.classList.remove('file-drop-zone-active');
    });

    fileDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        fileDropZone.classList.remove('file-drop-zone-active');
        handleFiles(e.dataTransfer.files);
    });

    fileDropZone.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            selectedFilesArray = Array.from(files);
            updateFileList();
            uploadBtn.disabled = false;
        }
    }

    function updateFileList() {
        fileList.innerHTML = '';
        selectedFilesArray.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            fileItem.innerHTML = `
                <div>
                    <i class="fas fa-file me-2 text-muted"></i>
                    <span class="file-name">${file.name.length > 25 ? file.name.substring(0, 25) + '...' : file.name}</span>
                    <small class="text-muted d-block">${formatFileSize(file.size)}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
        selectedFiles.style.display = 'block';
    }

    // Form submission with AJAX
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (selectedFilesArray.length === 0) {
            const uploadError = document.getElementById('uploadError');
            uploadError.textContent = 'Please select at least one file.';
            uploadError.style.display = 'block';
            return;
        }

        const formData = new FormData();
        
        // Append all files
        selectedFilesArray.forEach(file => {
            formData.append('files[]', file);
        });
        
        // Append folder_id
        const folderSelect = document.getElementById('uploadFolder');
        formData.append('folder_id', currentFolderId);
        
        const submitBtn = document.getElementById('uploadFileBtn');
        const originalText = submitBtn.innerHTML;
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading...';
        uploadProgress.style.display = 'block';

        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        });

        xhr.addEventListener('load', function() {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    const uploadSuccess = document.getElementById('uploadSuccess');
                    uploadSuccess.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        Successfully uploaded ${response.uploaded_count || selectedFilesArray.length} file(s)!
                    `;
                    uploadSuccess.style.display = 'block';
                    document.getElementById('uploadError').style.display = 'none';
                    
                    // Reload page after delay
                    setTimeout(() => {
                        const modalElement = document.getElementById('uploadFileModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        modal.hide();
                        window.location.reload();
                    }, 1500);
                } else {
                    const uploadError = document.getElementById('uploadError');
                    uploadError.textContent = response.message || 'Error uploading files';
                    uploadError.style.display = 'block';
                }
            } catch (error) {
                const uploadError = document.getElementById('uploadError');
                uploadError.textContent = 'Error parsing server response';
                uploadError.style.display = 'block';
            }
        });

        xhr.addEventListener('error', function() {
            const uploadError = document.getElementById('uploadError');
            uploadError.textContent = 'An error occurred while uploading files';
            uploadError.style.display = 'block';
        });

        xhr.addEventListener('loadend', function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            uploadProgress.style.display = 'none';
            progressBar.style.width = '0%';
        });

        xhr.open('POST', uploadForm.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);

    });
});

// Global function to remove files
function removeFile(index) {
    selectedFilesArray.splice(index, 1);
    if (selectedFilesArray.length === 0) {
        document.getElementById('selectedFiles').style.display = 'none';
        document.getElementById('uploadFileBtn').disabled = true;
    }
    updateFileList();
}

// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Function to update file list (needs to be global)
function updateFileList() {
    const fileList = document.getElementById('fileList');
    if (!fileList) return;
    
    fileList.innerHTML = '';
    selectedFilesArray.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        fileItem.innerHTML = `
            <div>
                <i class="fas fa-file me-2 text-muted"></i>
                <span class="file-name">${file.name}</span>
                <small class="text-muted d-block">${formatFileSize(file.size)}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
        `;
        fileList.appendChild(fileItem);
    });
    document.getElementById('selectedFiles').style.display = selectedFilesArray.length > 0 ? 'block' : 'none';
}
</script>

<style>
.file-drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 5px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-drop-zone:hover,
.file-drop-zone-active {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.file-drop-content {
    color: #6c757d;
}

.file-name {
    word-break: break-all;
}
</style>