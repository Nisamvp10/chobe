$(function () {
    $('#filterDate').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'DD-MM-YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Apply event
    $('#filterDate').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');
        $(this).val(startDate + ' to ' + endDate);
        let search = $('#searchInput').val();
        loadReports(search, startDate, endDate)


    });

    // Cancel event
    $('#filterDate').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#results').empty();
    });
});

$('#downloadReport').on('click', function () {
    let search = $('#searchInput').val();
    let filter = $('#filerStatus').val();
    let projectUnit = $('#projectUnitFilter').val();
    let startDate = ($('#filterDate').val() || '').split('to')[0]?.trim() || '';
    let endDate = ($('#filterDate').val() || '').split('to')[1]?.trim() || '';

    window.location.href = App.getSiteurl() + 'report/generate?search=' + encodeURIComponent(search) + '&filter=' + encodeURIComponent(filter) + '&startDate=' + encodeURIComponent(startDate) + '&endDate=' + encodeURIComponent(endDate) + '&projectUnit=' + encodeURIComponent(projectUnit);
})
// <?=base_url('report/generate');?>