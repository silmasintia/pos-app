document.addEventListener("DOMContentLoaded", function () {
    const transactionsTable = $("#transactions-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: transactionsDataUrl,
            type: "GET",
        },
        columns: [
            { data: "no", orderable: false, searchable: false },
            { data: "date_formatted" },
            { data: "name" },
            { data: "category_name" },
            { data: "cash_name" },
            { data: "amount_formatted", orderable: false, searchable: false },
            { data: "image_preview", orderable: false, searchable: false },
            { data: "action", orderable: false, searchable: false },
        ],
        pageLength: 10,
        responsive: true,
    });

    document
        .getElementById("transaction_type")
        .addEventListener("change", function () {
            const amountInput = document.getElementById("amount");
            if (this.value === "expense") {
                amountInput.setAttribute("min", "0.01");
                amountInput.setAttribute("placeholder", "0.00");
            } else if (this.value === "income") {
                amountInput.setAttribute("min", "0.01");
                amountInput.setAttribute("placeholder", "0.00");
            }
        });

    // Add Transaction
    const addTransactionForm = document.getElementById("addTransactionForm");
    if (addTransactionForm) {
        addTransactionForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const transactionType =
                document.getElementById("transaction_type").value;
            const amountInput = document.getElementById("amount");
            let amount = parseFloat(amountInput.value);

            if (transactionType === "expense") {
                amount = -Math.abs(amount);
            } else {
                amount = Math.abs(amount);
            }

            amountInput.value = amount;

            const formData = new FormData(this);
            const btn = document.querySelector(
                "#addTransactionModal .btn-submit"
            );
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(transactionsStoreUrl, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then(handleApiResponse)
                .then((data) => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("addTransactionModal")
                        );
                        modal.hide();
                        this.reset();
                        document.getElementById(
                            "image-container-new"
                        ).innerHTML =
                            '<i class="fas fa-image fa-2x text-secondary"></i>';
                        transactionsTable.ajax.reload();
                        flashMessage("success", data.message);
                    } else {
                        flashMessage("error", data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    flashMessage("error", error.message);
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Add Transaction";
                    btn.disabled = false;
                });
        });
    }

    // Edit Transaction Button Click
    document.addEventListener("click", function (e) {
        if (e.target.closest(".edit-btn")) {
            const btn = e.target.closest(".edit-btn");
            const transactionId = btn.getAttribute("data-id");

            fetch(
                transactionsUpdateUrl.replace(":id", transactionId) + "/edit",
                {
                    method: "GET",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                }
            )
                .then(handleApiResponse)
                .then((data) => {
                    console.log("Transaction data:", data);

                    document.getElementById("edit_transaction_id").value =
                        data.id;

                    if (data.date) {
                        document.getElementById("edit_date").value = data.date;
                    }

                    document.getElementById("edit_name").value = data.name;
                    document.getElementById("edit_category_id").value =
                        data.transaction_category_id;
                    document.getElementById("edit_cash_id").value =
                        data.cash_id;
                    document.getElementById("edit_description").value =
                        data.description || "";

                    const amount = parseFloat(data.amount);
                    const transactionType = amount >= 0 ? "income" : "expense";
                    document.getElementById("edit_transaction_type").value =
                        transactionType;

                    document.getElementById("edit_amount").value =
                        Math.abs(amount);

                    const imageContainer = document.getElementById(
                        "image-container-edit"
                    );
                    const removeImageCheckbox = document.getElementById(
                        "remove_image_checkbox"
                    );

                    if (data.image) {
                        let imagePath = data.image.startsWith("storage/")
                            ? data.image
                            : "storage/" + data.image;
                        const imageUrl = `${window.location.origin}/${imagePath}`;

                        imageContainer.innerHTML = `
                    <img src="${imageUrl}" class="w-100 h-100" style="object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="clear_edit_image_btn" title="Hapus Gambar">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                        if (removeImageCheckbox) {
                            removeImageCheckbox.checked = false;
                        }

                        document
                            .getElementById("clear_edit_image_btn")
                            .addEventListener("click", function () {
                                imageContainer.innerHTML =
                                    '<i class="fas fa-image fa-2x text-secondary"></i>';
                                if (removeImageCheckbox) {
                                    removeImageCheckbox.checked = true;
                                }
                                document.getElementById("edit_image").value =
                                    null;
                            });
                    } else {
                        imageContainer.innerHTML =
                            '<i class="fas fa-image fa-2x text-secondary"></i>';
                        if (removeImageCheckbox) {
                            removeImageCheckbox.checked = false;
                        }
                    }

                    const modal = new bootstrap.Modal(
                        document.getElementById("editTransactionModal")
                    );
                    modal.show();
                })
                .catch((error) => {
                    console.error("Error:", error);
                    flashMessage("error", "Failed to load transaction data");
                });
        }
    });

    document
        .getElementById("edit_transaction_type")
        .addEventListener("change", function () {
            const amountInput = document.getElementById("edit_amount");
            if (this.value === "expense") {
                amountInput.setAttribute("min", "0.01");
                amountInput.setAttribute("placeholder", "0.00");
            } else if (this.value === "income") {
                amountInput.setAttribute("min", "0.01");
                amountInput.setAttribute("placeholder", "0.00");
            }
        });

    // Update Transaction
    const editTransactionForm = document.getElementById("editTransactionForm");
    if (editTransactionForm) {
        editTransactionForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const transactionType = document.getElementById(
                "edit_transaction_type"
            ).value;
            const amountInput = document.getElementById("edit_amount");
            let amount = parseFloat(amountInput.value);

            if (transactionType === "expense") {
                amount = -Math.abs(amount);
            } else {
                amount = Math.abs(amount);
            }

            amountInput.value = amount;

            const transactionId = document.getElementById(
                "edit_transaction_id"
            ).value;
            const formData = new FormData(this);
            const btn = document.querySelector(
                "#editTransactionModal .btn-submit"
            );
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(transactionsUpdateUrl.replace(":id", transactionId), {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then(handleApiResponse)
                .then((data) => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("editTransactionModal")
                        );
                        modal.hide();
                        transactionsTable.ajax.reload();
                        flashMessage("success", data.message);
                    } else {
                        flashMessage("error", data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    flashMessage("error", error.message);
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Update Transaction";
                    btn.disabled = false;
                });
        });
    }

    // Delete Transaction
    document.addEventListener("click", function (e) {
        if (e.target.closest(".delete-btn")) {
            const btn = e.target.closest(".delete-btn");
            const transactionId = btn.getAttribute("data-id");
            const transactionName = btn.getAttribute("data-name");

            confirmDialog(
                "Delete Transaction",
                `Are you sure you want to delete "${transactionName}"?`
            ).then((result) => {
                if (result.isConfirmed) {
                    fetch(transactionsDeleteUrl.replace(":id", transactionId), {
                        method: "DELETE",
                        headers: { "X-CSRF-TOKEN": csrfToken },
                    })
                        .then(handleApiResponse)
                        .then((data) => {
                            if (data.success) {
                                transactionsTable.ajax.reload();
                                flashMessage("success", data.message);
                            } else {
                                flashMessage("error", data.message);
                            }
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                            flashMessage("error", error.message);
                        });
                }
            });
        }
    });
});

function previewTransactionImage(input, type) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            const container = document.getElementById(
                `image-container-${type}`
            );
            container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`;
        };

        reader.readAsDataURL(input.files[0]);
    }
}