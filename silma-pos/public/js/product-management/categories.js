document.addEventListener("DOMContentLoaded", function () {
    const categoriesTable = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: categoriesDataUrl,
            type: 'GET'
        },
        columns: [
            { data: 'no', orderable: false, searchable: false },
            { data: 'image_preview', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'slug' },
            { 
                data: 'description',
                render: function(data) {
                    if (!data) return '';
                    const text = data.length > 20 ? data.substring(0, 20) + '...' : data;
                    return `<span title="${data.replace(/"/g, '&quot;')}">${text}</span>`;
                }
            },
            { data: 'position' },
            { data: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        order: [[5, 'asc']]
    });

    // Add Category 
    const addCategoryForm = document.getElementById('addCategoryForm');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector('#addCategoryModal .btn-submit');
            const spinner = btn.querySelector('.spinner-border');
            const text = btn.querySelector('.btn-text');
            const loadingText = btn.getAttribute('data-loading-text');

            spinner.classList.remove('d-none');
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(categoriesStoreUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                    modal.hide();
                    this.reset();
                    document.getElementById('image-container-new').innerHTML = '<i class="fas fa-image fa-2x text-secondary"></i>';
                    categoriesTable.ajax.reload();
                    flashMessage('success', data.message);
                } else {
                    flashMessage('error', data.message || 'Unexpected error');
                }
            })
            .catch(error => {
                console.error('Add Category Error:', error);
                flashMessage('error', error.message);
            })
            .finally(() => {
                spinner.classList.add('d-none');
                text.textContent = 'Add Category';
                btn.disabled = false;
            });
        });
    }

    // Edit Category 
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            const btn = e.target.closest('.edit-btn');
            const categoryId = btn.getAttribute('data-id');

            fetch(categoriesUpdateUrl.replace(':id', categoryId) + '/edit', {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse)
            .then(data => {
                document.getElementById('edit_category_id').value = data.id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_description').value = data.description || '';
                document.getElementById('edit_position').value = data.position || 0;

                const imageContainer = document.getElementById('image-container-edit');
                if (data.image) {
                    imageContainer.innerHTML = `<img src="${window.location.origin}/${data.image}" class="w-100 h-100" style="object-fit:cover;">`;
                } else {
                    imageContainer.innerHTML = '<i class="fas fa-image fa-2x text-secondary"></i>';
                }

                const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Edit Category Error:', error);
                flashMessage('error', error.message || 'Failed to load category data.');
            });
        }
    });

    // Update Category 
    const editCategoryForm = document.getElementById('editCategoryForm');
    if (editCategoryForm) {
        editCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const categoryId = document.getElementById('edit_category_id').value;
            const formData = new FormData(this);
            const btn = document.querySelector('#editCategoryModal .btn-submit');
            const spinner = btn.querySelector('.spinner-border');
            const text = btn.querySelector('.btn-text');
            const loadingText = btn.getAttribute('data-loading-text');

            spinner.classList.remove('d-none');
            text.textContent = loadingText;
            btn.disabled = true;

            formData.append('_method', 'PUT');

            fetch(categoriesUpdateUrl.replace(':id', categoryId), {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                    modal.hide();
                    categoriesTable.ajax.reload();
                    flashMessage('success', data.message);
                } else {
                    flashMessage('error', data.message || 'Unexpected error');
                }
            })
            .catch(error => {
                console.error('Update Category Error:', error);
                flashMessage('error', error.message);
            })
            .finally(() => {
                spinner.classList.add('d-none');
                text.textContent = 'Update Category';
                btn.disabled = false;
            });
        });
    }

    // Delete Category 
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const categoryId = btn.getAttribute('data-id');
            const categoryName = btn.getAttribute('data-name');

            confirmDialog('Delete Category', `Are you sure you want to delete "${categoryName}"?`)
            .then(result => {
                if (result.isConfirmed) {
                    fetch(categoriesDeleteUrl.replace(':id', categoryId), {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    })
                    .then(handleApiResponse)
                    .then(data => {
                        if (data.success) {
                            categoriesTable.ajax.reload();
                            flashMessage('success', data.message);
                        } else {
                            flashMessage('error', data.message || 'Unexpected error');
                        }
                    })
                    .catch(error => {
                        console.error('Delete Category Error:', error);
                        flashMessage('error', error.message);
                    })
                    .finally(() => {
                        restoreDeleteButton(btn);
                    });
                } else {
                    restoreDeleteButton(btn);
                }
            });
        }
    });
});
