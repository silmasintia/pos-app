document.addEventListener("DOMContentLoaded", function() {
    let cart = [];
    let products = [];
    
    loadProducts();
    
    document.getElementById('search-btn').addEventListener('click', searchProduct);
    document.getElementById('product-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProduct();
        }
    });
    
    document.getElementById('category-filter').addEventListener('change', filterProducts);
    
    document.getElementById('percent-discount').addEventListener('input', calculateTotal);
    document.getElementById('amount-discount').addEventListener('input', calculateTotal);
    
    document.getElementById('payment-amount').addEventListener('input', calculateChange);
    
    document.getElementById('clear-cart-btn').addEventListener('click', clearCart);
    document.getElementById('checkout-btn').addEventListener('click', processOrder);
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('#print-receipt-btn')) {
            const orderId = document.getElementById('print-receipt-btn').getAttribute('data-id');
            window.open(printReceiptUrl.replace(':id', orderId), '_blank');
        }
    });
    
    function loadProducts() {
        fetch(productsUrl)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    products = data;
                } else if (Array.isArray(data.products)) {
                    products = data.products;
                } else {
                    console.error('Unexpected response format:', data);
                    throw new Error('Invalid product data format');
                }

                displayProducts(products);
            })
            .catch(error => {
                console.error('Error loading products:', error);
                flashMessage('error', 'Failed to load products');
                document.getElementById('product-container').innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            Failed to load products. Please try again later.
                        </div>
                    </div>
                `;
            });
    }
    
    function displayProducts(productsToDisplay) {
        const productContainer = document.getElementById('product-container');
        productContainer.innerHTML = '';

        if (productsToDisplay.length === 0) {
            productContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No products found</p>
                </div>
            `;
            return;
        }

        productsToDisplay.forEach(product => {
            const productColumn = document.createElement('div');
            productColumn.className = 'col-xl-1 col-lg-2 col-md-3 col-4';

            const price = numberFormat(product.price || 0);
            const stock = product.base_stock || 0;

            const imageUrl = product.image ? `/storage/${product.image}` : '';

            productColumn.innerHTML = `
                <div class="card product-card h-100" data-id="${product.id}" ${stock <= 0 ? 'style="opacity: 0.6; pointer-events: none;"' : ''}>
                    <div class="product-image-wrapper">
                        ${imageUrl ?
                            `<img src="${imageUrl}" class="product-image" alt="${product.name}">` :
                            `<div class="product-image d-flex align-items-center justify-content-center bg-body-tertiary">
                                <i class="fas fa-box fa-2x text-muted"></i>
                            </div>`
                        }
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title fw-bold small mb-1 text-truncate" title="${product.name}">${product.name}</h6>
                        <p class="card-text small fw-medium text-primary mb-0">Rp ${price}</p>
                    </div>
                    ${stock <= 0 ? '<div class="card-footer p-1 text-center small bg-danger text-white">Out of Stock</div>' : ''}
                </div>
            `;
            
            productContainer.appendChild(productColumn);
        });

        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                const stock = card.style.pointerEvents !== 'none';
                if (stock) {
                    const productId = this.getAttribute('data-id');
                    addToCart(productId);
                }
            });
        });
    }
    
    function filterProducts() {
        const categoryId = document.getElementById('category-filter').value;
        const searchValue = document.getElementById('product-search').value.toLowerCase();
        
        let filteredProducts = products;
        
        if (categoryId) {
            filteredProducts = filteredProducts.filter(product => 
                product.category_id == categoryId
            );
        }
        
        if (searchValue) {
            filteredProducts = filteredProducts.filter(product => 
                product.name.toLowerCase().includes(searchValue) || 
                (product.barcode && product.barcode.includes(searchValue)) ||
                (product.product_code && product.product_code.includes(searchValue))
            );
        }
        
        displayProducts(filteredProducts);
    }
    
    function searchProduct() {
        filterProducts();
    }
    
    function addToCart(productId) {
        const product = products.find(p => p.id == productId);
        if (!product) {
            flashMessage('error', 'Product not found');
            return;
        }
        
        if (product.base_stock <= 0) {
            flashMessage('warning', 'Product is out of stock');
            return;
        }
        
        const price = parseFloat(product.price) || 0;
        
        const existingItemIndex = cart.findIndex(item => item.product_id == productId);
        
        if (existingItemIndex !== -1) {
            if (cart[existingItemIndex].quantity >= product.base_stock) {
                flashMessage('warning', 'Not enough stock available');
                return;
            }
            
            cart[existingItemIndex].quantity += 1;
            cart[existingItemIndex].total = cart[existingItemIndex].quantity * cart[existingItemIndex].price;
        } else {
            cart.push({
                product_id: productId,
                name: product.name,
                price: price,
                quantity: 1,
                total: price * 1
            });
        }
        
        updateCartDisplay();
        calculateTotal();
        flashMessage('success', 'Product added to cart');
    }
    
    function updateCartDisplay() {
        const cartItems = document.getElementById('cart-items');
        cartItems.innerHTML = '';

        if (cart.length === 0) {
            cartItems.innerHTML = `
                <tr id="empty-cart-row">
                    <td colspan="5">
                        <div id="empty-cart-container" class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p class="mb-0 fs-5">Cart is empty</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.classList.add('align-middle');

            row.innerHTML = `
                <td>
                    <div class="fw-medium">${item.name}</div>
                </td>
                <td>Rp ${numberFormat(item.price)}</td>
                <td>
                    <div class="input-group input-group-sm cart-item-quantity">
                        <button class="btn btn-outline-secondary decrease-qty" type="button" data-index="${index}">-</button>
                        <input type="number" class="form-control text-center" value="${item.quantity}" min="1" data-index="${index}">
                        <button class="btn btn-outline-secondary increase-qty" type="button" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="fw-medium">Rp ${numberFormat(item.total)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger remove-item" title="Remove item" data-index="${index}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            cartItems.appendChild(row);
        });

        document.querySelectorAll('.decrease-qty').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                decreaseQuantity(index);
            });
        });

        document.querySelectorAll('.increase-qty').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                increaseQuantity(index);
            });
        });

        document.querySelectorAll('.cart-item-quantity input').forEach(input => {
            input.addEventListener('change', function() {
                const index = this.getAttribute('data-index');
                const newQty = parseInt(this.value, 10);
                if (!isNaN(newQty) && newQty > 0) {
                    updateQuantity(index, newQty);
                } else {
                    this.value = cart[index].quantity;
                }
            });
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                removeFromCart(index);
            });
        });
    }
    
    function decreaseQuantity(index) {
        if (cart[index].quantity > 1) {
            cart[index].quantity -= 1;
            const price = parseFloat(cart[index].price) || 0;
            cart[index].total = cart[index].quantity * price;
            updateCartDisplay();
            calculateTotal();
        }
    }
    
    function increaseQuantity(index) {
        const productId = cart[index].product_id;
        const product = products.find(p => p.id == productId);
        
        if (cart[index].quantity >= product.base_stock) {
            flashMessage('warning', 'Not enough stock available');
            return;
        }
        
        cart[index].quantity += 1;
        const price = parseFloat(cart[index].price) || 0;
        cart[index].total = cart[index].quantity * price;
        updateCartDisplay();
        calculateTotal();
    }
    
    function updateQuantity(index, newQty) {
        const productId = cart[index].product_id;
        const product = products.find(p => p.id == productId);
        
        if (newQty > product.base_stock) {
            flashMessage('warning', 'Not enough stock available');
            document.querySelector(`.cart-item-quantity[data-index="${index}"]`).value = cart[index].quantity;
            return;
        }
        
        const price = parseFloat(cart[index].price) || 0;
        cart[index].quantity = newQty;
        cart[index].total = price * newQty;
        updateCartDisplay();
        calculateTotal();
    }
    
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartDisplay();
        calculateTotal();
        flashMessage('info', 'Item removed from cart');
    }
    
    function clearCart() {
        if (cart.length === 0) {
            flashMessage('warning', 'Cart is already empty');
            return;
        }
        
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = [];
            updateCartDisplay();
            calculateTotal();
            document.getElementById('percent-discount').value = 0;
            document.getElementById('amount-discount').value = 0;
            document.getElementById('payment-amount').value = 0;
            flashMessage('success', 'Cart cleared');
        }
    }
    
    function calculateTotal() {
        const subtotal = cart.reduce((sum, item) => {
            const itemTotal = parseFloat(item.total) || 0;
            return sum + itemTotal;
        }, 0);
        
        const percentDiscount = parseFloat(document.getElementById('percent-discount').value) || 0;
        const amountDiscount = parseFloat(document.getElementById('amount-discount').value) || 0;
        
        let finalDiscount = amountDiscount;
        if (percentDiscount > 0 && amountDiscount === 0) {
            finalDiscount = subtotal * (percentDiscount / 100);
            document.getElementById('amount-discount').value = finalDiscount.toFixed(0);
        }
        
        const total = subtotal - finalDiscount;
        
        document.getElementById('subtotal').textContent = 'Rp ' + numberFormat(subtotal);
        document.getElementById('total-amount').textContent = 'Rp ' + numberFormat(total);
        
        calculateChange();
    }
    
    function calculateChange() {
        const totalText = document.getElementById('total-amount').textContent;
        const total = parseFloat(totalText.replace('Rp ', '').replace(/\./g, '')) || 0;
        const inputPayment = parseFloat(document.getElementById('payment-amount').value) || 0;
        const change = inputPayment - total;
        
        document.getElementById('change-amount').textContent = 'Rp ' + numberFormat(change);
        
        const changeElement = document.getElementById('change-amount');
        if (change < 0) {
            changeElement.classList.add('text-danger');
            changeElement.classList.remove('text-success');
        } else {
            changeElement.classList.add('text-success');
            changeElement.classList.remove('text-danger');
        }
    }
    
    function processOrder() {
        if (cart.length === 0) {
            flashMessage('warning', 'Cart is empty');
            return;
        }
        
        const subtotal = cart.reduce((sum, item) => {
            const itemTotal = parseFloat(item.total) || 0;
            return sum + itemTotal;
        }, 0);
        
        const percentDiscount = parseFloat(document.getElementById('percent-discount').value) || 0;
        const amountDiscount = parseFloat(document.getElementById('amount-discount').value) || 0;
        const total = subtotal - amountDiscount;
        const inputPayment = parseFloat(document.getElementById('payment-amount').value) || 0;
        
        if (inputPayment < total) {
            flashMessage('error', 'Payment amount is less than total');
            return;
        }
        
        const cashId = document.getElementById('cash-select').value;
        if (!cashId) {
            flashMessage('error', 'Please select a cash account');
            return;
        }
        
        const orderData = {
            customer_id: document.getElementById('customer-select').value || null,
            cash_id: cashId,
            total_cost_before: subtotal,
            percent_discount: percentDiscount,
            amount_discount: amountDiscount,
            input_payment: inputPayment,
            return_payment: inputPayment - total,
            total_cost: total,
            type_payment: document.getElementById('payment-type').value,
            description: document.getElementById('order-notes').value,
            items: cart
        };
        
        const btn = document.getElementById('checkout-btn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
        
        fetch(storeOrderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                flashMessage('success', data.message);
                
                document.getElementById('order-number').textContent = data.order_number;
                document.getElementById('print-receipt-btn').setAttribute('data-id', data.order_id);
                const successModal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
                successModal.show();
                
                document.getElementById('customer-select').value = '';
                document.getElementById('order-notes').value = '';
                document.getElementById('percent-discount').value = 0;
                document.getElementById('amount-discount').value = 0;
                document.getElementById('payment-amount').value = 0;
                cart = [];
                updateCartDisplay();
                calculateTotal();
            } else {
                flashMessage('error', data.message || 'Failed to process order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            flashMessage('error', 'An error occurred while processing the order');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    function numberFormat(num) {
        const number = parseFloat(num) || 0;
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    function flashMessage(type, message) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '11';
        
        const bgClass = type === 'success' ? 'bg-success' : 
                        type === 'error' ? 'bg-danger' : 
                        type === 'warning' ? 'bg-warning' : 'bg-info';
        
        toastContainer.innerHTML = `
            <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        document.body.appendChild(toastContainer);
        
        const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
        toast.show();
        
        toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', () => {
            toastContainer.remove();
        });
    }
});