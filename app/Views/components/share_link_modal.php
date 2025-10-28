<!-- Share Link Modal -->
<div class="modal fade" id="shareLinkModal" tabindex="-1" aria-labelledby="shareLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareLinkModalLabel">
                    <i class="fas fa-link me-2"></i>Share Link
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="shareItemName" class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="shareItemName" readonly>
                </div>
                <div class="mb-3">
                    <label for="shareLink" class="form-label">Shareable Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyShareLinkBtn">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">
                        Anyone with this link can view this item.
                    </small>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="enablePasswordProtection">
                        <label class="form-check-label" for="enablePasswordProtection">
                            Enable password protection
                        </label>
                    </div>
                </div>
                <div id="passwordSection" style="display: none;">
                    <div class="mb-3">
                        <label for="sharePassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="sharePassword" placeholder="Enter password">
                    </div>
                    <div class="mb-3">
                        <label for="confirmSharePassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmSharePassword" placeholder="Confirm password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="generateShareLinkBtn">
                    <i class="fas fa-key me-1"></i>Generate Link
                </button>
            </div>
        </div>
    </div>
</div>