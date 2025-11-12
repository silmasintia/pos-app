 $(document).ready(function() {
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: usersDataUrl,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        language: {
            searchPlaceholder: "Search...",
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

    if (typeof successMessage !== 'undefined') {
        flashMessage('success', successMessage);
    }
    if (typeof errorMessage !== 'undefined') {
        flashMessage('error', errorMessage);
    }

    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        submitUserForm($(this), usersStoreUrl, 'POST', 'Add User', '#addUserModal');
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const userId = $('#edit_user_id').val();
        submitUserForm($(this), `/users/${userId}`, 'PUT', 'Update User', '#editUserModal');
    });

    $(document).on('click', '.edit-btn', function() {
        const userId = $(this).data('id');
        loadUserEdit(userId);
    });

    $(document).on('click', '.delete-btn', function() {
        const userId = $(this).data('id');
        deleteUser(userId, $(this));
    });

    $('#addUserModal').on('hidden.bs.modal', function () {
        $('#addUserForm')[0].reset();
        resetPreviewImages('new');
    });

    $('#editUserModal').on('hidden.bs.modal', function () {
        $('#editUserForm')[0].reset();
        resetPreviewImages('edit');
    });

    window.reloadTable = function() {
        table.ajax.reload(null, false);
    };
});

function previewProfileImage(input, type) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#profile-image-container-' + type).html(
                `<img src="${e.target.result}" class="rounded-circle border-4 border-white shadow" style="width:100px;height:100px;object-fit:cover;">`
            );
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function previewBanner(input, type) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#banner-container-' + type).html(
                `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`
            );
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function resetPreviewImages(type) {
    $('#profile-image-container-' + type).html(
        `<div class="rounded-circle border-4 border-white bg-light d-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px;">
            <i class="fas fa-user fa-2x text-secondary"></i>
        </div>`
    );
    
    $('#banner-container-' + type).html(
        `<div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
            <span class="text-white">Banner</span>
        </div>`
    );
}

function loadUserEdit(userId) {
    $.get(`/users/${userId}/edit`, function(response) {
        $('#edit_user_id').val(response.id);
        $('#edit_name').val(response.name);
        $('#edit_username').val(response.username);
        $('#edit_email').val(response.email);
        $('#edit_phone_number').val(response.phone_number);
        $('#edit_wa_number').val(response.wa_number);
        $('#edit_address').val(response.address);
        $('#edit_about').val(response.about);
        $('#edit_description').val(response.description);

        $('#edit_status').prop('checked', response.status == 1);
        $('#edit_status_label').text(response.status ? 'Active' : 'Inactive');

        $('#edit_status_display').prop('checked', response.status_display == 1);
        $('#edit_status_display_label').text(response.status_display ? 'Public' : 'Private');

        if (response.image) {
            $('#profile-image-container-edit').html(
                `<img src="${window.location.origin}/storage/${response.image}" class="rounded-circle border-4 border-white shadow" style="width:100px;height:100px;object-fit:cover;">`
            );
        } else {
            $('#profile-image-container-edit').html(
                `<div class="rounded-circle border-4 border-white bg-light d-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px;">
                    <i class="fas fa-user fa-2x text-secondary"></i>
                </div>`
            );
        }
        
        if (response.banner) {
            $('#banner-container-edit').html(
                `<img src="${window.location.origin}/storage/${response.banner}" class="w-100 h-100" style="object-fit:cover;">`
            );
        } else {
            $('#banner-container-edit').html(
                `<div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
                    <span class="text-white">Banner</span>
                </div>`
            );
        }
        
        $('#remove-image-edit').prop('checked', false);
        $('#remove-banner-edit').prop('checked', false);
    }).fail(function(xhr) {
        let errorMsg = 'Failed to load user data.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        }
        flashMessage('error', errorMsg);
    });
}

function submitUserForm(form, url, method, defaultText, modalId) {
    const btn = $(modalId).find('.modal-footer .btn-submit');
    const spinner = btn.find('.spinner-border');
    const text = btn.find('.btn-text');
    const loadingText = btn.attr('data-loading-text');

    btn.prop('disabled', true);
    spinner.removeClass('d-none');
    text.text(loadingText);

    if (method === 'PUT') {
        const removeImageCheckbox = form.find('input[name="remove_image"]');
        const removeBannerCheckbox = form.find('input[name="remove_banner"]');
        
        if (removeImageCheckbox.length) {
            removeImageCheckbox.val(removeImageCheckbox.is(':checked') ? '1' : '0');
        }
        
        if (removeBannerCheckbox.length) {
            removeBannerCheckbox.val(removeBannerCheckbox.is(':checked') ? '1' : '0');
        }
    }

    $.ajax({
        url: url,
        method: "POST",
        data: new FormData(form[0]),
        processData: false,
        contentType: false,
        headers: {
            'X-HTTP-Method-Override': method
        },
        success: function(response) {
            if (response.success) {
                $(modalId).modal('hide');
                form[0].reset();
                reloadTable(); 
                flashMessage('success', response.message);
            }
        },
        error: function(xhr) {
            let errorMsg = 'An error occurred. Please try again.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).join('<br>');
                } else if (xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
            }
            flashMessage('error', errorMsg);
        },
        complete: function() {
            btn.prop('disabled', false);
            spinner.addClass('d-none');
            text.text(defaultText);
        }
    });
}

function deleteUser(userId, btn) {
    confirmDialog('Apakah Anda yakin?', 'Data ini akan dihapus permanen!', 'Ya, Hapus!', 'Batal')
        .then((result) => {
            if (result.isConfirmed) {
                const spinner = btn.find('.spinner-border');
                const btnText = btn.find('.btn-text');
                
                spinner.removeClass('d-none');
                btnText.addClass('d-none');
                
                $.ajax({
                    url: `/users/${userId}`,
                    method: "DELETE",
                    data: { _token: csrfToken },
                    success: function(response) {
                        if (response.success) {
                            reloadTable(); 
                            flashMessage('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete user.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        flashMessage('error', errorMsg);
                    },
                    complete: function() {
                        spinner.addClass('d-none');
                        btnText.removeClass('d-none');
                    }
                });
            }
        });
}