$(document).ready(function() {

    // Initially disable the search button
    $('#searchButton').prop('disabled', true);

    // When the database dropdown changes
    $('#Databasedropdown').on('change', function() {
        var databaseValue = $(this).val();
        if (databaseValue && databaseValue !== 'option') {
            $('#searchButton').prop('disabled', true); // Disable search button until BrchID is selected
            fetchBrchID(databaseValue);
        } else {
            $('#searchButton').prop('disabled', true);
            $('#Brchdropdown').html('<option value="option"> --กรุณาเลือก Branch-- </option>');
        }
    });

    // Fetch BrchID options based on the selected database
    function fetchBrchID(database) {
        $.ajax({
            url: 'fetch-brchid.php',
            type: 'POST',
            data: { dropdown: database }, // Use the correct variable
            success: function(response) {
                var data;
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    console.error('Error parsing JSON:', e, response);  // Added response to see the data received
                    return;
                }
        
                if (data.status === 'success') {
                    // Populate Brchdropdown
                    var options = '<option value="option"> --กรุณาเลือก Branch-- </option>';
                    data.data.forEach(function(item) {
                        options += '<option value="' + item.BrchID + '">' + item.BrchName + '</option>';
                    });
                    $('#Brchdropdown').html(options);
                } else {
                    console.error('Error status received:', data ? data.message : 'No data');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });
    }

    // Enable the search button when a BrchID is selected
    $('#Brchdropdown').on('change', function() {
        var branchValue = $(this).val();
        if (branchValue && branchValue !== 'option') {
            $('#searchButton').prop('disabled', false);
        } else {
            $('#searchButton').prop('disabled', true);
        }
    });

    var selectedIndex = -1;

    // อัพเดตผลลัพธ์ autocomplete ตาม input
    $('#textInput').on('input', function() {
        var query = $(this).val();
        var databaseValue = $('#Databasedropdown').val();
        var branchValue = $('#Brchdropdown').val();
        
        if (query.length >= 2) {
            $.ajax({
                url: 'autocomplete.php',
                type: 'GET',
                data: {
                    query: query,
                    database: databaseValue,
                    branch: branchValue
                },
                success: function(response) {
                    var data;
                    try {
                        data = JSON.parse(response);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        return;
                    }

                    if (data.status === 'success') {
                        var suggestions = data.data.map(function(item, index) {
                            return `<li class="autocomplete-item" data-index="${index}" data-goodcode="${item.Goodcode}">${item.Goodcode}</li>`;
                        }).join('');
                        
                        $('#autocompleteList').html(suggestions).show();
                    } else {
                        $('#autocompleteList').hide();
                        console.error('Error status received:', data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX request failed:', textStatus, errorThrown);
                }
            });
        } else {
            $('#autocompleteList').hide();
        }
    });

    // ใช้ปุ่มลูกศรและ Enter เพื่อเลือก
    $('#textInput').on('keydown', function(e) {
        var items = $('#autocompleteList .autocomplete-item');
        if (e.key === 'ArrowDown') {
            selectedIndex = (selectedIndex + 1) % items.length;
            items.removeClass('active');
            $(items[selectedIndex]).addClass('active');
        } else if (e.key === 'ArrowUp') {
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            items.removeClass('active');
            $(items[selectedIndex]).addClass('active');
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0) {
                var selectedItem = $(items[selectedIndex]).text();
                $('#textInput').val(selectedItem);
                $('#autocompleteList').hide();
            }
        }
    });

    // เลือกจากการคลิก
    $(document).on('click', '.autocomplete-item', function() {
        var selectedValue = $(this).text();
        $('#textInput').val(selectedValue);
        $('#autocompleteList').hide();
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
        var branchValue = $('#Brchdropdown').val();
        var textInputValue = $('#textInput').val().trim();

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
                branch: branchValue,
                textInput: searchPattern
            },
            success: function(response) {
                var data;
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    return;
                }

                if (data.status === 'success') {
                    var tableBody = $('#table-service-type tbody');
                    tableBody.empty();

                    var rows = data.data.map(function(row, index) {
                        var standardCost = parseFloat(row.Standardcost).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        var standardSalePrice = parseFloat(row.StandardSalePrce).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        var standardBuyPrice = parseFloat(row.StandardBuyPrce).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        var remacostValue = parseFloat(row.remacost);
                        var remacostText = isNaN(remacostValue) ? 'ไม่มีรายการเคลื่อนไหว' : remacostValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        var AVGCOSTValue = parseFloat(row.AVGCOST);
                        var AVGCOSTText = isNaN(AVGCOSTValue) ? 'ไม่มีรายการเคลื่อนไหว' : AVGCOSTValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        return [
                            index + 1,
                            row.Goodid,
                            row.Goodcode,
                            row.Goodname1,
                            row.Maingoodunitid,
                            row.GoodUnitCode,
                            standardCost,
                            standardSalePrice,
                            standardBuyPrice,
                            remacostText,
                            AVGCOSTText,
                            databaseValue,
                            branchValue
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
        var branchValue = $('#Brchdropdown').val();
        var textInputValue = $('#textInput').val().trim();
        console.log('Database value:', databaseValue);
        console.log('Branch value:', branchValue);
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
                branch: branchValue,
                textInput: searchPattern,
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

                var fileName = 'Export-Data-Cost-' + dd + '-' + mm + '-' + yyyy + '.xlsx';

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
