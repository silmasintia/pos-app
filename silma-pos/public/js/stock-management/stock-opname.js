document.addEventListener("DOMContentLoaded", function () {
    const stockOpnamesTable = $("#stock-opnames-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: stockOpnamesDataUrl,
            type: "GET",
            error: function (xhr, error, thrown) {
                console.error("DataTables error:", error);
                console.error("Response:", xhr.responseText);
                flashMessage("error", "Failed to load stock opname data");
            },
        },
        columns: [
            { data: "DT_RowIndex", orderable: false, searchable: false },
            { data: "opname_number" },
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
            $(".select2, .product-select").select2({
                placeholder: "Select Product",
                width: "100%",
                allowClear: true,
            });
        }
    }

    function initializeActionButtons() {
        $(".view-btn")
            .off("click")
            .on("click", function () {
                const stockOpnameId = $(this).data("id");

                fetch(stockOpnamesViewUrl.replace(":id", stockOpnameId), {
                    method: "GET",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const stockOpname = data.data;
                            const viewOpnameNumber =
                                document.getElementById("view_opname_number");
                            if (viewOpnameNumber)
                                viewOpnameNumber.textContent =
                                    stockOpname.opname_number;

                            const viewOpnameDate =
                                document.getElementById("view_opname_date");
                            if (viewOpnameDate)
                                viewOpnameDate.textContent = formatDate(
                                    stockOpname.opname_date
                                );

                            const viewTotalItems =
                                document.getElementById("view_total_items");
                            if (viewTotalItems)
                                viewTotalItems.textContent = stockOpname.details
                                    ? stockOpname.details.length
                                    : 0;

                            const viewDescription =
                                document.getElementById("view_description");
                            if (viewDescription)
                                viewDescription.textContent =
                                    stockOpname.description || "-";

                            const imageContainer = document.getElementById(
                                "view_image_container"
                            );
                            if (imageContainer) {
                                if (stockOpname.image) {
                                    imageContainer.innerHTML = `<img src="${window.location.origin}/${stockOpname.image}" class="w-100 h-100" style="object-fit:cover;">`;
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

                                stockOpname.details.forEach((item) => {
                                    const row = document.createElement("tr");
                                    row.innerHTML = `
                                <td>${
                                    item.product ? item.product.name : "-"
                                }</td>
                                <td>${item.system_stock}</td>
                                <td>${item.physical_stock}</td>
                                <td>${item.difference > 0 ? "+" : ""}${
                                        item.difference
                                    }</td>
                                <td>${item.description_detail || "-"}</td>
                            `;
                                    itemsContainer.appendChild(row);
                                });
                            }

                            const viewStockOpnameModal =
                                document.getElementById("viewStockOpnameModal");
                            if (viewStockOpnameModal) {
                                const modal = new bootstrap.Modal(
                                    viewStockOpnameModal
                                );
                                modal.show();
                            }
                        } else {
                            flashMessage(
                                "error",
                                data.message ||
                                    "Failed to load stock opname data"
                            );
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        flashMessage(
                            "error",
                            "Failed to load stock opname data"
                        );
                    });
            });

        $(".edit-btn")
            .off("click")
            .on("click", function () {
                const stockOpnameId = $(this).data("id");

                fetch(stockOpnamesEditUrl.replace(":id", stockOpnameId), {
                    method: "GET",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const stockOpname = data.data;
                            const editStockOpnameId = document.getElementById(
                                "edit_stock_opname_id"
                            );
                            if (editStockOpnameId)
                                editStockOpnameId.value = stockOpname.id;

                            const editOpnameNumber =
                                document.getElementById("edit_opname_number");
                            if (editOpnameNumber)
                                editOpnameNumber.value =
                                    stockOpname.opname_number;

                            const editOpnameDate =
                                document.getElementById("edit_opname_date");
                            if (editOpnameDate) {
                                const date = new Date(stockOpname.opname_date);
                                const year = date.getFullYear();
                                const month = String(
                                    date.getMonth() + 1
                                ).padStart(2, "0");
                                const day = String(date.getDate()).padStart(
                                    2,
                                    "0"
                                );
                                editOpnameDate.value = `${year}-${month}-${day}`;
                            }

                            const editDescription =
                                document.getElementById("edit_description");
                            if (editDescription)
                                editDescription.value =
                                    stockOpname.description || "";

                            const imageContainerEdit = document.getElementById(
                                "image-container-edit"
                            );
                            if (imageContainerEdit) {
                                if (stockOpname.image) {
                                    imageContainerEdit.innerHTML = `<img src="${window.location.origin}/${stockOpname.image}" class="w-100 h-100" style="object-fit:cover;">`;
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

                                    stockOpname.details.forEach(
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
                                        <input type="text" class="form-control system-stock-input" value="${
                                            item.system_stock
                                        }" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][physical_stock]" class="form-control physical-stock-input" min="0" value="${
                                                item.physical_stock
                                            }" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control difference-input" value="${
                                            item.difference > 0 ? "+" : ""
                                        }${item.difference}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="items[${index}][description_detail]" class="form-control" value="${
                                                item.description_detail || ""
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

                                    stockOpname.details.forEach(
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

                            const editStockOpnameModal =
                                document.getElementById("editStockOpnameModal");
                            if (editStockOpnameModal) {
                                const modal = new bootstrap.Modal(
                                    editStockOpnameModal
                                );
                                modal.show();
                            }
                        } else {
                            flashMessage(
                                "error",
                                data.message ||
                                    "Failed to load stock opname data"
                            );
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        flashMessage(
                            "error",
                            "Failed to load stock opname data"
                        );
                    });
            });

        $(".delete-btn")
            .off("click")
            .on("click", function () {
                const stockOpnameId = $(this).data("id");
                const stockOpnameNumber = $(this).data("number");

                confirmDialog(
                    "Delete Stock Opname",
                    `Are you sure you want to delete stock opname "${stockOpnameNumber}"?`
                ).then((result) => {
                    if (result.isConfirmed) {
                        fetch(
                            stockOpnamesDeleteUrl.replace(":id", stockOpnameId),
                            {
                                method: "DELETE",
                                headers: { "X-CSRF-TOKEN": csrfToken },
                            }
                        )
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    stockOpnamesTable.ajax.reload(() => {
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

    stockOpnamesTable.on("draw", function () {
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
                option.dataset.systemStock = product.base_stock;
                select.appendChild(option);
            });

            select.value = currentValue;

            if (select.value) {
                select.dispatchEvent(new Event("change"));
            }
        });
    }

    const addStockOpnameForm = document.getElementById("addStockOpnameForm");
    if (addStockOpnameForm) {
        addStockOpnameForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector(
                "#addStockOpnameModal .btn-submit"
            );

            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector(".spinner-border");
                const text = btn.querySelector(".btn-text");
                const loadingText = btn.getAttribute("data-loading-text");

                if (spinner) spinner.classList.remove("d-none");
                if (text) text.textContent = loadingText;
            }

            fetch(stockOpnamesStoreUrl, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const addStockOpnameModal = document.getElementById(
                            "addStockOpnameModal"
                        );
                        if (addStockOpnameModal) {
                            const modal =
                                bootstrap.Modal.getInstance(
                                    addStockOpnameModal
                                );
                            if (modal) modal.hide();
                        }

                        this.reset();
                        resetItemsTable();

                        stockOpnamesTable.ajax.reload(() => {
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
                        if (text) text.textContent = "Save Stock Opname";
                        btn.disabled = false;
                    }
                });
        });
    }

    const editStockOpnameForm = document.getElementById("editStockOpnameForm");
    if (editStockOpnameForm) {
        editStockOpnameForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const stockOpnameId = document.getElementById(
                "edit_stock_opname_id"
            );
            const id = stockOpnameId ? stockOpnameId.value : "";
            const formData = new FormData(this);
            const btn = document.querySelector(
                "#editStockOpnameModal .btn-submit"
            );

            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector(".spinner-border");
                const text = btn.querySelector(".btn-text");
                const loadingText = btn.getAttribute("data-loading-text");

                if (spinner) spinner.classList.remove("d-none");
                if (text) text.textContent = loadingText;
            }

            fetch(stockOpnamesUpdateUrl.replace(":id", id), {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const editStockOpnameModal = document.getElementById(
                            "editStockOpnameModal"
                        );
                        if (editStockOpnameModal) {
                            const modal =
                                bootstrap.Modal.getInstance(
                                    editStockOpnameModal
                                );
                            if (modal) modal.hide();
                        }

                        stockOpnamesTable.ajax.reload(() => {
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
                        if (text) text.textContent = "Update Stock Opname";
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
                            <input type="text" class="form-control system-stock-input" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][physical_stock]" class="form-control physical-stock-input" min="0" required>
                        </td>
                        <td>
                            <input type="text" class="form-control difference-input" readonly>
                        </td>
                        <td>
                            <input type="text" name="items[${rowCount}][description_detail]" class="form-control">
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
                            <input type="text" class="form-control system-stock-input" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${rowCount}][physical_stock]" class="form-control physical-stock-input" min="0" required>
                        </td>
                        <td>
                            <input type="text" class="form-control difference-input" readonly>
                        </td>
                        <td>
                            <input type="text" name="items[${rowCount}][description_detail]" class="form-control">
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

    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("physical-stock-input")) {
            const row = e.target.closest("tr");
            calculateRowDifference(row);
        }
    });

    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("product-select")) {
            const row = e.target.closest("tr");
            if (row) {
                const systemStockInput = row.querySelector(
                    ".system-stock-input"
                );
                const physicalStockInput = row.querySelector(
                    ".physical-stock-input"
                );
                const differenceInput = row.querySelector(".difference-input");

                if (
                    systemStockInput &&
                    e.target.options[e.target.selectedIndex]
                ) {
                    const systemStock =
                        e.target.options[e.target.selectedIndex].dataset
                            .systemStock || 0;
                    systemStockInput.value = systemStock;

                    if (physicalStockInput && differenceInput) {
                        const physicalStock =
                            parseFloat(physicalStockInput.value) || 0;
                        const difference =
                            physicalStock - parseFloat(systemStock);
                        differenceInput.value =
                            difference > 0 ? "+" + difference : difference;
                    }
                }
            }
        }
    });

    function calculateRowDifference(row) {
        if (!row) return;

        const systemStockInput = row.querySelector(".system-stock-input");
        const physicalStockInput = row.querySelector(".physical-stock-input");
        const differenceInput = row.querySelector(".difference-input");

        if (!systemStockInput || !physicalStockInput || !differenceInput)
            return;

        const systemStock = parseFloat(systemStockInput.value) || 0;
        const physicalStock = parseFloat(physicalStockInput.value) || 0;
        const difference = physicalStock - systemStock;

        differenceInput.value = difference > 0 ? "+" + difference : difference;
    }

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
                            <input type="text" class="form-control system-stock-input" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[0][physical_stock]" class="form-control physical-stock-input" min="0" required>
                        </td>
                        <td>
                            <input type="text" class="form-control difference-input" readonly>
                        </td>
                        <td>
                            <input type="text" name="items[0][description_detail]" class="form-control">
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

    function previewStockOpnameImage(input, type) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                let container;
                if (type === "new") {
                    container = document.getElementById("image-container-new");
                } else if (type === "edit") {
                    container = document.getElementById("image-container-edit");
                }

                if (container) {
                    container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`;
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    const addImageInput = document.querySelector('input[name="image"]');
    if (addImageInput) {
        addImageInput.addEventListener("change", function () {
            previewStockOpnameImage(this, "new");
        });
    }

    const editImageInput = document.getElementById("image-edit");
    if (editImageInput) {
        editImageInput.addEventListener("change", function () {
            previewStockOpnameImage(this, "edit");
        });
    }
});

window.clearImagePreview = function (type) {
    let container;
    if (type === "new") {
        container = document.getElementById("image-container-new");
        document.querySelector('input[name="image"]').value = "";
    } else if (type === "edit") {
        container = document.getElementById("image-container-edit");
        document.getElementById("image-edit").value = "";
    }

    if (container) {
        container.innerHTML =
            '<i class="fas fa-image fa-2x text-secondary"></i>';
    }
};
