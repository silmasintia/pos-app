document.addEventListener("DOMContentLoaded", function () {
    const customersTable = $("#customers-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: customersDataUrl,
            type: "GET",
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            { data: "name", name: "name" },
            { data: "email", name: "email" },
            { data: "phone", name: "phone" },
            { data: "category_name", name: "category_name" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
        pageLength: 10,
        responsive: true,
    });

    // Add Customer
    const addCustomerForm = document.getElementById("addCustomerForm");
    if (addCustomerForm) {
        addCustomerForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = document.querySelector("#addCustomerModal .btn-submit");
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(customersStoreUrl, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken, "Accept": "application/json"},
            })
                .then(handleApiResponse)
                .then((data) => {
                    flashMessage("success", data.message);
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("addCustomerModal")
                    );
                    modal.hide();
                    this.reset();
                    document.querySelector('#addCustomerForm select[name="customer_category_id"]').value = 1;
                    customersTable.ajax.reload();
                })
                .catch((error) => {
                    flashMessage("error", error.message);
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Add Customer";
                    btn.disabled = false;
                });
        });
    }

    // Edit Customer Button Click
    document.addEventListener("click", function (e) {
        const editBtn = e.target.closest(".edit-btn");
        if (!editBtn) return;

        const customerId = editBtn.getAttribute("data-id");

        fetch(customersUpdateUrl.replace(":id", customerId) + "/edit", {
            method: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
        })
            .then(handleApiResponse)
            .then((data) => {
                document.getElementById("edit_customer_id").value = data.id;
                document.getElementById("edit_name").value = data.name;
                document.getElementById("edit_email").value = data.email || "";
                document.getElementById("edit_phone").value = data.phone || "";
                
                const categorySelect = document.getElementById("edit_customer_category_id");
                categorySelect.value = data.customer_category_id || 1;

                const modal = new bootstrap.Modal(
                    document.getElementById("editCustomerModal")
                );
                modal.show();
            })
            .catch((error) => {
                flashMessage("error", error.message);
            });
    });

    // Update Customer
    const editCustomerForm = document.getElementById("editCustomerForm");
    if (editCustomerForm) {
        editCustomerForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const customerId =
                document.getElementById("edit_customer_id").value;
            const formData = new FormData(this);
            const btn = document.querySelector(
                "#editCustomerModal .btn-submit"
            );
            const spinner = btn.querySelector(".spinner-border");
            const text = btn.querySelector(".btn-text");
            const loadingText = btn.getAttribute("data-loading-text");

            spinner.classList.remove("d-none");
            text.textContent = loadingText;
            btn.disabled = true;

            fetch(customersUpdateUrl.replace(":id", customerId), {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then(handleApiResponse)
                .then((data) => {
                    flashMessage("success", data.message);
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("editCustomerModal")
                    );
                    modal.hide();
                    customersTable.ajax.reload();
                })
                .catch((error) => {
                    flashMessage("error", error.message);
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    text.textContent = "Update Customer";
                    btn.disabled = false;
                });
        });
    }

    // Delete Customer
    document.addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".delete-btn");
        if (!deleteBtn) return;

        const customerId = deleteBtn.getAttribute("data-id");

        confirmDialog(
            "Delete Customer",
            "Are you sure you want to delete this customer?"
        ).then((result) => {
            if (!result.isConfirmed) return;

            fetch(customersDeleteUrl.replace(":id", customerId), {
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .then(handleApiResponse)
                .then((data) => {
                    flashMessage("success", data.message);
                    customersTable.ajax.reload();
                })
                .catch((error) => {
                    flashMessage("error", error.message);
                });
        });
    });
});