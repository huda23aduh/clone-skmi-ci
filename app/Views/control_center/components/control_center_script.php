<script>
$(document).ready(function() {
    let currentPage = 1;
    let search = '';
    let status = 'all';
    let sort = 'created_at_desc';
    let memberToDelete = null;

    // Load members on page load
    loadMembers();

    // Add member form submission
    $('#addMemberForm').on('submit', function(e) {
        e.preventDefault();
        addMember();
    });

    // Search input with debounce
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            search = $(this).val();
            currentPage = 1;
            loadMembers();
        }, 500);
    });

    // Filter and sort changes
    $('#statusFilter, #sortSelect').on('change', function() {
        status = $('#statusFilter').val();
        sort = $('#sortSelect').val();
        currentPage = 1;
        loadMembers();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#searchInput').val('');
        $('#statusFilter').val('all');
        $('#sortSelect').val('created_at_desc');
        search = '';
        status = 'all';
        sort = 'created_at_desc';
        currentPage = 1;
        loadMembers();
    });

    // Load members function
    // Load members function
    function loadMembers() {
        console.log('Loading members...', { currentPage, search, status, sort });
        
        $.ajax({
            url: '<?= base_url('control-center/members') ?>',
            type: 'GET',
            data: {
                page: currentPage,
                search: search,
                status: status,
                sort: sort
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderMembersTable(response.data);
                } else {
                    console.error('Failed response:', response);
                    showToast('error', response.message || 'Failed to load members');
                    // Show empty state
                    $('#membersTableContainer').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-muted">Failed to load members</h5>
                            <p class="text-muted">Please try again later</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.error('Status:', status);
                console.error('XHR:', xhr);
                showToast('error', 'Error loading members: ' + error);
                // Show error state
                $('#membersTableContainer').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-muted">Error loading members</h5>
                        <p class="text-muted">Please check your connection and try again</p>
                    </div>
                `);
            }
        });
    }

    // Render members table
    function renderMembersTable(data) {
        const { members, pagination } = data;
        let html = '';

        if (members.length > 0) {
            html = `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                <?= app_lang('app.name') ?>
                                </th>
                                <th>
                                <?= app_lang('app.email_address') ?>
                                </th>
                                <th>Status</th>
                                <th>
                                <?= app_lang('app.date') ?>
                                </th>
                                <th width="120">
                                <?= app_lang('app.actions') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            members.forEach(member => {
                const createdDate = new Date(member.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                html += `
                    <tr>
                        <td>${escapeHtml(member.name || 'N/A')}</td>
                        <td>${escapeHtml(member.email)}</td>
                        <td>
                            <span class="badge ${member.isActive ? 'bg-success' : 'bg-danger'}">
                                ${member.isActive ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>${createdDate}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-warning toggle-status" 
                                        data-id="${member.id}" data-status="${member.isActive}">
                                    <i class="fas ${member.isActive ? 'fa-ban' : 'fa-check'}"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-member" 
                                        data-id="${member.id}" data-name="${escapeHtml(member.name || member.email)}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            // Add pagination
            if (pagination.totalPages > 1) {
                html += renderPagination(pagination);
            }
        } else {
            html = `
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No members found</h5>
                    <p class="text-muted">No members match your search criteria</p>
                </div>
            `;
        }

        $('#membersTableContainer').html(html);
        attachEventHandlers();
    }

    // Render pagination
    function renderPagination(pagination) {
        let html = `
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing ${((pagination.currentPage - 1) * pagination.perPage) + 1} to 
                    ${Math.min(pagination.currentPage * pagination.perPage, pagination.totalItems)} of 
                    ${pagination.totalItems} entries
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
        `;

        // Previous button
        html += `
            <li class="page-item ${pagination.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage - 1}">Previous</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 1 && i <= pagination.currentPage + 1)) {
                html += `
                    <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === pagination.currentPage - 2 || i === pagination.currentPage + 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next button
        html += `
            <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage + 1}">Next</a>
            </li>
        `;

        html += `
                    </ul>
                </nav>
            </div>
        `;

        return html;
    }

    // Attach event handlers to dynamic elements
    function attachEventHandlers() {
        // Pagination
        $('.pagination .page-link').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page && page !== currentPage) {
                currentPage = page;
                loadMembers();
            }
        });

        // Toggle status
        $('.toggle-status').on('click', function() {
            const memberId = $(this).data('id');
            const currentStatus = $(this).data('status');
            toggleMemberStatus(memberId, currentStatus);
        });

        // Delete member
        $('.delete-member').on('click', function() {
            memberToDelete = $(this).data('id');
            const memberName = $(this).data('name');
            $('#deleteModal .modal-body p').text(`Are you sure you want to delete "${memberName}"? This action cannot be undone.`);
            $('#deleteModal').modal('show');
        });
    }

    // Add member
    function addMember() {
        const formData = $('#addMemberForm').serialize();
        const button = $('#addMemberBtn');
        const originalText = button.html();

        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
        $('#formErrors').hide().empty();

        $.ajax({
            url: '<?= base_url('control-center/members') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#addMemberForm')[0].reset();
                    showToast('success', response.message);
                    loadMembers();
                } else {
                    if (response.errors) {
                        let errors = '';
                        for (const field in response.errors) {
                            errors += response.errors[field] + '<br>';
                        }
                        $('#formErrors').html(errors).show();
                    } else {
                        showToast('error', response.message);
                    }
                }
            },
            error: function() {
                showToast('error', 'Error adding member');
            },
            complete: function() {
                button.prop('disabled', false).html(originalText);
            }
        });
    }

    // Toggle member status
    function toggleMemberStatus(memberId, currentStatus) {
        $.ajax({
            url: `<?= base_url('control-center/members') ?>/${memberId}/toggle-status`,
            type: 'POST',
            data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    loadMembers();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Error updating member status');
            }
        });
    }

    // Confirm delete
    $('#confirmDelete').on('click', function() {
        if (memberToDelete) {
            $.ajax({
                url: `<?= base_url('control-center/members') ?>/${memberToDelete}`,
                type: 'DELETE',
                data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        loadMembers();
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function() {
                    showToast('error', 'Error deleting member');
                }
            });
        }
        $('#deleteModal').modal('hide');
        memberToDelete = null;
    });

    // Utility functions
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

});
</script>