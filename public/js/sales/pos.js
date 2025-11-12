let cart = [];
let currentProduct = null;
let currentPage = 1;
let perPage = 6;
let isLoading = false;
let hasMoreProducts = true;

 $(document).ready(function () {
    loadCategories();
    loadProducts();

    $("#search-btn").click(function () {
        currentPage = 1;
        hasMoreProducts = true;
        $("#product-container").empty();
        loadProducts();
    });

    $("#product-search").keypress(function (e) {
        if (e.which == 13) {
            currentPage = 1;
            hasMoreProducts = true;
            $("#product-container").empty();
            loadProducts();
        }
    });

    $("#category-filter").change(function () {
        currentPage = 1;
        hasMoreProducts = true;
        $("#product-container").empty();
        loadProducts();
    });

    $("#clear-cart-btn").click(function () {
        if (cart.length > 0) {
            confirmDialog(
                "Clear Cart",
                "Are you sure you want to clear the cart?",
                "Yes, Clear Cart",
                "Cancel"
            ).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    updateCartDisplay();
                    updateOrderSummary();
                    $(".product-quantity").val(0); 
                    flashMessage(
                        "success",
                        "Cart has been cleared successfully"
                    );
                }
            });
        }
    });

    $("#modal-percent-discount").on("input", function () {
        const percent = parseFloat($(this).val()) || 0;
        const subtotal = calculateSubtotal();
        const amount = subtotal * (percent / 100);
        $("#modal-amount-discount").val(amount.toFixed(0));
        updateModalOrderSummary();
    });

    $("#modal-amount-discount").on("input", function () {
        const amount = parseFloat($(this).val()) || 0;
        const subtotal = calculateSubtotal();
        const percent = subtotal > 0 ? (amount / subtotal) * 100 : 0;
        $("#modal-percent-discount").val(percent.toFixed(2));
        updateModalOrderSummary();
    });

    $("#modal-payment-amount").on("input", function () {
        updateModalOrderSummary();
    });

    $("#modal-checkout-btn").click(function () {
        processOrderFromModal();
    });

    $("#checkout-btn").click(function () {
        $("#modal-customer-select").val("10");
        $("#modal-cash-select").val($("#modal-cash-select option:first").val());
        $("#modal-payment-type").val("cash"); 
        
        $("#modal-percent-discount").val(0);
        $("#modal-amount-discount").val(0);
        $("#modal-payment-amount").val("");
        $("#modal-order-notes").val("");

        updateModalOrderSummary();
        $("#orderProcessModal").modal("show");
    });

    $("#print-receipt-btn").click(function (e) {
        e.preventDefault();
        const orderId = $(this).data("order-id");
        if (orderId) {
            window.open(window.posConfig.printReceiptUrl.replace(":id", orderId), "_blank");
        }
    });

    $("#load-more-btn").click(function () {
        if (!isLoading && hasMoreProducts) {
            currentPage++;
            loadProducts();
        }
    });
});

function loadCategories() {
    $.ajax({
        url: window.posConfig.categoriesUrl,
        type: "GET",
        success: function (response) {
            displayCategories(response);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            $("#category-container").html(
                '<div class="alert alert-danger">Error loading categories</div>'
            );
        },
    });
}

function displayCategories(categories) {
    let html = "";

    html += `
        <div class="col-4 col-md-3 col-lg-2">
            <div class="category-card active" data-id="">
                <div class="category-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="category-name">All</div>
            </div>
        </div>
    `;

    categories.forEach(function (category) {
        html += `
            <div class="col-4 col-md-3 col-lg-2">
                <div class="category-card" data-id="${category.id}">
                    <div class="category-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="category-name">${category.name}</div>
                </div>
            </div>
        `;
    });

    $("#category-container").html(html);

    $(".category-card").click(function () {
        $(".category-card").removeClass("active");
        $(this).addClass("active");

        const categoryId = $(this).data("id");
        $("#category-filter").val(categoryId).trigger("change");
    });
}

function loadProducts() {
    if (isLoading) return;

    isLoading = true;
    const search = $("#product-search").val();
    const categoryId = $("#category-filter").val();

    $.ajax({
        url: window.posConfig.productsUrl,
        type: "GET",
        data: {
            search: search,
            category_id: categoryId,
            page: currentPage,
            per_page: perPage,
        },
        success: function (response) {
            displayProducts(response.data);

            if (response.data.length < perPage) {
                hasMoreProducts = false;
                $("#load-more-btn").hide();
            } else {
                $("#load-more-btn").show();
            }

            isLoading = false;
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            if (currentPage === 1) {
                $("#product-container").html(
                    '<div class="alert alert-danger">Error loading products</div>'
                );
            }
            isLoading = false;
        },
    });
}

function displayProducts(products) {
    let html = "";

    if (products.length === 0 && currentPage === 1) {
        html = '<div class="alert alert-info">No products found</div>';
        $("#product-container").html(html);
        $("#load-more-btn").hide();
        return;
    }

    products.forEach(function (product) {
        const imageUrl = product.image
            ? `/storage/${product.image}`
            : "https://via.placeholder.com/400x250?text=No+Image";
        
        const price =
            product.product_units && product.product_units.length > 0
                ? product.product_units.find((u) => u.is_base)
                      ?.price_before_discount || 0
                : 0;

        const cartItem = cart.find(item => item.product_id === product.id);
        const quantity = cartItem ? cartItem.quantity : 0;
        
        const availabilityText = product.note || "Available in stock"; 

        html += `
            <div class="col-6 col-md-4 mt-4"> 
                <div class="card product-card border-0 shadow rounded-3 p-2 d-flex flex-column" 
                     data-id="${product.id}" 
                     style="height: 100%;"> 
                    
                    <div class="product-image-container mb-2" style="overflow: hidden; border-radius: 8px;">
                        <img src="${imageUrl}" class="img-fluid rounded-3 w-100 mb-3" alt="${product.name}" 
                             style="height: 120px; object-fit: cover;">
                    </div>

                    <div class="card-body p-0 d-flex flex-column">
                        <h6 class="card-title fw-bold mb-1">${product.name}</h6>
                        
                        <p class="text-muted mb-1" style="font-size: 0.7rem; line-height: 1.1;">${availabilityText}</p>
                        
                        <h6 class="fw-bold text-primary mb-1 mt-1">Rp ${numberFormat(price)}</h6>
                        
                        <div class="d-flex align-items-center justify-content-center mt-auto">
                            <button class="btn btn-outline-secondary quantity-control minus-btn me-1" 
                                    data-id="${product.id}" 
                                    style="width: 40px; height: 30px; border-radius: 8px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; padding: 0;">
                                -
                            </button>
                            
                            <input type="number" 
                                class="form-control text-center product-quantity text-body" 
                                value="${quantity}" 
                                min="0" 
                                data-id="${product.id}"
                                style="width: 45px; height: 30px; font-weight: bold; font-size: 0.75rem; border-radius: 8px; color: var(--bs-body-color);">
                                
                            <button class="btn btn-primary quantity-control plus-btn ms-1" 
                                    data-id="${product.id}" 
                                    style="width: 40px; height: 30px; border-radius: 8px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; padding: 0;">
                                +
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    if (currentPage === 1) {
        $("#product-container").html(html);
    } else {
        $("#product-container").append(html);
    }

    $(".quantity-control").off('click').on('click', function (e) {
        e.stopPropagation();
        const productId = $(this).data("id");
        const $input = $(`.product-quantity[data-id="${productId}"]`);
        let quantity = parseInt($input.val());

        if ($(this).hasClass("minus-btn")) {
            if (quantity > 0) {
                updateProductQuantity(productId, -1);
            }
        } else if ($(this).hasClass("plus-btn")) {
            updateProductQuantity(productId, 1);
        }
    });

    $(".product-quantity").off('change').on('change', function() {
        const productId = $(this).data("id");
        const newQuantity = parseInt($(this).val()) || 0;
        const $input = $(this);
        
        if (newQuantity > 0) {
            const cartItem = cart.find(item => item.product_id === productId);
            
            if (!cartItem) {
                $.ajax({
                    url: window.posConfig.productUrl.replace(":id", productId),
                    type: "GET",
                    success: function (response) {
                        const baseUnit = response.product_units
                            ? response.product_units.find((u) => u.is_base)
                            : null;

                        if (baseUnit) {
                            addToCart(
                                response,
                                newQuantity, 
                                baseUnit.price_before_discount,
                                baseUnit.unit_id,
                                baseUnit.unit.name
                            );
                            
                            $input.val(newQuantity);
                        } else {
                            flashMessage("error", "No unit available for this product");
                            $input.val(0); 
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        flashMessage("error", "Error loading product details");
                        $input.val(0);
                    },
                });
            } else {
                const currentQuantity = cartItem.quantity;
                const change = newQuantity - currentQuantity;
                
                if (change !== 0) {
                    updateProductQuantity(productId, change);
                }
            }
        } else if (newQuantity === 0) {
            const cartItemIndex = cart.findIndex(item => item.product_id === productId);
            if (cartItemIndex !== -1) {
                cart.splice(cartItemIndex, 1);
                updateCartDisplay();
                updateOrderSummary();
            }
            $input.val(0);
        }
    });
}

function updateProductQuantity(productId, change) {
    const existingItemIndex = cart.findIndex(item => item.product_id === productId);

    if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantity += change;
        
        if (cart[existingItemIndex].quantity <= 0) {
            cart.splice(existingItemIndex, 1);
        } else {
            cart[existingItemIndex].total_price = cart[existingItemIndex].quantity * cart[existingItemIndex].price;
        }

        updateProductQuantityDisplay(productId);
        updateCartDisplay();
        updateOrderSummary();

    } else if (change > 0) {
        
        $.ajax({
            url: window.posConfig.productUrl.replace(":id", productId),
            type: "GET",
            success: function (response) {
                const baseUnit = response.product_units
                    ? response.product_units.find((u) => u.is_base)
                    : null;

                if (baseUnit) {
                    addToCart(
                        response,
                        1,
                        baseUnit.price_before_discount,
                        baseUnit.unit_id,
                        baseUnit.unit.name
                    );

                    updateProductQuantityDisplay(productId);

                } else {
                    flashMessage("error", "No unit available for this product");
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                flashMessage("error", "Error loading product details");
            },
        });
    }
}

function updateProductQuantityDisplay(productId) {
    const $input = $(`.product-quantity[data-id="${productId}"]`);
    const cartItem = cart.find(item => item.product_id === productId);
    const quantity = cartItem ? cartItem.quantity : 0;
    $input.val(quantity);
}

function flashMessage(type, message) {
    const alertClass = type === "success" ? "alert-success" : "alert-danger";
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 250px;" 
             role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    $("body").append(alertHtml);

    setTimeout(function () {
        $(".alert").fadeOut();
    }, 3000);
}

function addToCart(product, quantity, price, unitId, unitName) {
    const existingItemIndex = cart.findIndex(
        (item) => item.product_id === product.id && item.unit_id === unitId
    );

    if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantity += quantity;
        cart[existingItemIndex].total_price =
            cart[existingItemIndex].quantity * cart[existingItemIndex].price;
    } else {
        cart.push({
            product_id: product.id,
            product_code: product.product_code || "",
            name: product.name,
            unit_id: unitId,
            unit_name: unitName,
            quantity: quantity,
            price: price,
            total_price: quantity * price,
        });
    }

    updateCartDisplay();
    updateOrderSummary();
}

function updateCartDisplay() {
    let html = "";
    let cartCount = 0;
    let cartTotal = 0;

    if (cart.length === 0) {
        html = '<div class="text-center text-muted py-3">Cart is empty</div>';
    } else {
        cart.forEach(function (item, index) {
            cartCount += item.quantity;
            cartTotal += item.total_price;
            
            html += `
                <div class="cart-item d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div class="item-info">
                        <div class="fw-bold">${item.name}</div>
                        <small class="text-muted">${item.quantity} x Rp ${numberFormat(item.price)}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="item-total fw-bold me-2">Rp ${numberFormat(item.total_price)}</div>
                        <button class="btn btn-sm btn-outline-danger remove-item-btn" data-index="${index}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `;
        });
    }

    $("#cart-items-container").html(html);
    $("#cart-count").text(cartCount);
    $("#cart-total").text("Rp " + numberFormat(cartTotal));

    $(".remove-item-btn").off('click').on('click', function() {
        const index = $(this).data("index");
        removeCartItem(index);
    });
}

function removeCartItem(index) {
    if (index >= 0 && index < cart.length) {
        const removedItem = cart[index];
        cart.splice(index, 1);
        
        updateProductQuantityDisplay(removedItem.product_id);
        
        updateCartDisplay();
        updateOrderSummary();
        
        flashMessage("success", "Item removed from cart");
    }
}

function calculateSubtotal() {
    return cart.reduce((total, item) => total + item.total_price, 0);
}

function updateOrderSummary() {
    const subtotal = calculateSubtotal();
    const total = subtotal;
    const payment = parseFloat($("#payment-amount").val()) || 0;
    const change = payment - total;

    $("#subtotal").text("Rp " + numberFormat(subtotal));
    $("#total-amount").html("<strong>Rp " + numberFormat(total) + "</strong>");
    $("#change-amount").text("Rp " + numberFormat(change));

    if ($("#payment-amount").val() === "" && total > 0) {
        $("#payment-amount").val(total);
    }
}

function updateModalOrderSummary() {
    const subtotal = calculateSubtotal();
    const percentDiscount = parseFloat($("#modal-percent-discount").val()) || 0;
    const amountDiscount = parseFloat($("#modal-amount-discount").val()) || 0;
    const total = subtotal - amountDiscount;
    const payment = parseFloat($("#modal-payment-amount").val()) || 0;
    const change = payment - total;

    $("#modal-subtotal").text("Rp " + numberFormat(subtotal));
    $("#modal-total-amount").html("<strong>Rp " + numberFormat(total) + "</strong>");
    $("#modal-change-amount").text("Rp " + numberFormat(change));

    if ($("#modal-payment-amount").val() === "" && total > 0) {
        $("#modal-payment-amount").val(total);
    }
}

function processOrderFromModal() {
    if (cart.length === 0) {
        alert("Please add at least one product to the cart");
        return;
    }

    const customerId = $("#modal-customer-select").val() || null;
    const cashId = $("#modal-cash-select").val();
    const percentDiscount = parseFloat($("#modal-percent-discount").val()) || 0;
    const amountDiscount = parseFloat($("#modal-amount-discount").val()) || 0;
    const payment = parseFloat($("#modal-payment-amount").val()) || 0;
    const total = calculateSubtotal() - amountDiscount;
    const paymentType = $("#modal-payment-type").val();
    const notes = $("#modal-order-notes").val();

    if (payment < total) {
        alert("Payment amount is less than total amount");
        return;
    }

    const orderData = {
        customer_id: customerId,
        cash_id: cashId,
        items: cart,
        percent_discount: percentDiscount,
        amount_discount: amountDiscount,
        input_payment: payment,
        type_payment: paymentType,
        description: notes,
    };

    $.ajax({
        url: window.posConfig.storeOrderUrl,
        type: "POST",
        data: JSON.stringify(orderData),
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": window.posConfig.csrfToken,
        },
        success: function (response) {
            if (response.success) {
                cart = [];
                updateCartDisplay();
                updateOrderSummary();
                
                $("#modal-customer-select").val("");
                $("#modal-percent-discount").val(0);
                $("#modal-amount-discount").val(0);
                $("#modal-payment-amount").val("");
                $("#modal-payment-type").val("cash");
                $("#modal-order-notes").val("");
                
                $("#orderProcessModal").modal("hide");

                $("#order-number").text(response.order_number);
                $("#print-receipt-btn").data("order-id", response.order_id);
                $("#orderSuccessModal").modal("show");

                currentPage = 1;
                hasMoreProducts = true;
                $("#product-container").empty();
                loadProducts();
            } else {
                alert("Error: " + response.message);
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert(
                "Error processing order: " +
                    (xhr.responseJSON.message || "Unknown error")
            );
        },
    });
}

function numberFormat(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

function confirmDialog(title, message, confirmText, cancelText) {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });
}