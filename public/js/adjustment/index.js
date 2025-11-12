document.addEventListener("DOMContentLoaded", function () {
    const adjustmentsTable = $("#adjustments-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: adjustmentsDataUrl,
            type: "GET",
            error: function (xhr, error, thrown) {
                console.error("DataTables error:", error);
                console.error("Response:", xhr.responseText);
                flashMessage("error", "Failed to load adjustment data");
            },
        },
        columns: [
            { data: "DT_RowIndex", orderable: false, searchable: false },
            { data: "adjustment_number" },
            { data: "formatted_date" },
            { data: "details_count", orderable: false, searchable: false },
            { data: "description" },
            { data: "action", orderable: false, searchable: false },
        ],
        pageLength: 10,
        responsive: true,
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        initComplete: function () {
            initializeSelect2();
            initializeActionButtons();
        },
    });

    function initializeSelect2() {
        if (typeof $ !== "undefined" && $.fn.select2) {
            $(".select2").select2({
                placeholder: "Select an option",
                width: "100%",
            });
        }
    }

    function initializeActionButtons() {
        $(".view-btn")
            .off("click")
            .on("click", function () {
                const adjustmentId = $(this).data("id");

                fetch(adjustmentsViewUrl.replace(":id", adjustmentId), {
                    method: "GET",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const adjustment = data.data;
                            const viewAdjustmentNumber =
                                document.getElementById(
                                    "view_adjustment_number"
                                );
                            if (viewAdjustmentNumber)
                                viewAdjustmentNumber.textContent =
                                    adjustment.adjustment_number;

                            const viewAdjustmentDate = document.getElementById(
                                "view_adjustment_date"
                            );
                            if (viewAdjustmentDate)
                                viewAdjustmentDate.textContent = formatDate(
                                    adjustment.adjustment_date
                                );

                            const viewTotalItems =
                                document.getElementById("view_total_items");
                            if (viewTotalItems)
                                viewTotalItems.textContent = adjustment.details
                                    ? adjustment.details.length
                                    : 0;

                            const viewDescription =
                                document.getElementById("view_description");
                            if (viewDescription)
                                viewDescription.textContent =
                                    adjustment.description || "-";

                            const imageContainer = document.getElementById(
                                "view_image_container"
                            );
                            if (imageContainer) {
                                if (adjustment.image) {
                                    imageContainer.innerHTML = `<img src="${window.location.origin}/storage/${adjustment.image}" class="w-100 h-100" style="object-fit:cover;">`;
                                } else {
                                    imageContainer.innerHTML =
                                        '<i class="fas fa-image fa-3x text-secondary"></i>';
                                }
                            }

                            const itemsContainer = document.getElementById(
                                "view_items_container"
                            );
                            if (itemsContainer) {
                                itemsContainer.innerHTML = "";

                                adjustment.details.forEach((item) => {
                                    const row = document.createElement("tr");
                                    row.innerHTML = `
                                <td>${item.name}</td>
                                <td>${item.product_code}</td>
                                <td>${item.quantity > 0 ? "+" : ""}${
                                        item.quantity
                                    }</td>
                                <td>${item.reason || "-"}</td>
                            `;
                                    itemsContainer.appendChild(row);
                                });
                            }

                            const viewAdjustmentModal = document.getElementById(
                                "viewAdjustmentModal"
                            );
                            if (viewAdjustmentModal) {
                                const modal = new bootstrap.Modal(
                                    viewAdjustmentModal
                                );
                                modal.show();
                            }
                        } else {
                            flashMessage(
                                "error",
                                data.message || "Failed to load adjustment data"
                            );
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        flashMessage("error", "Failed to load adjustment data");
                    });
            });

        $(".edit-btn")
            .off("click")
            .on("click", function () {
                const adjustmentId = $(this).data("id");

                fetch(adjustmentsEditUrl.replace(":id", adjustmentId), {
                    method: "GET",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const adjustment = data.data;

                            const editAdjustmentForm =
                                document.getElementById("editAdjustmentForm");
                            if (editAdjustmentForm) {
                                editAdjustmentForm.reset();
                            }

                            const imageContainerEdit = document.getElementById(
                                "image-container-edit"
                            );
                            if (imageContainerEdit) {
                                imageContainerEdit.style.opacity = "1";
                                imageContainerEdit.style.border = "";

                                const indicator =
                                    document.querySelector(".remove-indicator");
                                if (indicator) {
                                    indicator.remove();
                                }
                            }

                            const removeImageCheckbox =
                                document.getElementById("remove_image_edit");
                            if (removeImageCheckbox) {
                                removeImageCheckbox.checked = false;
                            }

                            const editAdjustmentId =
                                document.getElementById("edit_adjustment_id");
                            if (editAdjustmentId)
                                editAdjustmentId.value = adjustment.id;

                            const editAdjustmentNumber =
                                document.getElementById(
                                    "edit_adjustment_number"
                                );
                            if (editAdjustmentNumber)
                                editAdjustmentNumber.value =
                                    adjustment.adjustment_number;

                            const editAdjustmentDate = document.getElementById(
                                "edit_adjustment_date"
                            );
                            if (editAdjustmentDate) {
                                const date = new Date(
                                    adjustment.adjustment_date
                                );
                                const year = date.getFullYear();
                                const month = String(
                                    date.getMonth() + 1
                                ).padStart(2, "0");
                                const day = String(date.getDate()).padStart(
                                    2,
                                    "0"
                                );
                                editAdjustmentDate.value = `${year}-${month}-${day}`;
                            }

                            const editDescription =
                                document.getElementById("edit_description");
                            if (editDescription)
                                editDescription.value =
                                    adjustment.description || "";

                            if (imageContainerEdit) {
                                if (adjustment.image) {
                                    imageContainerEdit.innerHTML = `<img src="${window.location.origin}/storage/${adjustment.image}" class="w-100 h-100" style="object-fit:cover;">`;
                                } else {
                                    imageContainerEdit.innerHTML =
                                        '<i class="fas fa-image fa-2x text-secondary"></i>';
                                }
                            }

                            const editItemsTable =
                                document.getElementById("editItemsTable");
                            if (editItemsTable) {
                                const tbody =
                                    editItemsTable.getElementsByTagName(
                                        "tbody"
                                    )[0];
                                if (tbody) {
                                    tbody.innerHTML = "";

                                    adjustment.details.forEach(
                                        (item, index) => {
                                            const row =
                                                document.createElement("tr");
                                            row.className = "item-row";
                                            row.innerHTML = `
                                    <td>
                                        <select name="items[${index}][product_id]" class="form-control product-select" required>
                                            <option value="">Select Product</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control current-stock-input" value="${
                                            item.product
                                                ? item.product.base_stock
                                                : "0"
                                        }" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][quantity]" class="form-control quantity-input" value="${
                                                item.quantity
                                            }" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[${index}][reason]" class="form-control" value="${
                                                item.reason || ""
                                            }">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                `;
                                            tbody.appendChild(row);
                                        }
                                    );

                                    updateProductSelects();
                                    initializeSelect2();

                                    adjustment.details.forEach(
                                        (item, index) => {
                                            const select = tbody.querySelector(
                                                `select[name="items[${index}][product_id]"]`
                                            );
                                            if (select) {
                                                $(select)
                                                    .val(item.product_id)
                                                    .trigger("change");
                                            }
                                        }
                                    );
                                }
                            }

                            const editAdjustmentModal = document.getElementById(
                                "editAdjustmentModal"
                            );
                            if (editAdjustmentModal) {
                                const modal = new bootstrap.Modal(
                                    editAdjustmentModal
                                );
                                modal.show();
                            }
                        } else {
                            flashMessage(
                                "error",
                                data.message || "Failed to load adjustment data"
                            );
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        flashMessage("error", "Failed to load adjustment data");
                    });
            });

        $(".delete-btn")
            .off("click")
            .on("click", function () {
                const adjustmentId = $(this).data("id");
                const adjustmentNumber = $(this).data("number");

                confirmDialog(
                    "Delete Stock Adjustment",
                    `Are you sure you want to delete adjustment "${adjustmentNumber}"?`
                ).then((result) => {
                    if (result.isConfirmed) {
                        fetch(
                            adjustmentsDeleteUrl.replace(":id", adjustmentId),
                            {
                                method: "DELETE",
                                headers: { "X-CSRF-TOKEN": csrfToken },
                            }
                        )
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    adjustmentsTable.ajax.reload(() => {
                                        initializeActionButtons();
                                        flashMessage("success", data.message);
                                    }, false);
                                } else {
                                    flashMessage(
                                        "error",
                                        data.message || "An error occurred"
                                    );
                                }
                            })
                            .catch((error) => {
                                console.error("Error:", error);
                                flashMessage("error", "An error occurred");
                            });
                    }
                });
            });
    }

    adjustmentsTable.on("draw", function () {
        initializeActionButtons();
    });

    let products = [];
    fetch(productsUrl)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                products = data.data;
                updateProductSelects();
            } else {
                flashMessage(
                    "error",
                    data.message || "Failed to load products"
                );
            }
        })
        .catch((error) => {
            console.error("Error loading products:", error);
            flashMessage("error", "Failed to load products");
        });

    function updateProductSelects() {
        const productSelects = document.querySelectorAll(".product-select");
        productSelects.forEach((select) => {
            const currentValue = select.value;

            while (select.options.length > 1) {
                select.remove(1);
            }

            products.forEach((product) => {
                const option = document.createElement("option");
                option.value = product.id;
                option.textContent = `${product.product_code} - ${product.name}`;
                option.dataset.currentStock = product.base_stock;
                select.appendChild(option);
            });

            select.value = currentValue;

            select.addEventListener("change", function () {
                const row = this.closest("tr");
                if (row) {
                    const currentStockInput = row.querySelector(
                        ".current-stock-input"
                    );

                    if (currentStockInput && this.options[this.selectedIndex]) {
                        const currentStock =
                            this.options[this.selectedIndex].dataset
                                .currentStock || 0;
                        currentStockInput.value = currentStock;
                    }
                }
            });

            if (select.value) {
                select.dispatchEvent(new Event("change"));
            }
        });
    }

    const addAdjustmentForm = document.getElementById("addAdjustmentForm");
    if (addAdjustmentForm) {
        addAdjustmentForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector(
                "#addAdjustmentModal .btn-submit"
            );

            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector(".spinner-border");
                const text = btn.querySelector(".btn-text");
                const loadingText = btn.getAttribute("data-loading-text");

                if (spinner) spinner.classList.remove("d-none");
                if (text) text.textContent = loadingText;
            }

            fetch(adjustmentsStoreUrl, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const addAdjustmentModal =
                            document.getElementById("addAdjustmentModal");
                        if (addAdjustmentModal) {
                            const modal =
                                bootstrap.Modal.getInstance(addAdjustmentModal);
                            if (modal) modal.hide();
                        }

                        this.reset();
                        resetItemsTable();

                        adjustmentsTable.ajax.reload(() => {
                            initializeActionButtons();
                            flashMessage("success", data.message);
                        }, false);
                    } else {
                        flashMessage(
                            "error",
                            data.message || "An error occurred"
                        );
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    flashMessage("error", "An error occurred");
                })
                .finally(() => {
                    if (btn) {
                        const spinner = btn.querySelector(".spinner-border");
                        const text = btn.querySelector(".btn-text");

                        if (spinner) spinner.classList.add("d-none");
                        if (text) text.textContent = "Save Adjustment";
                        btn.disabled = false;
                    }
                });
        });
    }

    const editAdjustmentForm = document.getElementById("editAdjustmentForm");
    if (editAdjustmentForm) {
        editAdjustmentForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const adjustmentId = document.getElementById("edit_adjustment_id");
            const id = adjustmentId ? adjustmentId.value : "";
            const formData = new FormData(this);
            const btn = document.querySelector(
                "#editAdjustmentModal .btn-submit"
            );

            const removeCheckbox = document.getElementById("remove_image_edit");
            formData.append(
                "remove_image",
                removeCheckbox && removeCheckbox.checked ? "1" : "0"
            );

            console.log("FormData before sending:");
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": ", pair[1]);
            }

            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector(".spinner-border");
                const text = btn.querySelector(".btn-text");
                const loadingText = btn.getAttribute("data-loading-text");

                if (spinner) spinner.classList.remove("d-none");
                if (text) text.textContent = loadingText;
            }

            fetch(adjustmentsUpdateUrl.replace(":id", id), {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((err) => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        const editAdjustmentModal = document.getElementById(
                            "editAdjustmentModal"
                        );
                        if (editAdjustmentModal) {
                            const modal =
                                bootstrap.Modal.getInstance(
                                    editAdjustmentModal
                                );
                            if (modal) modal.hide();
                        }

                        adjustmentsTable.ajax.reload(() => {
                            initializeActionButtons();
                            flashMessage("success", data.message);
                        }, false);
                    } else {
                        if (data.errors) {
                            let errorMessage = "Validation errors:<br>";
                            for (let field in data.errors) {
                                errorMessage += `- ${field}: ${data.errors[
                                    field
                                ].join(", ")}<br>`;
                            }
                            flashMessage("error", errorMessage);
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
                    flashMessage(
                        "error",
                        "An error occurred: " +
                            (error.message || "Unknown error")
                    );
                })
                .finally(() => {
                    if (btn) {
                        const spinner = btn.querySelector(".spinner-border");
                        const text = btn.querySelector(".btn-text");

                        if (spinner) spinner.classList.add("d-none");
                        if (text) text.textContent = "Update Adjustment";
                        btn.disabled = false;
                    }
                });
        });
    }

    const addItemBtn = document.getElementById("addItemBtn");
    if (addItemBtn) {
        addItemBtn.addEventListener("click", function () {
            const itemsTable = document.getElementById("itemsTable");
            if (itemsTable) {
                const tbody = itemsTable.getElementsByTagName("tbody")[0];
                if (tbody) {
                    const rowCount = tbody.rows.length;

                    const row = document.createElement("tr");
                    row.className = "item-row";
                    row.innerHTML = `
                        <td>
                            <select name="items[${rowCount}][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control current-stock-input" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity-input" required>
                        </td>
                        <td>
                            <input type="text" name="items[${rowCount}][reason]" class="form-control">
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
                }
            }
        });
    }

    const editAddItemBtn = document.getElementById("editAddItemBtn");
    if (editAddItemBtn) {
        editAddItemBtn.addEventListener("click", function () {
            const itemsTable = document.getElementById("editItemsTable");
            if (itemsTable) {
                const tbody = itemsTable.getElementsByTagName("tbody")[0];
                if (tbody) {
                    const rowCount = tbody.rows.length;

                    const row = document.createElement("tr");
                    row.className = "item-row";
                    row.innerHTML = `
                        <td>
                            <select name="items[${rowCount}][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control current-stock-input" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity-input" required>
                        </td>
                        <td>
                            <input type="text" name="items[${rowCount}][reason]" class="form-control">
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
                }
            }
        });
    }

    document.addEventListener("click", function (e) {
        if (e.target.closest(".remove-item-btn")) {
            const row = e.target.closest("tr");
            if (row) {
                row.remove();
            }
        }
    });

    function resetItemsTable() {
        const itemsTable = document.getElementById("itemsTable");
        if (itemsTable) {
            const tbody = itemsTable.getElementsByTagName("tbody")[0];
            if (tbody) {
                tbody.innerHTML = `
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control current-stock-input" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" required>
                        </td>
                        <td>
                            <input type="text" name="items[0][reason]" class="form-control">
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
            }
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "long",
            year: "numeric",
        });
    }

    function previewAdjustmentImage(input, type) {
        if (input.files && input.files[0]) {
            const fileType = input.files[0].type;
            const validImageTypes = [
                "image/jpeg",
                "image/png",
                "image/jpg",
                "image/gif",
            ];

            if (!validImageTypes.includes(fileType)) {
                flashMessage(
                    "error",
                    "Please select a valid image file (JPEG, PNG, JPG, GIF)"
                );
                input.value = ""; 
                return;
            }

            const maxSize = 2 * 1024 * 1024; 
            if (input.files[0].size > maxSize) {
                flashMessage("error", "Image size must be less than 2MB");
                input.value = ""; 
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                let container;
                if (type === "new") {
                    container = document.getElementById("image-container-new");
                } else if (type === "edit") {
                    container = document.getElementById("image-container-edit");
                }

                if (container) {
                    container.innerHTML = `
                        <img src="${e.target.result}" 
                             class="w-100 h-100" 
                             style="object-fit:cover;">
                    `;
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    window.clearAdjustmentImagePreview = function (type) {
        let container;
        let input;

        if (type === "new") {
            container = document.getElementById("image-container-new");
            input = document.querySelector('input[name="image"]');
        } else if (type === "edit") {
            container = document.getElementById("image-container-edit");
            input = document.getElementById("image-edit");
        }

        if (container) {
            container.innerHTML =
                '<i class="fas fa-image fa-2x text-secondary"></i>';
        }

        if (input) {
            input.value = "";
        }

        if (type === "edit") {
            const removeCheckbox = document.getElementById("remove_image_edit");
            if (removeCheckbox) {
                removeCheckbox.checked = true;
                updateRemoveImageIndicator(true);
            }
        }
    };

    const addImageInput = document.querySelector('input[name="image"]');
    if (addImageInput) {
        addImageInput.addEventListener("change", function () {
            previewAdjustmentImage(this, "new");
        });
    }

    const editImageInput = document.getElementById("image-edit");
    if (editImageInput) {
        editImageInput.addEventListener("change", function () {
            previewAdjustmentImage(this, "edit");
            const removeCheckbox = document.getElementById("remove_image_edit");
            if (removeCheckbox) {
                removeCheckbox.checked = false;
                updateRemoveImageIndicator(false);
            }
        });
    }

    window.updateRemoveImageIndicator = function (isChecked) {
        const imageContainer = document.getElementById("image-container-edit");
        if (imageContainer) {
            if (isChecked) {
                imageContainer.style.opacity = "0.5";
                imageContainer.style.border = "2px dashed #dc3545";

                if (!document.querySelector(".remove-indicator")) {
                    const indicator = document.createElement("div");
                    indicator.className =
                        "remove-indicator text-danger text-center mt-1";
                    indicator.textContent = "Will be removed on save";
                    imageContainer.parentNode.appendChild(indicator);
                }
            } else {
                imageContainer.style.opacity = "1";
                imageContainer.style.border = "";

                const indicator = document.querySelector(".remove-indicator");
                if (indicator) {
                    indicator.remove();
                }
            }
        }
    };
});
