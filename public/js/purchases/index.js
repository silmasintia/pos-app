document.addEventListener("DOMContentLoaded", function () {
    const purchasesTable = $('#purchases-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: purchasesDataUrl,
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error);
                console.error('Response:', xhr.responseText);
                flashMessage('error', 'Failed to load purchase data');
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'purchase_number' },
            { data: 'formatted_date' },
            { data: 'supplier_name' },
            { data: 'cash_name' },
            { data: 'formatted_total' },
            { data: 'status_badge', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        initComplete: function() {
            initializeSelect2();
            initializeActionButtons();
        }
    });

    function initializeSelect2() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').select2({
                placeholder: 'Select an option',
                width: '100%'
            });
        }
    }

    function initializeActionButtons() {
        $('.view-btn').off('click').on('click', function() {
            const purchaseId = $(this).data('id');
            
            fetch(purchasesViewUrl.replace(':id', purchaseId), {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const purchase = data.data;
                    const viewPurchaseNumber = document.getElementById('view_purchase_number');
                    if (viewPurchaseNumber) viewPurchaseNumber.textContent = purchase.purchase_number;
                    
                    const viewPurchaseDate = document.getElementById('view_purchase_date');
                    if (viewPurchaseDate) viewPurchaseDate.textContent = formatDate(purchase.purchase_date);
                    
                    const viewSupplierName = document.getElementById('view_supplier_name');
                    if (viewSupplierName) viewSupplierName.textContent = purchase.supplier ? purchase.supplier.name : '-';
                    
                    const viewCashName = document.getElementById('view_cash_name');
                    if (viewCashName) viewCashName.textContent = purchase.cash ? purchase.cash.name : '-';
                    
                    const viewTypePayment = document.getElementById('view_type_payment');
                    if (viewTypePayment) viewTypePayment.textContent = purchase.type_payment;
                    
                    const statusElement = document.getElementById('view_status');
                    if (statusElement) {
                        let statusClass = '';
                        switch(purchase.status) {
                            case 'completed':
                                statusClass = 'bg-success';
                                break;
                            case 'pending':
                                statusClass = 'bg-warning';
                                break;
                            case 'cancelled':
                                statusClass = 'bg-danger';
                                break;
                            default:
                                statusClass = 'bg-secondary';
                        }
                        statusElement.innerHTML = `<span class="badge ${statusClass}">${purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1)}</span>`;
                    }
                    
                    const viewTotalCost = document.getElementById('view_total_cost');
                    if (viewTotalCost) viewTotalCost.textContent = formatCurrency(purchase.total_cost);
                    
                    const viewDescription = document.getElementById('view_description');
                    if (viewDescription) viewDescription.textContent = purchase.description || '-';
                    
                    const imageContainer = document.getElementById('view_image_container');
                    if (imageContainer) {
                        if (purchase.image) {
                            imageContainer.innerHTML = `<img src="${window.location.origin}/storage/${purchase.image}" class="w-100 h-100" style="object-fit:cover;">`;
                        } else {
                            imageContainer.innerHTML = '<i class="fas fa-image fa-3x text-secondary"></i>';
                        }
                    }
                    
                    const itemsContainer = document.getElementById('view_items_container');
                    if (itemsContainer) {
                        itemsContainer.innerHTML = '';
                        
                        purchase.items.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.product ? item.product.name : '-'}</td>
                                <td>${item.quantity}</td>
                                <td>${formatCurrency(item.purchase_price)}</td>
                                <td>${formatCurrency(item.total_price)}</td>
                            `;
                            itemsContainer.appendChild(row);
                        });
                    }
                    
                    const viewPurchaseModal = document.getElementById('viewPurchaseModal');
                    if (viewPurchaseModal) {
                        const modal = new bootstrap.Modal(viewPurchaseModal);
                        modal.show();
                    }
                } else {
                    flashMessage('error', data.message || 'Failed to load purchase data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', 'Failed to load purchase data');
            });
        });

        $('.edit-btn').off('click').on('click', function() {
            const purchaseId = $(this).data('id');
            
            fetch(purchasesEditUrl.replace(':id', purchaseId), {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const purchase = data.data;
                    const editPurchaseId = document.getElementById('edit_purchase_id');
                    if (editPurchaseId) editPurchaseId.value = purchase.id;
                    
                    const editPurchaseNumber = document.getElementById('edit_purchase_number');
                    if (editPurchaseNumber) editPurchaseNumber.value = purchase.purchase_number;
                    
                    const editPurchaseDate = document.getElementById('edit_purchase_date');
                    if (editPurchaseDate) editPurchaseDate.value = purchase.purchase_date;
                    
                    const editSupplierId = document.getElementById('edit_supplier_id');
                    if (editSupplierId) editSupplierId.value = purchase.supplier_id;
                    
                    const editCashId = document.getElementById('edit_cash_id');
                    if (editCashId) editCashId.value = purchase.cash_id;
                    
                    const editTypePayment = document.getElementById('edit_type_payment');
                    if (editTypePayment) editTypePayment.value = purchase.type_payment;
                    
                    const editStatus = document.getElementById('edit_status');
                    if (editStatus) editStatus.value = purchase.status;
                    
                    const editDescription = document.getElementById('edit_description');
                    if (editDescription) editDescription.value = purchase.description || '';
                    
                    const imageContainerEdit = document.getElementById('image-container-edit');
                    if (imageContainerEdit) {
                        if (purchase.image) {
                            imageContainerEdit.innerHTML = `<img src="${window.location.origin}/storage/${purchase.image}" class="w-100 h-100" style="object-fit:cover;">`;
                        } else {
                            imageContainerEdit.innerHTML = '<i class="fas fa-image fa-2x text-secondary"></i>';
                        }
                    }
                    
                    const editItemsTable = document.getElementById('editItemsTable');
                    if (editItemsTable) {
                        const tbody = editItemsTable.getElementsByTagName('tbody')[0];
                        if (tbody) {
                            tbody.innerHTML = '';
                            
                            purchase.items.forEach((item, index) => {
                                const row = document.createElement('tr');
                                row.className = 'item-row';
                                row.innerHTML = `
                                    <td>
                                        <select name="items[${index}][product_id]" class="form-control product-select" required>
                                            <option value="">Select Product</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][quantity]" class="form-control quantity-input" min="1" value="${item.quantity}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][purchase_price]" class="form-control price-input" min="0" step="0.01" value="${item.purchase_price}" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total-input" value="${formatCurrency(item.total_price)}" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });
                            
                            updateProductSelects();
                            initializeSelect2();
                            
                            purchase.items.forEach((item, index) => {
                                const select = tbody.querySelector(`select[name="items[${index}][product_id]"]`);
                                if (select) {
                                    $(select).val(item.product_id).trigger('change');
                                }
                            });
                        }
                    }
                    
                    calculateEditGrandTotal();
                    
                    const editPurchaseModal = document.getElementById('editPurchaseModal');
                    if (editPurchaseModal) {
                        const modal = new bootstrap.Modal(editPurchaseModal);
                        modal.show();
                    }
                } else {
                    flashMessage('error', data.message || 'Failed to load purchase data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', 'Failed to load purchase data');
            });
        });

        $('.delete-btn').off('click').on('click', function() {
            const purchaseId = $(this).data('id');
            const purchaseNumber = $(this).data('number');

            confirmDialog('Delete Purchase', `Are you sure you want to delete purchase "${purchaseNumber}"?`)
                .then((result) => {
                    if (result.isConfirmed) {
                        fetch(purchasesDeleteUrl.replace(':id', purchaseId), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                purchasesTable.ajax.reload(() => {
                                    initializeActionButtons();
                                    flashMessage('success', data.message);
                                }, false);
                            } else {
                                flashMessage('error', data.message || 'An error occurred');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            flashMessage('error', 'An error occurred');
                        });
                    }
                });
        });
    }

    purchasesTable.on('draw', function() {
        initializeActionButtons();
    });

    let products = [];
    fetch(productsUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                products = data.data;
                updateProductSelects();
            } else {
                flashMessage('error', data.message || 'Failed to load products');
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            flashMessage('error', 'Failed to load products');
        });

    function updateProductSelects() {
        const productSelects = document.querySelectorAll('.product-select');
        productSelects.forEach(select => {
            const currentValue = select.value;
            
            while (select.options.length > 1) {
                select.remove(1);
            }
            
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.product_code} - ${product.name}`;
                select.appendChild(option);
            });
            
            select.value = currentValue;
        });
    }

    const addPurchaseForm = document.getElementById('addPurchaseForm');
    if (addPurchaseForm) {
        addPurchaseForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector('#addPurchaseModal .btn-submit');
            
            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector('.spinner-border');
                const text = btn.querySelector('.btn-text');
                const loadingText = btn.getAttribute('data-loading-text');
                
                if (spinner) spinner.classList.remove('d-none');
                if (text) text.textContent = loadingText;
            }

            fetch(purchasesStoreUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const addPurchaseModal = document.getElementById('addPurchaseModal');
                    if (addPurchaseModal) {
                        const modal = bootstrap.Modal.getInstance(addPurchaseModal);
                        if (modal) modal.hide();
                    }
                    
                    this.reset();
                    resetItemsTable();
                    
                    purchasesTable.ajax.reload(() => {
                        initializeActionButtons();
                        flashMessage('success', data.message);
                    }, false);
                } else {
                    flashMessage('error', data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', 'An error occurred');
            })
            .finally(() => {
                if (btn) {
                    const spinner = btn.querySelector('.spinner-border');
                    const text = btn.querySelector('.btn-text');
                    
                    if (spinner) spinner.classList.add('d-none');
                    if (text) text.textContent = 'Save Purchase';
                    btn.disabled = false;
                }
            });
        });
    }

    const editPurchaseForm = document.getElementById('editPurchaseForm');
    if (editPurchaseForm) {
        editPurchaseForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const purchaseId = document.getElementById('edit_purchase_id');
            const id = purchaseId ? purchaseId.value : '';
            const formData = new FormData(this);
            const btn = document.querySelector('#editPurchaseModal .btn-submit');
            
            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector('.spinner-border');
                const text = btn.querySelector('.btn-text');
                const loadingText = btn.getAttribute('data-loading-text');
                
                if (spinner) spinner.classList.remove('d-none');
                if (text) text.textContent = loadingText;
            }

            fetch(purchasesUpdateUrl.replace(':id', id), {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const editPurchaseModal = document.getElementById('editPurchaseModal');
                    if (editPurchaseModal) {
                        const modal = bootstrap.Modal.getInstance(editPurchaseModal);
                        if (modal) modal.hide();
                    }
                    
                    purchasesTable.ajax.reload(() => {
                        initializeActionButtons();
                        flashMessage('success', data.message);
                    }, false);
                } else {
                    flashMessage('error', data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flashMessage('error', 'An error occurred');
            })
            .finally(() => {
                if (btn) {
                    const spinner = btn.querySelector('.spinner-border');
                    const text = btn.querySelector('.btn-text');
                    
                    if (spinner) spinner.classList.add('d-none');
                    if (text) text.textContent = 'Update Purchase';
                    btn.disabled = false;
                }
            });
        });
    }

    const addItemBtn = document.getElementById('addItemBtn');
    if (addItemBtn) {
        addItemBtn.addEventListener('click', function() {
            const itemsTable = document.getElementById('itemsTable');
            if (itemsTable) {
                const tbody = itemsTable.getElementsByTagName('tbody')[0];
                if (tbody) {
                    const rowCount = tbody.rows.length;
                    
                    const row = document.createElement('tr');
                    row.className = 'item-row';
                    row.innerHTML = `
                        <td>
                            <select name="items[${rowCount}][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity-input" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][purchase_price]" class="form-control price-input" min="0" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="form-control total-input" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                    
                    updateProductSelects();
                    initializeSelect2();
                    addInputEventListeners(row);
                }
            }
        });
    }

    const editAddItemBtn = document.getElementById('editAddItemBtn');
    if (editAddItemBtn) {
        editAddItemBtn.addEventListener('click', function() {
            const itemsTable = document.getElementById('editItemsTable');
            if (itemsTable) {
                const tbody = itemsTable.getElementsByTagName('tbody')[0];
                if (tbody) {
                    const rowCount = tbody.rows.length;
                    
                    const row = document.createElement('tr');
                    row.className = 'item-row';
                    row.innerHTML = `
                        <td>
                            <select name="items[${rowCount}][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity-input" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][purchase_price]" class="form-control price-input" min="0" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="form-control total-input" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                    
                    updateProductSelects();
                    initializeSelect2();
                    addInputEventListeners(row);
                }
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const row = e.target.closest('tr');
            if (row) {
                row.remove();
                
                if (row.closest('#itemsTable')) {
                    calculateGrandTotal();
                } else if (row.closest('#editItemsTable')) {
                    calculateEditGrandTotal();
                }
            }
        }
    });

    document.querySelectorAll('.quantity-input, .price-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            if (row) {
                calculateRowTotal(row);
                
                if (row.closest('#itemsTable')) {
                    calculateGrandTotal();
                } else if (row.closest('#editItemsTable')) {
                    calculateEditGrandTotal();
                }
            }
        });
    });

    function addInputEventListeners(row) {
        if (!row) return;
        
        row.querySelectorAll('.quantity-input, .price-input').forEach(input => {
            input.addEventListener('input', function() {
                const currentRow = this.closest('tr');
                if (currentRow) {
                    calculateRowTotal(currentRow);
                    
                    if (currentRow.closest('#itemsTable')) {
                        calculateGrandTotal();
                    } else if (currentRow.closest('#editItemsTable')) {
                        calculateEditGrandTotal();
                    }
                }
            });
        });
    }

    function calculateRowTotal(row) {
        if (!row) return;
        
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const totalInput = row.querySelector('.total-input');
        
        if (!quantityInput || !priceInput || !totalInput) return;
        
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        
        totalInput.value = formatCurrency(total);
    }

    function calculateGrandTotal() {
        const rows = document.querySelectorAll('#itemsTable .item-row');
        let grandTotal = 0;
        
        rows.forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            
            if (quantityInput && priceInput) {
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                grandTotal += quantity * price;
            }
        });
        
        const grandTotalInput = document.getElementById('grandTotal');
        if (grandTotalInput) {
            grandTotalInput.value = formatCurrency(grandTotal);
        }
    }

    function calculateEditGrandTotal() {
        const rows = document.querySelectorAll('#editItemsTable .item-row');
        let grandTotal = 0;
        
        rows.forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            
            if (quantityInput && priceInput) {
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                grandTotal += quantity * price;
            }
        });
        
        const editGrandTotalInput = document.getElementById('editGrandTotal');
        if (editGrandTotalInput) {
            editGrandTotalInput.value = formatCurrency(grandTotal);
        }
    }

    function resetItemsTable() {
        const itemsTable = document.getElementById('itemsTable');
        if (itemsTable) {
            const tbody = itemsTable.getElementsByTagName('tbody')[0];
            if (tbody) {
                tbody.innerHTML = `
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][purchase_price]" class="form-control price-input" min="0" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="form-control total-input" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                
                updateProductSelects();
                initializeSelect2();
                
                const row = tbody.rows[0];
                addInputEventListeners(row);
                
                const grandTotalInput = document.getElementById('grandTotal');
                if (grandTotalInput) {
                    grandTotalInput.value = formatCurrency(0);
                }
            }
        }
    }

    function formatCurrency(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function previewTransactionImage(input, type) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                let container;
                if (type === 'new') {
                    container = document.getElementById('image-container-new');
                } else if (type === 'edit') {
                    container = document.getElementById('image-container-edit');
                }
                
                if (container) {
                    container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`;
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    const addImageInput = document.querySelector('input[name="image"]');
    if (addImageInput) {
        addImageInput.addEventListener('change', function() {
            previewTransactionImage(this, 'new');
        });
    }
    
    const editImageInput = document.getElementById('image-edit');
    if (editImageInput) {
        editImageInput.addEventListener('change', function() {
            previewTransactionImage(this, 'edit');
        });
    }
});

window.clearImagePreview = function(type) {
    let container;
    if (type === 'new') {
        container = document.getElementById('image-container-new');
        document.querySelector('input[name="image"]').value = '';
    } else if (type === 'edit') {
        container = document.getElementById('image-container-edit');
        document.getElementById('image-edit').value = '';
    }
    
    if (container) {
        container.innerHTML = '<i class="fas fa-image fa-2x text-secondary"></i>';
    }
};