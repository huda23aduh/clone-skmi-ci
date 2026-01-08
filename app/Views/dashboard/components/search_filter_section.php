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
                    <i class="fas fa-check-square me-1"></i> 
                    <?= app_lang('app.selectall') ?>
                </button>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="<?= lang('app.searchfileandfolder') ?>...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="typeFilter">
                    <option value="all">
                        <?= app_lang('app.allitems') ?>
                    </option>
                    <option value="folder">
                        <?= app_lang('app.folderonly') ?>
                    </option>
                    <option value="file">
                        <?= app_lang('app.fileonly') ?>
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="sortBy">
                    <option value="name_asc">
                        <?= app_lang('app.nameAZ') ?>
                    </option>
                    <option value="name_desc">
                        <?= app_lang('app.nameZA') ?>
                    </option>
                    <option value="date_asc">
                        <?= app_lang('app.dateOldest') ?>
                    </option>
                    <option value="date_desc">
                        <?= app_lang('app.dateNewest') ?>
                    </option>
                    <option value="size_asc">
                        <?= app_lang('app.sizeSmallest') ?>
                    </option>
                    <option value="size_desc">
                        <?= app_lang('app.sizeLargest') ?>
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>