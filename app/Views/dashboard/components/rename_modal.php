<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameModalLabel">Rename Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renameForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="item_id" id="renameItemId">
                    <input type="hidden" name="item_type" id="renameItemType">
                    
                    <div class="mb-3">
                        <label for="newName" class="form-label">New Name</label>
                        <input type="text" class="form-control" id="newName" name="new_name" required>
                        <div class="form-text">Enter the new name for the item.</div>
                    </div>
                    
                    <div id="renameError" class="alert alert-danger mt-3" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmRename">Rename</button>
            </div>
        </div>
    </div>
</div>