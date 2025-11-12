 $(document).ready(function() {
    const permissionTable = $('#permission-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: permissionDataUrl,
            data: function(d) {
                d.search = $('#permission-table_filter input').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'roles_count', name: 'roles_count' },
            // { data: 'users_count', name: 'users_count' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        language: {
            searchPlaceholder: "Search records...",
            search: "",
            lengthMenu: "Show _MENU_",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    $('#addPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const button = $(this).find('.btn-submit')[0];
        
        handleAjaxWithSpinner(
            permissionStoreUrl,
            'POST',
            $(this).serialize(),
            button,
            function(response) {
                $('#addPermissionModal').modal('hide');
                permissionTable.ajax.reload();
                flashMessage('success', response.success);
                $('#addPermissionForm')[0].reset();
            },
            function(errorMsg) {
                flashMessage('error', errorMsg);
            }
        );
    });

    $(document).on('click', '.edit-permission', function() {
        const button = this;
        const permissionId = $(this).data('id');
        
        showSpinnerOnEditButton(button);
        
        $.ajax({
            url: permissionEditUrl.replace(':id', permissionId),
            method: 'GET',
            success: function(response) {
                $('#edit_permission_id').val(response.id);
                $('#edit_name').val(response.name);
                $('#editPermissionModal').modal('show');
            },
            error: function(xhr) {
                flashMessage('error', 'Failed to load permission data');
            },
            complete: function() {
                restoreEditButton(button);
            }
        });
    });

    $('#editPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const permissionId = $('#edit_permission_id').val();
        const button = $(this).find('.btn-submit')[0];
        
        handleAjaxWithSpinner(
            permissionUpdateUrl.replace(':id', permissionId),
            'POST',
            $(this).serialize() + '&_method=PUT',
            button,
            function(response) {
                $('#editPermissionModal').modal('hide');
                permissionTable.ajax.reload();
                flashMessage('success', response.success);
            },
            function(errorMsg) {
                flashMessage('error', errorMsg);
            }
        );
    });

    $(document).on('click', '.delete-permission', function() {
        const button = this;
        const permissionId = $(this).data('id');
        const permissionName = $(this).closest('tr').find('td:nth-child(2)').text();
        
        confirmDeleteWithSpinner(
            button,
            permissionDeleteUrl.replace(':id', permissionId),
            permissionName,
            function(response) {
                permissionTable.ajax.reload();
                flashMessage('success', response.success);
            },
            function(errorMsg) {
                flashMessage('error', errorMsg);
            }
        );
    });
});