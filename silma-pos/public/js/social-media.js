document.addEventListener("DOMContentLoaded", function () {
    const socialMediaTable = $('#social-media-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: socialMediaDataUrl,
            type: 'GET'
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            { data: "image_preview", name: "image_preview", orderable: false, searchable: false },
            { data: "name", name: "name" },
            { data: "description", name: "description" },
            { data: "link", name: "link" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
        pageLength: 10,
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
    });

    // Add Social Media 
    const addSocialMediaForm = document.getElementById('add-social-media-form');
    if (addSocialMediaForm) {
        addSocialMediaForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector('#addSocialMediaModal .btn-submit');
            const spinner = btn.querySelector('.spinner-border');
            const text = btn.querySelector('.btn-text');
            const loadingText = btn.getAttribute('data-loading-text');

            spinner.classList.remove('d-none');
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(socialMediaStoreUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSocialMediaModal'));
                    modal.hide();
                    this.reset();
                    document.getElementById('image-container-add').innerHTML = '<i class="fas fa-image fa-2x text-secondary"></i>';
                    socialMediaTable.ajax.reload();
                    flashMessage('success', data.message);
                } else {
                    flashMessage('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', error.message);
            })
            .finally(() => {
                spinner.classList.add('d-none');
                text.textContent = 'Add Social Media';
                btn.disabled = false;
            });
        });
    }

    // Edit Social Media Button Click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-social-btn')) {
            const btn = e.target.closest('.edit-social-btn');
            const socialMediaId = btn.getAttribute('data-id');

            fetch(socialMediaEditUrl.replace(':id', socialMediaId), {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const socialMedia = data.social_media;
                    document.getElementById('edit_social_id').value = socialMedia.id;
                    document.getElementById('edit_social_name').value = socialMedia.name;
                    document.getElementById('edit_social_description').value = socialMedia.description || '';
                    document.getElementById('edit_social_link').value = socialMedia.link || '';

                    const imageContainer = document.getElementById('image-container-edit');
                    if (socialMedia.image) {
                        imageContainer.innerHTML = `<img src="${window.location.origin}/storage/${socialMedia.image}" class="w-100 h-100" style="object-fit:cover;">`;
                    } else {
                        imageContainer.innerHTML = '<i class="fas fa-image fa-2x text-secondary"></i>';
                    }

                    const modal = new bootstrap.Modal(document.getElementById('editSocialMediaModal'));
                    modal.show();
                } else {
                    flashMessage('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', 'Failed to load social media data');
            });
        }
    });

    // Update Social Media 
    const editSocialMediaForm = document.getElementById('edit-social-media-form');
    if (editSocialMediaForm) {
        editSocialMediaForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const socialMediaId = document.getElementById('edit_social_id').value;
            const formData = new FormData(this);
            const btn = document.querySelector('#editSocialMediaModal .btn-submit');
            const spinner = btn.querySelector('.spinner-border');
            const text = btn.querySelector('.btn-text');
            const loadingText = btn.getAttribute('data-loading-text');

            spinner.classList.remove('d-none');
            text.textContent = loadingText;
            btn.disabled = true;

            formData.append('_method', 'PUT');

            fetch(socialMediaUpdateUrl.replace(':id', socialMediaId), {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editSocialMediaModal'));
                    modal.hide();
                    socialMediaTable.ajax.reload();
                    flashMessage('success', data.message);
                } else {
                    flashMessage('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', error.message);
            })
            .finally(() => {
                spinner.classList.add('d-none');
                text.textContent = 'Update Social Media';
                btn.disabled = false;
            });
        });
    }

    // Delete Social Media 
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-social-btn')) {
            const btn = e.target.closest('.delete-social-btn');
            const socialMediaId = btn.getAttribute('data-id');

            confirmDialog('Delete Social Media', 'Are you sure you want to delete this social media account?')
            .then(result => {
                if (result.isConfirmed) {
                    fetch(socialMediaDeleteUrl.replace(':id', socialMediaId), {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    })
                    .then(handleApiResponse)
                    .then(data => {
                        if (data.success) {
                            socialMediaTable.ajax.reload();
                            flashMessage('success', data.message);
                        } else {
                            flashMessage('error', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        flashMessage('error', error.message);
                    });
                }
            });
        }
    });
});