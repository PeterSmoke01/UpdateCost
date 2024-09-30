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

<div class="pc-container">
    <div class="pcoded-content">
        <!-- breadcrumb start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10"><i class="ti-calendar"></i> Price list</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Price list</li>
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
                            <h5 class="m-0">รายละเอียด Price list</h5>
                            <div class="col-xl-4 text-right">
                                <button id="exportButton" class="btn btn-primary">Export to Excel</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="card-body">
                            <form id="searchForm" method="post" action="">
                                <div class="d-flex align-items-center mb-3">
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
                                        <select class="form-control" name="searchType" id="searchType">
                                            <option value="both">ค้นหาด้วย docuno/goodcode</option>
                                            <option value="docuno">ค้นหาด้วย docuno</option>
                                            <option value="goodcode">ค้นหาด้วย goodcode</option>
                                        </select>
                                    </div>
                                    <div class="mx-3">
                                        <input type="text" class="form-control" name="textInput" id="textInput" placeholder="ค้นหา..." style="overflow-y: auto;">
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
                                        <th>docuno</th>
                                        <th>docutype</th>
                                        <th>docudate</th>
                                        <th>BeginDate</th>
                                        <th>enddate</th>
                                        <!-- <th>custflag</th> -->
                                        <th>Custflagname</th>
                                        <!-- <th>Goodflag</th> -->
                                        <th>Goodflagname</th>
                                        <!-- <th>begintime</th>
                                        <th>endtime</th>
                                        <th>docuflag</th>
                                        <th>PromotionFlag</th> -->
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
