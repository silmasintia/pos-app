 $(document).ready(function() {
    const roleTable = $('#role-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: roleDataUrl,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'users_count', name: 'users_count' },
            { data: 'permissions_count', name: 'permissions_count' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"f>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "Show _MENU_",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    $('#addRoleForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#addRoleModal').find('.btn-submit');
        const $btnText = $btn.find('.btn-text');
        const $spinner = $btn.find('.spinner-border');
        
        $btn.prop('disabled', true);
        $btnText.text($btn.data('loading-text'));
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: roleStoreUrl,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addRoleModal').modal('hide');
                roleTable.ajax.reload();
                alertDialog('success', 'Success', response.success);
                $('#addRoleForm')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    for (let key in errors) {
                        errorMsg += errors[key][0] + '<br>';
                    }
                    alertDialog('error', 'Error', errorMsg);
                } else {
                    alertDialog('error', 'Error', 'An error occurred while creating role');
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btnText.text($btn.data('original-text'));
                $spinner.addClass('d-none');
            }
        });
    });

    $(document).on('click', '.edit-role', function() {
        const roleId = $(this).data('id');
        
        showSpinnerOnEditButton(this);
        
        $.ajax({
            url: `/roles/${roleId}/edit`,
            method: 'GET',
            success: function(response) {
                $('#edit_role_id').val(response.id);
                $('#edit_name').val(response.name);
                
                $.ajax({
                    url: '/api/permissions',
                    method: 'GET',
                    success: function(allPermissions) {
                        let permissionsHtml = '';
                        $.each(allPermissions, function(index, permission) {
                            const isChecked = response.permissions.some(p => p.id === permission.id) ? 'checked' : '';
                            permissionsHtml += `
                                <div class="form-check permission-item">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="${permission.id}" id="edit_permission_${permission.id}" ${isChecked}>
                                    <label class="form-check-label" for="edit_permission_${permission.id}">
                                        ${permission.name}
                                    </label>
                                </div>
                            `;
                        });
                        
                        $('#edit_permissions').html(permissionsHtml);
                        $('#editRoleModal').modal('show');
                    },
                    error: function() {
                        alertDialog('error', 'Error', 'Error loading permissions');
                    }
                });
            },
            error: function(xhr) {
                console.error(xhr);
                alertDialog('error', 'Error', 'Failed to load role data');
            },
            complete: function() {
                restoreEditButton(this);
            }.bind(this)
        });
    });

    $('#editRoleForm').on('submit', function(e) {
        e.preventDefault();
        const roleId = $('#edit_role_id').val();
        const $btn = $('#editRoleModal').find('.btn-submit');
        const $btnText = $btn.find('.btn-text');
        const $spinner = $btn.find('.spinner-border');
        
        $btn.prop('disabled', true);
        $btnText.text($btn.data('loading-text'));
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: roleUpdateUrl.replace(':id', roleId),
            method: 'POST',
            data: $(this).serialize() + '&_method=PUT',
            success: function(response) {
                $('#editRoleModal').modal('hide');
                roleTable.ajax.reload();
                alertDialog('success', 'Success', response.success);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    for (let key in errors) {
                        errorMsg += errors[key][0] + '<br>';
                    }
                    alertDialog('error', 'Error', errorMsg);
                } else {
                    alertDialog('error', 'Error', 'An error occurred while updating role');
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btnText.text($btn.data('original-text'));
                $spinner.addClass('d-none');
            }
        });
    });

    $(document).on('click', '.delete-role', function() {
        const roleId = $(this).data('id');
        const deleteUrl = roleDeleteUrl.replace(':id', roleId);
        const roleName = $(this).closest('tr').find('td:nth-child(2)').text();
        
        confirmDeleteWithSpinner(
            this,
            deleteUrl,
            roleName, 
            function(response) {
                roleTable.ajax.reload();
                alertDialog('success', 'Success', response.success);
            },
            function(errorMsg) {
                alertDialog('error', 'Error', errorMsg);
            }
        );
    });

    $(document).on('keyup', '.permission-search', function() {
        const searchTerm = $(this).val().toLowerCase();
        const $container = $(this).closest('.modal-body').find('.permission-checkboxes');
        
        $container.find('.permission-item').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(searchTerm) > -1);
        });
    });
});