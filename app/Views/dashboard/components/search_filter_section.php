<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-center">
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckboxMain">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" id="selectAllBtn">
                    <i class="fas fa-check-square me-1"></i> Select All
                </button>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search files and folders...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="typeFilter">
                    <option value="all">All Items</option>
                    <option value="folder">Folders Only</option>
                    <option value="file">Files Only</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="sortBy">
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="date_asc">Date (Oldest)</option>
                    <option value="date_desc">Date (Newest)</option>
                    <option value="size_asc">Size (Smallest)</option>
                    <option value="size_desc">Size (Largest)</option>
                </select>
            </div>
        </div>
    </div>
</div>