<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();
$arr_user = array(1, 460, 461);
if (in_array($current_user['user_id'], $arr_user, true)) {

} else {
    unset($_SESSION['auth_sc']);
    if(empty($_SESSION['auth_sc'])) {
        header("Location:".HOME_URI."?error=err03");
        exit;
    }
}
?>

<!-- include header start -->
<?php require_once(home_path().'config/header/header-page.php'); ?>
<!-- include header End -->

<div class="pc-container">
    <div class="pcoded-content">
        <!-- breadcrumb start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10"><i class="ti-upload"></i> Update PriceList </h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"> PriceList </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb end -->

        <!-- Main Content start -->
        <div class="row">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Update PriceList</h5>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" action="upload-pricelist.php" method="post" enctype="multipart/form-data">
                        <div class="d-flex align-items-center mb-4">
                                <div class="col-sm-3 mr-4">
                                    <select name="database" id="database" class="form-control">
                                        <option value="option1"> --กรุณาเลือก-- </option>
                                        <?php
                                            // Show databases based on user_id
                                            if ($current_user['user_id'] == 1) {
                                                echo '<option value="SCA">SCA</option>';
                                                echo '<option value="SCA10">SCA10</option>';
                                                echo '<option value="SCO">SCO</option>';
                                                echo '<option value="SCORP">SCORP</option>';
                                                echo '<option value="WECHILL">WECHILL</option>';
                                                echo '<option value="Test">Test</option>';
                                            } elseif ($current_user['user_id'] == 460) {
                                                echo '<option value="SCA">SCA</option>';
                                            } elseif ($current_user['user_id'] == 461) {
                                                echo '<option value="SCO">SCO</option>';
                                                echo '<option value="SCORP">SCORP</option>';
                                                echo '<option value="WECHILL">WECHILL</option>';
                                            }
                                            ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="col-sm-5 mr-4">
                                    <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls" required>
                                </div>
                                <div>
                                    <button type="submit" id="submitButton" name="submit" value="Upload" class="btn btn-primary" disabled>Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content end -->

        <!-- Table to display Excel content -->
        <div class="row">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Preview Excel File</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-service-type" class="table table-hover dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>docuno</th>
                                        <th>docutype</th>
                                        <th>docudate</th>
                                        <th>BeginDate</th>
                                        <th>enddate</th>
                                        <th>ListID</th>
                                        <!-- <th>custflag</th> -->
                                        <th>Custflagname</th>
                                        <!-- <th>Goodflag</th> -->
                                        <th>Goodflagname</th>
                                        <!-- <th>begintime</th>
                                        <th>endtime</th> -->
                                        <!-- <th>docuflag</th> -->
                                        <!-- <th>PromotionFlag</th> -->
                                        <th>Datestatus</th>
                                        <th>docstatus</th>
                                        <th>doctype</th>
                                        <th>Custcode</th>
                                        <th>CustName</th>
                                        <!-- <th>CustGroupID</th> -->
                                        <th>CustGroupCode</th>
                                        <th>CustGroupName</th>
                                        <!-- <th>GoodGroupID</th> -->
                                        <th>GoodGroupCode</th>
                                        <th>GoodGroupName</th>
                                        <th>Headremark</th>
                                        <th>listno</th>
                                        <th>ListID</th>
                                        <th>goodcode</th>
                                        <th>GoodName1</th>
                                        <th>GoodUnitCode</th>
                                        <th>GoodPrice</th>
                                        <th>GoodDiscFormula</th>
                                        <th>GoodDiscAmnt</th>
                                        <th>GoodPriceNet</th>
                                        <th>ItemRemark</th>
                                        <th>PriceBaseAmnt</th>
                                        <th>startgoodqty</th>
                                        <th>endgoodqty</th>
                                        <th>Database</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- ข้อมูลจะถูกเติมโดย JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once(home_path().'config/footer/footer.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>
// ฟังก์ชันที่ใช้ตรวจสอบไฟล์ Excel และอัพเดตข้อมูลในตาราง
function checkExcelAndUpdateTable(file) {
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, { type: 'array' });
            var sheetName = workbook.SheetNames[0];
            var worksheet = workbook.Sheets[sheetName];
            var json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            var tableBody = document.querySelector('#table-service-type tbody');
            tableBody.innerHTML = '';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check-status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var requestData = 'data=' + encodeURIComponent(JSON.stringify(json)) + '&database=' + encodeURIComponent(document.getElementById('database').value);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var statusData = JSON.parse(xhr.responseText);
                    for (var i = 1; i < json.length; i++) {  // Start from 1 to skip header
                        var row = json[i];
                        var tr = document.createElement('tr');

                        var tdStatus = document.createElement('td');
                        tdStatus.innerHTML = statusData[i - 1].status;

                        // ตรวจสอบสถานะและเปลี่ยนสี
                        if (statusData[i - 1].status.includes('สามารถอัพเดทได้')) {
                            tdStatus.innerHTML = '<span style="color:green;">' + statusData[i - 1].status + '</span>';
                        } else {
                            tdStatus.innerHTML = '<span style="color:red;">' + statusData[i - 1].status + '</span>';
                        }

                        tr.appendChild(tdStatus);

                        for (var j = 0; j < row.length; j++) {
                            var td = document.createElement('td');
                            td.textContent = row[j];
                            tr.appendChild(td);
                        }

                        tableBody.appendChild(tr);
                    }

                    // Enable the submit button
                    document.getElementById('submitButton').disabled = false;
                }
            };

            xhr.send(requestData);
        };
        reader.readAsArrayBuffer(file);
    }
}

// เมื่อมีการเลือกไฟล์ใหม่
document.getElementById('file').addEventListener('change', function(event) {
    var file = event.target.files[0];
    checkExcelAndUpdateTable(file);
});

// เมื่อมีการเปลี่ยน dropdown
document.getElementById('database').addEventListener('change', function() {
    var fileInput = document.getElementById('file');
    if (fileInput.files.length > 0) {
        var file = fileInput.files[0];
        checkExcelAndUpdateTable(file);
    }
});
</script>
