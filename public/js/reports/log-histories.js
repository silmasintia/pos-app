document.addEventListener("DOMContentLoaded", function () {
    let logHistoriesTable = $("#log-histories-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/reports/log-histories/data",
            type: "GET",
            data: function (d) {
                d.date_from = $("#date_from").val();
                d.date_to = $("#date_to").val();
                d.user_id = $("#user_id").val();
                d.table_name = $("#table_name").val();
                d.action = $("#action").val();
            },
            error: function(xhr, error, thrown) {
                console.log(xhr.responseText);
                alert("Error loading data. Please check console for details.");
            }
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            { data: "timestamp", name: "log_histories.timestamp" },
            { data: "user_name", name: "users.name" },
            { data: "table_name", name: "log_histories.table_name" },
            { data: "action", name: "log_histories.action" },
            { data: "entity_id", name: "log_histories.entity_id" },
            { data: "details", name: "details", searchable: false },
            {
                data: "action_btn",
                name: "action_btn",
                orderable: false,
                searchable: false,
            },
        ],
        pageLength: 10,
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
    });

    $("#date_from, #date_to, #user_id, #table_name, #action").on("change", function () {
        logHistoriesTable.ajax.reload();
    });

    $("#reset-filter").on("click", function () {
        $("#date_from").val("");
        $("#date_to").val("");
        $("#user_id").val("");
        $("#table_name").val("");
        $("#action").val("");
        logHistoriesTable.ajax.reload();
    });

    $(document).on("click", ".view-log-btn", function () {
        const logId = $(this).data("id");

        fetch(`/reports/log-histories/show/${logId}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then((data) => {
                $("#log-table-name").text(data.table_name);
                $("#log-entity-id").text(data.entity_id);
                $("#log-action").html(data.action_html);
                $("#log-timestamp").text(data.timestamp);
                $("#log-user-name").text(data.user_name);
                
                let summary = "";
                if (data.action === "create") {
                    summary = `New record created in ${data.table_name} table`;
                } else if (data.action === "update") {
                    summary = `Record updated in ${data.table_name} table`;
                } else if (data.action === "delete") {
                    summary = `Record deleted from ${data.table_name} table`;
                } else {
                    summary = `${data.action} action performed on ${data.table_name} table`;
                }
                $("#log-summary").text(summary);
                
                let html = "";
                if (data.changes.length === 0) {
                    html = `
                        <tr>
                            <td colspan="3" class="text-center">No changes recorded</td>
                        </tr>
                    `;
                } else {
                    data.changes.forEach((change) => {
                        html += `
                            <tr>
                                <td><strong>${change.field}</strong></td>
                                <td>${change.old_value}</td>
                                <td>${change.new_value}</td>
                            </tr>
                        `;
                    });
                }
                $("#log-changes").html(html);
                
                $("#viewLogModal").modal("show");
            })
            .catch((error) => {
                console.error('Error:', error);
                alert("Error loading log details. Please check console for details.");
            });
    });
});