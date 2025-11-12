document.addEventListener('DOMContentLoaded', function() {
    let profitLossTable = $('#profit-loss-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '/reports/profit-loss/data',
            type: 'GET',
            data: function(d) {
                d.start_date = $('#start-date').val();
                d.end_date = $('#end-date').val();
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'category', name: 'category' },
            { data: 'source', name: 'source' },
            { data: 'reference', name: 'reference' },
            { data: 'cash', name: 'cash.name' },
            { data: 'amount', name: 'amount', className: 'text-end' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip'
    });

    function loadSummary() {
        const startDate = $('#start-date').val();
        const endDate = $('#end-date').val();
        
        $.ajax({
            url: '/reports/profit-loss/summary',
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#total-sales').text('Rp ' + numberFormat(response.totalSales));
                $('#total-purchases').text('Rp ' + numberFormat(response.totalPurchases));
                $('#gross-profit').text('Rp ' + numberFormat(response.grossProfit));
                $('#net-profit').text('Rp ' + numberFormat(response.netProfit));
                
                $('#gross-profit').removeClass('text-success text-danger');
                $('#net-profit').removeClass('text-success text-danger');
                
                if (response.grossProfit >= 0) {
                    $('#gross-profit').addClass('text-success');
                } else {
                    $('#gross-profit').addClass('text-danger');
                }
                
                if (response.netProfit >= 0) {
                    $('#net-profit').addClass('text-success');
                } else {
                    $('#net-profit').addClass('text-danger');
                }
            },
            error: function(xhr) {
                flashMessage('error', 'Failed to load summary data');
            }
        });
    }

    $('#apply-filter').on('click', function() {
        profitLossTable.ajax.reload();
        loadSummary();
    });

    function numberFormat(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    loadSummary();
});