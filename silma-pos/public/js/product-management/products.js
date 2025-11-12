document.addEventListener("DOMContentLoaded", function () {
    const productsTable = $("#products-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: productsDataUrl,
            type: "GET",
            error: function (xhr, error, thrown) {
                console.error("DataTables error:", error, thrown);
                flashMessage("error", "Error loading products data");
            },
        },
        columns: [
            { data: "no", orderable: false, searchable: false },
            { data: "image_preview", orderable: false, searchable: false },
            { data: "product_code" },
            { data: "name" },
            { data: "category_name" },
            { data: "base_unit_name" },
            { data: "base_stock" },
            { data: "purchase_price", orderable: false, searchable: false },
            { data: "cost_price", orderable: false, searchable: false },
            {
                data: "price_before_discount",
                orderable: false,
                searchable: false,
            },
            { data: "status", orderable: false, searchable: false },
            { data: "action", orderable: false, searchable: false },
        ],
        pageLength: 10,
        responsive: true,
    });

    window.previewProductImage = function (input, type) {
        const container = document.getElementById(`image-container-${type}`);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Add Product 
    const addProductForm = document.getElementById("addProductForm");
    if (addProductForm) {
        addProductForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector("#addProductModal .btn-submit");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(productsStoreUrl, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((data) => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("addProductModal")
                        );
                        modal.hide();
                        this.reset();
                        document.getElementById(
                            "image-container-new"
                        ).innerHTML =
                            '<i class="fas fa-image fa-2x text-secondary"></i>';

                        productsTable.ajax.reload();
                        flashMessage("success", data.message);
                    } else {
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            flashMessage("error", firstError);
                        } else {
                            flashMessage(
                                "error",
                                data.message || "An error occurred"
                            );
                        }
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    if (error.errors) {
                        const firstError = Object.values(error.errors)[0][0];
                        flashMessage("error", firstError);
                    } else {
                        flashMessage(
                            "error",
                            "An error occurred: " +
                                (error.message || "Unknown error")
                        );
                    }
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Add Product";
                    btn.disabled = false;
                });
        });
    }

    // Edit Product Button Click
    document.addEventListener("click", function (e) {
        if (e.target.closest(".edit-btn")) {
            const btn = e.target.closest(".edit-btn");
            const productId = btn.getAttribute("data-id");

            fetch(productsUpdateUrl.replace(":id", productId) + "/edit", {
                method: "GET",
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.text().then((text) => {
                            throw new Error(text);
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log("Product data:", data); 

                    document.getElementById("edit_product_id").value = data.id;
                    document.getElementById("edit_product_code").value =
                        data.product_code;
                    document.getElementById("edit_barcode").value =
                        data.barcode || "";
                    document.getElementById("edit_name").value = data.name;
                    document.getElementById("edit_category_id").value =
                        data.category_id;
                    document.getElementById("edit_base_unit_id").value =
                        data.base_unit_id;
                    document.getElementById("edit_base_stock").value =
                        data.base_stock;
                    document.getElementById("edit_description").value =
                        data.description || "";

                    if (data.base_unit_prices) {
                        const baseUnit = data.base_unit_prices;
                        document.getElementById("edit_purchase_price").value =
                            parseFloat(baseUnit.purchase_price) || 0;
                        document.getElementById("edit_cost_price").value =
                            parseFloat(baseUnit.cost_price) || 0;
                        document.getElementById(
                            "edit_price_before_discount"
                        ).value =
                            parseFloat(baseUnit.price_before_discount) || 0;
                        document.getElementById("edit_unit_note").value =
                            baseUnit.note || "";
                    } else {
                        document.getElementById(
                            "edit_purchase_price"
                        ).value = 0;
                        document.getElementById("edit_cost_price").value = 0;
                        document.getElementById(
                            "edit_price_before_discount"
                        ).value = 0;
                        document.getElementById("edit_unit_note").value = "";
                    }

                    document.getElementById("edit_status_active").checked =
                        !!data.status_active;
                    document.getElementById("edit_status_discount").checked =
                        !!data.status_discount;
                    document.getElementById("edit_status_display").checked =
                        !!data.status_display;
                    document.getElementById("edit_position").value =
                        data.position || "";
                    document.getElementById("edit_reminder").value =
                        data.reminder || "";
                    document.getElementById("edit_expire_date").value =
                        data.expire_date || "";
                    document.getElementById("edit_note").value =
                        data.note || "";
                    document.getElementById("edit_link").value =
                        data.link || "";

                    const imageContainer = document.getElementById(
                        "image-container-edit"
                    );
                    if (data.image) {
                        imageContainer.innerHTML = `<img src="${window.location.origin}/storage/${data.image}" class="w-100 h-100" style="object-fit:cover;">`;
                    } else {
                        imageContainer.innerHTML =
                            '<i class="fas fa-image fa-2x text-secondary"></i>';
                    }

                    const modal = new bootstrap.Modal(
                        document.getElementById("editProductModal")
                    );
                    modal.show();
                })
                .catch((error) => {
                    console.error("Error:", error);
                    flashMessage(
                        "error",
                        "Failed to load product data: " + error.message
                    );
                });
        }
    });

    // Update Product Form Submit
    const editProductForm = document.getElementById("editProductForm");
    if (editProductForm) {
        editProductForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const productId = document.getElementById("edit_product_id").value;
            const formData = new FormData(this);
            formData.append("_method", "PUT");

            const btn = document.querySelector("#editProductModal .btn-submit");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(productsUpdateUrl.replace(":id", productId), {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((data) => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("editProductModal")
                        );
                        modal.hide();

                        productsTable.ajax.reload();
                        flashMessage("success", data.message);
                    } else {
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            flashMessage("error", firstError);
                        } else {
                            flashMessage(
                                "error",
                                data.message || "An error occurred"
                            );
                        }
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    if (error.errors) {
                        const firstError = Object.values(error.errors)[0][0];
                        flashMessage("error", firstError);
                    } else {
                        flashMessage(
                            "error",
                            "An error occurred: " +
                                (error.message || "Unknown error")
                        );
                    }
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Update Product";
                    btn.disabled = false;
                });
        });
    }

    // Delete Product
    document.addEventListener("click", function (e) {
        if (e.target.closest(".delete-btn")) {
            const btn = e.target.closest(".delete-btn");
            const productId = btn.getAttribute("data-id");
            const productName = btn.getAttribute("data-name");

            confirmDialog(
                "Delete Product",
                `Are you sure you want to delete "${productName}"?`
            ).then((result) => {
                if (result.isConfirmed) {
                    fetch(productsDeleteUrl.replace(":id", productId), {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Content-Type": "application/json",
                        },
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.text().then((text) => {
                                    throw new Error(text);
                                });
                            }
                            return response.json();
                        })
                        .then((data) => {
                            if (data.success) {
                                productsTable.ajax.reload();
                                flashMessage("success", data.message);
                            } else {
                                flashMessage(
                                    "error",
                                    data.message || "An error occurred"
                                );
                            }
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                            flashMessage(
                                "error",
                                "An error occurred: " + error.message
                            );
                        });
                }
            });
        }
    });
});
