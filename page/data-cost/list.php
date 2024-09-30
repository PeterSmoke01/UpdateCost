<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

date_default_timezone_set("Asia/Bangkok");

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

<!-- เพิ่ม CSS สำหรับ autocomplete -->
<style>
    /* ปรับปรุงสไตล์ของ autocomplete list */
    #autocompleteList {
        margin: 0;
        padding: 0;
        width: 20%;
        background-color: white; /* ตั้งค่าพื้นหลัง */
        max-height: 300px; /* จำกัดความสูงของรายการ */
        overflow-y: auto; /* เพิ่ม scroll ถ้ารายการยาวเกิน */
        z-index: 1000;
        position: absolute;
        border-radius: 0;
    }

    /* ปรับสไตล์ของแต่ละรายการ */
    .autocomplete-item {
        padding: 8px 12px; /* เพิ่มช่องว่างภายใน */
        cursor: pointer;
        background-color: white; /* สีพื้นหลังของแต่ละรายการ */
        border-bottom: 1px solid #eee; /* เส้นคั่นระหว่างรายการ */
    }

    /* สไตล์เมื่อ hover */
    .autocomplete-item:hover {
        background-color: #f0f0f0; /* เปลี่ยนสีพื้นหลังเมื่อ hover */
    }

    /* สไตล์เมื่อเลือก */
    .autocomplete-item.active {
        background-color: #e0e0e0; /* สีพื้นหลังเมื่อรายการถูกเลือก */
    }
</style>

<div class="pc-container">
    <div class="pcoded-content">
        <!-- breadcrumb start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10"><i class="ti-calendar"></i> Data cost</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Data cost</li>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">รายละเอียด Data cost</h5>
                            <div class="col-xl-4 text-right">
                                <button id="exportButton" class="btn btn-primary">Export to Excel</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="card-body">
                            <form id="searchForm" method="post" action="">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex col-sm-6">
                                        <select class="form-control mr-3" name="Databasedropdown" id="Databasedropdown">
                                            <option value="option"> --กรุณาเลือกฐานข้อมูล-- </option>
                                            <?php
                                            // Show databases based on user_id
                                            if ($current_user['user_id'] == 1) {
                                                echo '<option value="SCA">SCA</option>';
                                                echo '<option value="SCA10">SCA10</option>';
                                                echo '<option value="SCO">SCO</option>';
                                                echo '<option value="SCORP">SCORP</option>';
                                                echo '<option value="WECHILL">WECHILL</option>';
                                            } elseif ($current_user['user_id'] == 460) {
                                                echo '<option value="SCA">SCA</option>';
                                            } elseif ($current_user['user_id'] == 461) {
                                                echo '<option value="SCO">SCO</option>';
                                                echo '<option value="SCORP">SCORP</option>';
                                                echo '<option value="WECHILL">WECHILL</option>';
                                            }
                                            ?>
                                        </select>
                                        <select class="form-control" name="Brchdropdown" id="Brchdropdown">
                                            <option value="option"> --กรุณาเลือก Branch-- </option>
                                        </select>
                                    </div>
                                    <div class="mx-3">
                                        <input type="text" class="form-control" name="textInput" id="textInput" placeholder="ค้นหา..." autocomplete="off">
                                        <ul id="autocompleteList" class="list-group"></ul>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary" id="searchButton" disabled>ค้นหา</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-service-type" class="table table-hover dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Goodid</th>
                                        <th>Goodcode</th>
                                        <th>Goodname1</th>
                                        <th>Maingoodunitid</th>
                                        <th>GoodUnitCode</th>
                                        <th>Standardcost</th>
                                        <th>StandardSalePrce</th>
                                        <th>StandardBuyPrce</th>
                                        <th>remacost</th>
                                        <th>AVGCOST</th>
                                        <th>database</th>
                                        <th>branch</th>
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
        <!-- Main Content end -->
    </div>
</div>

<?php require_once(home_path().'config/footer/footer.php'); ?>
<script src="scripts.js"></script>
