document.addEventListener("DOMContentLoaded", function () {
    let salesTable = $("#sales-report-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/reports/sales/data",
            type: "GET",
            data: function (d) {
                d.date_from = $("#date_from").val();
                d.date_to = $("#date_to").val();
                d.customer_id = $("#customer_id").val();
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
            { data: "order_number", name: "orders.order_number" },
            { data: "order_date", name: "orders.order_date" },
            { data: "customer_name", name: "customers.name" },
            { data: "total_items", name: "total_items", searchable: false },
            {
                data: "subtotal",
                name: "orders.total_cost_before",
                searchable: false,
            },
            {
                data: "discount",
                name: "orders.amount_discount",
                searchable: false,
            },
            {
                data: "total_cost",
                name: "orders.total_cost",
                searchable: false,
            },
            { data: "payment_type", name: "orders.type_payment" },
            { data: "status", name: "orders.status" },
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

    $("#date_from, #date_to, #customer_id, #status").on("change", function () {
        salesTable.ajax.reload();
    });

    $(document).on("click", ".view-order-btn", function () {
        const orderId = $(this).data("id");

        fetch(`/reports/sales/show/${orderId}`)
            .then((response) => handleApiResponse(response))
            .then((data) => {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Order Number:</td>
                                    <td>${data.order_number}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>${data.order_date}</td>
                                </tr>
                                <tr>
                                    <td>Customer:</td>
                                    <td>${data.customer_name}</td>
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
                                    <td>Subtotal:</td>
                                    <td>${data.subtotal}</td>
                                </tr>
                                <tr>
                                    <td>Discount:</td>
                                    <td>${data.discount}</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td>${data.total_cost}</td>
                                </tr>
                                <tr>
                                    <td>Payment:</td>
                                    <td>${data.input_payment}</td>
                                </tr>
                                <tr>
                                    <td>Return:</td>
                                    <td>${data.return_payment}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <h6>Order Items</h6>
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
                            <td>${item.order_price}</td>
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

                $("#order-details-content").html(html);
                $("#viewOrderModal").modal("show");

                $("#print-order-btn").data("id", orderId);
            })
            .catch((error) => {
                flashMessage("error", error.message);
            });
    });

    $("#print-order-btn").on("click", function () {
        const orderId = $(this).data("id");
        window.open(`/sales/print/${orderId}`, "_blank");
    });
});
