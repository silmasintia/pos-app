document.addEventListener("DOMContentLoaded", function () {
    const suppliersTable = $('#suppliers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: suppliersDataUrl,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'address',
                render: function(data) {
                    if (!data) return '';
                    const text = data.length > 30 ? data.substring(0, 30) + '...' : data;
                    return `<span title="${data.replace(/"/g, '&quot;')}">${text}</span>`;
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true
    });

    // Add Supplier
    const addSupplierForm = document.getElementById('addSupplierForm');
    if (addSupplierForm) {
        addSupplierForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector('#addSupplierModal .btn-submit');
            const spinner = btn.querySelector('.spinner-border');
            const text = btn.querySelector('.btn-text');
            const loadingText = btn.getAttribute('data-loading-text');

            spinner.classList.remove('d-none');
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(suppliersStoreUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse) 
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSupplierModal'));
                    modal.hide();
                    this.reset();
                    suppliersTable.ajax.reload();
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
                text.textContent = 'Add Supplier';
                btn.disabled = false;
            });
        });
    }

    // Edit Supplier Button Click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            const btn = e.target.closest('.edit-btn');
            const supplierId = btn.getAttribute('data-id');

            fetch(suppliersUpdateUrl.replace(':id', supplierId) + '/edit', {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse) 
            .then(data => {
                document.getElementById('edit_supplier_id').value = data.id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_email').value = data.email || '';
                document.getElementById('edit_phone').value = data.phone || '';
                document.getElementById('edit_address').value = data.address || '';

                const modal = new bootstrap.Modal(document.getElementById('editSupplierModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', error.message);
            });
        }
    });

    // Update Supplier
    const editSupplierForm = document.getElementById('editSupplierForm');
    if (editSupplierForm) {
        editSupplierForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const supplierId = document.getElementById('edit_supplier_id').value;
            const formData = new FormData(this);
            const btn = document.querySelector('#editSupplierModal .btn-submit');
            const spinner = btn.querySelector('.spinner-border');
            const text = btn.querySelector('.btn-text');
            const loadingText = btn.getAttribute('data-loading-text');

            spinner.classList.remove('d-none');
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(suppliersUpdateUrl.replace(':id', supplierId), {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editSupplierModal'));
                    modal.hide();
                    suppliersTable.ajax.reload();
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
                text.textContent = 'Update Supplier';
                btn.disabled = false;
            });
        });
    }

    // Delete Supplier 
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const supplierId = btn.getAttribute('data-id');

            confirmDialog('Delete Supplier', 'Are you sure you want to delete this supplier?')
                .then((result) => {
                    if (result.isConfirmed) {
                        fetch(suppliersDeleteUrl.replace(':id', supplierId), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken }
                        })
                        .then(handleApiResponse) 
                        .then(data => {
                            if (data.success) {
                                suppliersTable.ajax.reload();
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