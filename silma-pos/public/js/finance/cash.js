document.addEventListener("DOMContentLoaded", function () {
    const cashTable = $("#cash-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: cashDataUrl,
            type: "GET",
        },
        columns: [
            { data: "no", orderable: false, searchable: false },
            { data: "name" },
            { data: "amount_formatted", orderable: false, searchable: false },
            { data: "action", orderable: false, searchable: false },
        ],
        pageLength: 10,
        responsive: true,
    });

    // Add Cash 
    const addCashForm = document.getElementById("addCashForm");
    if (addCashForm) {
        addCashForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector("#addCashModal .btn-submit");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(cashStoreUrl, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            })
                .then(handleApiResponse)
                .then((data) => {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("addCashModal")
                    );
                    modal.hide();
                    this.reset();
                    cashTable.ajax.reload();
                    flashMessage("success", data.message);
                })
                .catch((error) => {
                    console.error("Add Cash Error:", error);
                    flashMessage("error", error.message || "An error occurred");
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Add Cash Account";
                    btn.disabled = false;
                });
        });
    }

    // Edit Cash Button Click
    document.addEventListener("click", function (e) {
        if (e.target.closest(".edit-btn")) {
            const btn = e.target.closest(".edit-btn");
            const cashId = btn.getAttribute("data-id");

            fetch(cashUpdateUrl.replace(":id", cashId) + "/edit", {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            })
                .then(handleApiResponse)
                .then((data) => {
                    document.getElementById("edit_cash_id").value = data.id;
                    document.getElementById("edit_name").value = data.name;
                    document.getElementById("edit_amount").value = data.amount;

                    const modal = new bootstrap.Modal(
                        document.getElementById("editCashModal")
                    );
                    modal.show();
                })
                .catch((error) => {
                    console.error("Load Edit Data Error:", error);
                    flashMessage(
                        "error",
                        error.message || "Failed to load cash data"
                    );
                });
        }
    });

    // Update Cash 
    const editCashForm = document.getElementById("editCashForm");
    if (editCashForm) {
        editCashForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const cashId = document.getElementById("edit_cash_id").value;
            const formData = new FormData(this);
            const btn = document.querySelector("#editCashModal .btn-submit");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(cashUpdateUrl.replace(":id", cashId), {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            })
                .then(handleApiResponse)
                .then((data) => {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("editCashModal")
                    );
                    modal.hide();
                    cashTable.ajax.reload();
                    flashMessage("success", data.message);
                })
                .catch((error) => {
                    console.error("Update Cash Error:", error);
                    flashMessage("error", error.message || "An error occurred");
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Update Cash Account";
                    btn.disabled = false;
                });
        });
    }

    // Delete Cash 
    document.addEventListener("click", function (e) {
        if (e.target.closest(".delete-btn")) {
            const btn = e.target.closest(".delete-btn");
            const cashId = btn.getAttribute("data-id");
            const cashName = btn.getAttribute("data-name");

            confirmDialog(
                "Delete Cash Account",
                `Are you sure you want to delete "${cashName}"?`
            ).then((result) => {
                if (result.isConfirmed) {
                    fetch(cashDeleteUrl.replace(":id", cashId), {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                    })
                        .then(handleApiResponse)
                        .then((data) => {
                            cashTable.ajax.reload();
                            flashMessage("success", data.message);
                        })
                        .catch((error) => {
                            console.error("Delete Cash Error:", error);
                            flashMessage(
                                "error",
                                error.message || "An error occurred"
                            );
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