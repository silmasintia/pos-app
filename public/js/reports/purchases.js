document.addEventListener("DOMContentLoaded", function () {
    let purchasesTable = $("#purchases-report-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/reports/purchases/data",
            type: "GET",
            data: function (d) {
                d.date_from = $("#date_from").val();
                d.date_to = $("#date_to").val();
                d.supplier_id = $("#supplier_id").val();
                d.status = $("#status").val();
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            { data: "purchase_number", name: "purchases.purchase_number" },
            { data: "purchase_date", name: "purchases.purchase_date" },
            { data: "supplier_name", name: "suppliers.name" },
            { data: "total_items", name: "total_items", searchable: false },
            {
                data: "total_cost",
                name: "purchases.total_cost",
                searchable: false,
            },
            { data: "payment_type", name: "purchases.type_payment" },
            { data: "status", name: "purchases.status" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
        pageLength: 10,
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
    });

    $("#date_from, #date_to, #supplier_id, #status").on("change", function () {
        purchasesTable.ajax.reload();
    });

    $(document).on("click", ".view-purchase-btn", function () {
        const purchaseId = $(this).data("id");

        fetch(`/reports/purchases/show/${purchaseId}`)
            .then((response) => handleApiResponse(response))
            .then((data) => {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Purchase Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Purchase Number:</td>
                                    <td>${data.purchase_number}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>${data.purchase_date}</td>
                                </tr>
                                <tr>
                                    <td>Supplier:</td>
                                    <td>${data.supplier_name}</td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>${data.status_html}</td>
                                </tr>
                                <tr>
                                    <td>Payment Type:</td>
                                    <td>${data.payment_type}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Total:</td>
                                    <td>${data.total_cost}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <h6>Purchase Items</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.items.forEach((item) => {
                    html += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.purchase_price}</td>
                            <td>${item.quantity}</td>
                            <td>${item.total_price}</td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                $("#purchase-details-content").html(html);
                $("#viewPurchaseModal").modal("show");

                $("#print-purchase-btn").data("id", purchaseId);
            })
            .catch((error) => {
                flashMessage("error", error.message);
            });
    });

    $("#print-purchase-btn").on("click", function () {
        const purchaseId = $(this).data("id");
        window.open(`/purchases/print/${purchaseId}`, "_blank");
    });
});