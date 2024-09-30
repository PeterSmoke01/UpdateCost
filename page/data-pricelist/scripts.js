$(document).ready(function() {

    // Enable the search button when a Database is selected
    $('#Databasedropdown').on('change', function() {
        var databaseValue = $(this).val();
        if (databaseValue && databaseValue !== 'option') {
            $('#searchButton').prop('disabled', false);
        } else {
            $('#searchButton').prop('disabled', true);
        }
    });

    // Autocomplete for textInput
    $('#textInput').autocomplete({
        source: function(request, response) {
            var databaseValue = $('#Databasedropdown').val(); // ค่าจาก dropdown ฐานข้อมูล
            var searchType = $('#searchType').val(); // ค่าจาก search type

            $.ajax({
                url: 'autocomplete.php',
                type: 'POST',
                data: {
                    term: request.term,
                    database: databaseValue, // ส่งค่า database
                    searchType: searchType // ส่งค่า search type
                },
                success: function(data) {
                    response(JSON.parse(data).data); // แสดงผลลัพธ์
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching autocomplete data:', textStatus, errorThrown);
                }
            });
        },
        minLength: 2, // Minimum characters before triggering autocomplete
        select: function(event, ui) {
            $('#textInput').val(ui.item.value); // อัพเดต input value
            return false; // ป้องกันการเติมค่าภายในฟิลด์
        }
    });

    var table = $('#table-service-type').DataTable({
        "drawCallback": function(settings) {
            feather.replace();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        var databaseValue = $('#Databasedropdown').val();
        var textInputValue = $('#textInput').val().trim();
        var searchType = $('#searchType').val(); // ดึงค่าจาก dropdown ใหม่
    
        var searchPattern;
        if (textInputValue.startsWith('*') && textInputValue.endsWith('*')) {
            searchPattern = '%' + textInputValue.slice(1, -1) + '%';
        } else if (textInputValue.endsWith('*')) {
            searchPattern = textInputValue.slice(0, -1) + '%';
        } else if (textInputValue.startsWith('*')) {
            searchPattern = '%' + textInputValue.slice(1);
        } else if (textInputValue) {
            searchPattern = textInputValue;
        } else {
            searchPattern = '%' + '%';
        }
    
        $.ajax({
            url: 'list-json.php',
            type: 'POST',
            data: {
                ajax: true,
                database: databaseValue,
                // branch: branchValue,
                textInput: searchPattern,
                searchType: searchType // ส่งค่าประเภทการค้นหา
            },
            success: function(response) {
                var data;
                try {
                    data = JSON.parse(response);
                    console.log(data);  
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    return;
                }
    
                if (data.status === 'success') {
                    var tableBody = $('#table-service-type tbody');
                    tableBody.empty();
    
                    var rows = data.data.map(function(row, index) {
                        return [
                            index + 1,
                            row.docuno,
                            row.docutype,
                            row.docudate.date ? row.docudate.date.split(' ')[0] : 'N/A',
                            row.BeginDate.date ? row.BeginDate.date.split(' ')[0] : 'N/A',
                            row.enddate.date ? row.enddate.date.split(' ')[0] : 'N/A',
                            // row.custflag,
                            row.Custflagname,
                            // row.goodflag,
                            row.Goodflagname,
                            // row.begintime,
                            // row.endtime,
                            // row.docuflag,
                            // row.PromotionFlag,
                            row.Datestatus,
                            row.docstatus,
                            row.doctype,
                            row.Custcode,
                            row.CustName,
                            // row.CustGroupID,
                            row.CustGroupCode,
                            row.CustGroupName,
                            // row.GoodGroupID,
                            row.GoodGroupCode,
                            row.GoodGroupName,
                            row.Headremark,
                            row.listno,
                            row.ListID,
                            row.goodcode,
                            row.GoodName1,
                            row.GoodUnitCode,
                            parseFloat(row.GoodPrice).toFixed(2),         // ปรับให้มี 2 ตำแหน่ง
                            row.GoodDiscFormula,
                            parseFloat(row.GoodDiscAmnt).toFixed(2),     // ปรับให้มี 2 ตำแหน่ง
                            parseFloat(row.GoodPriceNet).toFixed(2),      // ปรับให้มี 2 ตำแหน่ง
                            row.ItemRemark,
                            parseFloat(row.PriceBaseAmnt).toFixed(2),     // ปรับให้มี 2 ตำแหน่ง
                            parseFloat(row.startgoodqty).toFixed(2),      // ปรับให้มี 2 ตำแหน่ง
                            parseFloat(row.endgoodqty).toFixed(2)         // ปรับให้มี 2 ตำแหน่ง
                        ];
                    });
    
                    table.clear();
                    table.rows.add(rows);
                    table.draw();
                } else {
                    console.error('Error status received:', data ? data.message : 'No data');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });
    });
    

    $('#exportButton').on('click', function() {
        console.log('Export button clicked');
        var databaseValue = $('#Databasedropdown').val();
        var searchType = $('#searchType').val();
        var textInputValue = $('#textInput').val().trim();
        console.log('Database value:', databaseValue);
        console.log('Text input value:', textInputValue);
        
        var searchPattern;
        if (textInputValue.startsWith('*') && textInputValue.endsWith('*')) {
            searchPattern = '%' + textInputValue.slice(1, -1) + '%';
        } else if (textInputValue.endsWith('*')) {
            searchPattern = textInputValue.slice(0, -1) + '%';
        } else if (textInputValue.startsWith('*')) {
            searchPattern = '%' + textInputValue.slice(1);
        } else if (textInputValue){
            searchPattern = textInputValue
        }
        else {
            searchPattern = '%' + '%';
        }
        
        $.ajax({
            url: 'export.php',
            type: 'POST',
            data: {
                dropdown: databaseValue,
                textInput: searchPattern,
                searchType: searchType,
                export: 'excel'
            },
            xhrFields: {
                responseType: 'blob' // Set response type to Blob for file download
            },
            success: function(response) {
                var link = document.createElement('a');
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); // Months start from 0
                var yyyy = today.getFullYear();

                var fileName = 'Export-Price-List-' + dd + '-' + mm + '-' + yyyy + '.xlsx';

                link.href = window.URL.createObjectURL(response);
                link.download = fileName; // Set file name for download
                link.click();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error exporting file:', textStatus, errorThrown);
            }
        });
    });
});
