let cart = [];
let currentProduct = null;

$(document).ready(function () {
    loadProducts();

    $("#search-btn").click(function () {
        loadProducts();
    });

    $("#product-search").keypress(function (e) {
        if (e.which == 13) {
            loadProducts();
        }
    });

    $("#category-filter").change(function () {
        loadProducts();
    });

    $("#clear-cart-btn").click(function () {
        if (cart.length > 0) {
            if (confirm("Are you sure you want to clear the cart?")) {
                cart = [];
                updateCartDisplay();
                updateOrderSummary();
            }
        }
    });

    $("#percent-discount").on("input", function () {
        const percent = parseFloat($(this).val()) || 0;
        const subtotal = calculateSubtotal();
        const amount = subtotal * (percent / 100);
        $("#amount-discount").val(amount.toFixed(0));
        updateOrderSummary();
    });

    $("#amount-discount").on("input", function () {
        const amount = parseFloat($(this).val()) || 0;
        const subtotal = calculateSubtotal();
        const percent = subtotal > 0 ? (amount / subtotal) * 100 : 0;
        $("#percent-discount").val(percent.toFixed(2));
        updateOrderSummary();
    });

    $("#payment-amount").on("input", function () {
        updateOrderSummary();
    });

    $("#decrease-qty").click(function () {
        const qtyInput = $("#product-quantity");
        let qty = parseInt(qtyInput.val());
        if (qty > 1) {
            qtyInput.val(qty - 1);
            updateProductSubtotal();
        }
    });

    $("#increase-qty").click(function () {
        const qtyInput = $("#product-quantity");
        const stock = parseInt($("#product-stock").data("stock")) || 0;
        let qty = parseInt(qtyInput.val());
        if (qty < stock) {
            qtyInput.val(qty + 1);
            updateProductSubtotal();
        }
    });

    $("#product-quantity").on("input", function () {
        updateProductSubtotal();
    });

    $("#product-price").on("input", function () {
        updateProductSubtotal();
    });

    $("#add-to-cart-btn").click(function () {
        if (currentProduct) {
            const quantity = parseInt($("#product-quantity").val());
            const price = parseFloat($("#product-price").val());
            const unitId = parseInt($("#product-unit").val());
            const unitName = $("#product-unit option:selected").text();

            if (quantity > 0 && price >= 0) {
                addToCart(currentProduct, quantity, price, unitId, unitName);
                $("#productModal").modal("hide");
            }
        }
    });

    $("#checkout-btn").click(function () {
        processOrder();
    });

    $("#print-receipt-btn").click(function (e) {
        e.preventDefault();
        const orderId = $(this).data("order-id");
        if (orderId) {
            window.open(printReceiptUrl.replace(":id", orderId), "_blank");
        }
    });
});

function loadProducts() {
    const search = $("#product-search").val();
    const categoryId = $("#category-filter").val();

    $.ajax({
        url: productsUrl,
        type: "GET",
        data: {
            search: search,
            category_id: categoryId,
        },
        success: function (response) {
            displayProducts(response);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            $("#product-container").html(
                '<div class="alert alert-danger">Error loading products</div>'
            );
        },
    });
}

function displayProducts(products) {
    let html = "";

    if (products.length === 0) {
        html = '<div class="alert alert-info">No products found</div>';
    } else {
        html = '<div class="row">';

        products.forEach(function (product) {
            const imageUrl = product.image
                ? `/storage/${product.image}`
                : "https://via.placeholder.com/150x150?text=No+Image";
            const price =
                product.product_units && product.product_units.length > 0
                    ? product.product_units.find((u) => u.is_base)
                          ?.price_before_discount || 0
                    : 0;

            html += `
                <div class="col-6 col-md-4 col-lg-4 mt-4">
                    <div class="card product-card h-100 border-0 shadow-sm" data-id="${
                        product.id
                    }">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 100px; overflow: hidden;">
                            <img src="${imageUrl}" class="img-fluid" alt="${
                product.name
            }" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-body p-2 d-flex flex-column justify-content-center text-start">
    <h6 class="card-title small">${product.name}</h6>
    <span class="fw-bold small text-primary mt-1">Rp ${numberFormat(
        price
    )}</span>
</div>

                    </div>
                </div>
            `;
        });

        html += "</div>";
    }

    $("#product-container").html(html);

    $(".product-card").click(function () {
        const productId = $(this).data("id");
        showProductModal(productId);
    });
}

function showProductModal(productId) {
    $.ajax({
        url: productUrl.replace(":id", productId),
        type: "GET",
        success: function (response) {
            currentProduct = response;

            $("#product-name").text(response.name);
            $("#product-code").text(
                "Code: " + (response.product_code || "N/A")
            );
            $("#product-description").text(response.description || "");
            $("#product-stock")
                .text(response.base_stock || 0)
                .data("stock", response.base_stock || 0);

            let imagesHtml = "";
            if (response.images && response.images.length > 0) {
                imagesHtml =
                    '<div id="product-carousel" class="carousel slide" data-bs-ride="carousel">';
                imagesHtml += '<div class="carousel-inner">';

                response.images.forEach(function (image, index) {
                    const activeClass = index === 0 ? "active" : "";
                    imagesHtml += `<div class="carousel-item ${activeClass}">`;
                    imagesHtml += `<img src="/storage/${
                        image.image
                    }" class="d-block w-100" alt="${
                        image.description || "Product Image"
                    }">`;
                    imagesHtml += "</div>";
                });

                imagesHtml += "</div>";
                imagesHtml +=
                    '<button class="carousel-control-prev" type="button" data-bs-target="#product-carousel" data-bs-slide="prev">';
                imagesHtml +=
                    '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                imagesHtml += '<span class="visually-hidden">Previous</span>';
                imagesHtml += "</button>";
                imagesHtml +=
                    '<button class="carousel-control-next" type="button" data-bs-target="#product-carousel" data-bs-slide="next">';
                imagesHtml +=
                    '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                imagesHtml += '<span class="visually-hidden">Next</span>';
                imagesHtml += "</button>";
                imagesHtml += "</div>";
            } else {
                const imageUrl = response.image
                    ? `/storage/${response.image}`
                    : "https://via.placeholder.com/300x300?text=No+Image";
                imagesHtml = `<img src="${imageUrl}" class="img-fluid" alt="${response.name}">`;
            }

            $("#product-images").html(imagesHtml);

            let unitsHtml = "";
            if (response.product_units && response.product_units.length > 0) {
                response.product_units.forEach(function (unit) {
                    const selected = unit.is_base ? "selected" : "";
                    unitsHtml += `<option value="${unit.unit_id}" ${selected}>${
                        unit.unit.name
                    } (Rp ${numberFormat(
                        unit.price_before_discount
                    )})</option>`;
                });
            } else {
                unitsHtml = '<option value="">No units available</option>';
            }

            $("#product-unit").html(unitsHtml);

            $("#product-quantity").val(1);

            const baseUnit = response.product_units
                ? response.product_units.find((u) => u.is_base)
                : null;
            const defaultPrice = baseUnit ? baseUnit.price_before_discount : 0;
            $("#product-price").val(defaultPrice);

            updateProductSubtotal();

            $("#productModal").modal("show");
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert("Error loading product details");
        },
    });
}

function updateProductSubtotal() {
    const quantity = parseInt($("#product-quantity").val()) || 0;
    const price = parseFloat($("#product-price").val()) || 0;
    const subtotal = quantity * price;

    $("#product-subtotal").text("Rp " + numberFormat(subtotal));
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

    if (cart.length === 0) {
        html =
            '<tr id="empty-cart"><td colspan="6" class="text-center">No items in cart</td></tr>';
    } else {
        cart.forEach(function (item, index) {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div>${item.name}</div>
                        <small class="text-muted">${item.product_code} | ${
                item.unit_name
            }</small>
                    </td>
                    <td>Rp ${numberFormat(item.price)}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary decrease-qty-btn" data-index="${index}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center cart-quantity" 
                                data-index="${index}" value="${
                item.quantity
            }" min="1">
                            <button class="btn btn-outline-secondary increase-qty-btn" data-index="${index}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td>Rp ${numberFormat(item.total_price)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger remove-item-btn" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $("#cart-items").html(html);

    $(".decrease-qty-btn").click(function () {
        const index = $(this).data("index");
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
            cart[index].total_price = cart[index].quantity * cart[index].price;
            updateCartDisplay();
            updateOrderSummary();
        }
    });

    $(".increase-qty-btn").click(function () {
        const index = $(this).data("index");
        cart[index].quantity++;
        cart[index].total_price = cart[index].quantity * cart[index].price;
        updateCartDisplay();
        updateOrderSummary();
    });

    $(".cart-quantity").on("input", function () {
        const index = $(this).data("index");
        const quantity = parseInt($(this).val()) || 1;

        if (quantity >= 1) {
            cart[index].quantity = quantity;
            cart[index].total_price = cart[index].quantity * cart[index].price;
            updateCartDisplay();
            updateOrderSummary();
        }
    });

    $(".remove-item-btn").click(function () {
        const index = $(this).data("index");
        cart.splice(index, 1);
        updateCartDisplay();
        updateOrderSummary();
    });
}

function calculateSubtotal() {
    return cart.reduce((total, item) => total + item.total_price, 0);
}

function updateOrderSummary() {
    const subtotal = calculateSubtotal();
    const percentDiscount = parseFloat($("#percent-discount").val()) || 0;
    const amountDiscount = parseFloat($("#amount-discount").val()) || 0;
    const total = subtotal - amountDiscount;
    const payment = parseFloat($("#payment-amount").val()) || 0;
    const change = payment - total;

    $("#subtotal").text("Rp " + numberFormat(subtotal));
    $("#total-amount").html("<strong>Rp " + numberFormat(total) + "</strong>");
    $("#change-amount").text("Rp " + numberFormat(change));

    if ($("#payment-amount").val() === "" && total > 0) {
        $("#payment-amount").val(total);
    }
}

function processOrder() {
    if (cart.length === 0) {
        alert("Please add at least one product to the cart");
        return;
    }

    const customerId = $("#customer-select").val() || null;
    const cashId = $("#cash-select").val();
    const percentDiscount = parseFloat($("#percent-discount").val()) || 0;
    const amountDiscount = parseFloat($("#amount-discount").val()) || 0;
    const payment = parseFloat($("#payment-amount").val()) || 0;
    const total = calculateSubtotal() - amountDiscount;
    const paymentType = $("#payment-type").val();
    const notes = $("#order-notes").val();

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
        url: storeOrderUrl,
        type: "POST",
        data: JSON.stringify(orderData),
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        success: function (response) {
            if (response.success) {
                cart = [];
                updateCartDisplay();
                updateOrderSummary();
                $("#customer-select").val("");
                $("#percent-discount").val(0);
                $("#amount-discount").val(0);
                $("#payment-amount").val("");
                $("#payment-type").val("cash");
                $("#order-notes").val("");

                $("#order-number").text(response.order_number);
                $("#print-receipt-btn").data("order-id", response.order_id);
                $("#orderSuccessModal").modal("show");

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
