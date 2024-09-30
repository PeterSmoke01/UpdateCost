<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once(home_path().'controller/fn-users.php');

global $conn;
date_default_timezone_set("Asia/Bangkok");

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();
$dateNow = date("Y-m-d");

// ตรวจสอบสิทธิ์ผู้ใช้งาน
if ($current_user['user_role'] == 'admin' || $current_user['user_role'] == 'purchase') {
    // Code สำหรับ admin หรือ purchase
} else {
    unset($_SESSION['auth_sc']);
    if (empty($_SESSION['auth_sc'])) {
        header("Location:" . HOME_URI . "?error=err03");
        exit;
    }
}

// ฟังก์ชันสำหรับการดึงข้อมูลจำนวนการอัพเดทในแต่ละเดือน
function getMonthlyUpdates($dir) {
    $files = scandir($dir);
    $monthlyCounts = [];

    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filePath = $dir . $file;
            $fileModifiedTime = filemtime($filePath);
            $month = date('Y-m', $fileModifiedTime); // รูปแบบ Y-m เช่น 2023-09
            
            if (!isset($monthlyCounts[$month])) {
                $monthlyCounts[$month] = 0;
            }
            $monthlyCounts[$month]++;
        }
    }
    return $monthlyCounts;
}

// ดึงข้อมูลจาก path ที่เกี่ยวข้อง update-cost\file-upload
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/update-cost/file-upload/';
$standardCostDir = $_SERVER['DOCUMENT_ROOT'] . '/update-cost/file-upload/standardcost/';
$salePriceDir = $_SERVER['DOCUMENT_ROOT'] . '/update-cost/file-upload/saleprice/';
$buyPriceDir = $_SERVER['DOCUMENT_ROOT'] . '/update-cost/file-upload/buyprice/';
$priceListDir = $_SERVER['DOCUMENT_ROOT'] . '/update-cost/file-upload/pricelist/';

// เก็บข้อมูลการอัพเดทในแต่ละเดือน
$uploadData = getMonthlyUpdates($uploadDir);
$standardCostData = getMonthlyUpdates($standardCostDir);
$salePriceData = getMonthlyUpdates($salePriceDir);
$buyPriceData = getMonthlyUpdates($buyPriceDir);
$priceListData = getMonthlyUpdates($priceListDir);

// ส่งข้อมูลไปยัง JavaScript
echo "<script>
        var uploadData = " . json_encode($uploadData) . ";
        var standardCostData = " . json_encode($standardCostData) . ";
        var salePriceData = " . json_encode($salePriceData) . ";
        var buyPriceData = " . json_encode($buyPriceData) . ";
        var priceListData = " . json_encode($priceListData) . ";
      </script>";
?>

<?php require_once(home_path().'config/header/header-page.php'); ?>

<div class="pc-container">
    <div class="pcoded-content">
        <!-- breadcrumb start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Dashboard</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Dashboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb end -->
        <!-- Main Content start -->
        <div class="row">
            <!-- กราฟการอัพเดทไฟล์รวม -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card prod-p-card background-pattern-white">
                    <div class="card-body">
                        <h6 class="m-b-5">จำนวนการอัพโหลดไฟล์ในแต่ละเดือน</h6>
                        <canvas id="uploadChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <!-- กราฟการอัพเดท Standardcost -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card prod-p-card background-pattern-white">
                    <div class="card-body">
                        <h6 class="m-b-5">จำนวนการอัพเดท Standardcost ในแต่ละเดือน</h6>
                        <canvas id="standardCostChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <!-- กราฟการอัพเดท SalePrice -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card prod-p-card background-pattern-white">
                    <div class="card-body">
                        <h6 class="m-b-5">จำนวนการอัพเดท SalePrice ในแต่ละเดือน</h6>
                        <canvas id="salePriceChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <!-- กราฟการอัพเดท BuyPrice -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card prod-p-card background-pattern-white">
                    <div class="card-body">
                        <h6 class="m-b-5">จำนวนการอัพเดท BuyPrice ในแต่ละเดือน</h6>
                        <canvas id="buyPriceChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <!-- กราฟการอัพเดท PriceList -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card prod-p-card background-pattern-white">
                    <div class="card-body">
                        <h6 class="m-b-5">จำนวนการอัพเดท PriceList ในแต่ละเดือน</h6>
                        <canvas id="priceListChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content end -->
    </div>
</div>

<div id="service_detail_modal"></div>

<?php require_once(home_path().'config/footer/footer.php'); ?>

<!-- เพิ่ม Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    $('.prod-p-card').matchHeight();

    // แปลงข้อมูลจาก PHP เป็น format ที่ใช้กับกราฟ
    var uploadLabels = Object.keys(uploadData);
    var uploadCounts = Object.values(uploadData);
    var standardCostCounts = Object.values(standardCostData);
    var salePriceCounts = Object.values(salePriceData);
    var buyPriceCounts = Object.values(buyPriceData);
    var priceListCounts = Object.values(priceListData);

    // สร้าง array สำหรับรวมข้อมูล
    var combinedCounts = uploadCounts.map((count, index) => {
        return count + (standardCostCounts[index] || 0) + (salePriceCounts[index] || 0) + (buyPriceCounts[index] || 0) + (priceListCounts[index] || 0) - 4;
    });

    // กราฟการอัพโหลดไฟล์รวม
    var ctxUpload = document.getElementById('uploadChart').getContext('2d');
    var uploadChart = new Chart(ctxUpload, {
        type: 'bar',
        data: {
            labels: uploadLabels,
            datasets: [{
                label: 'จำนวนการอัพโหลดไฟล์รวมในแต่ละเดือน',
                data: combinedCounts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // กราฟการอัพเดท Standardcost
    var ctxStandardCost = document.getElementById('standardCostChart').getContext('2d');
    var standardCostChart = new Chart(ctxStandardCost, {
        type: 'line',
        data: {
            labels: uploadLabels,
            datasets: [{
                label: 'จำนวนการอัพเดท Standardcost ในแต่ละเดือน',
                data: standardCostCounts,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // กราฟการอัพเดท SalePrice
    var ctxSalePrice = document.getElementById('salePriceChart').getContext('2d');
    var salePriceChart = new Chart(ctxSalePrice, {
        type: 'line',
        data: {
            labels: uploadLabels,
            datasets: [{
                label: 'จำนวนการอัพเดท SalePrice ในแต่ละเดือน',
                data: salePriceCounts,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // กราฟการอัพเดท BuyPrice
    var ctxBuyPrice = document.getElementById('buyPriceChart').getContext('2d');
    var buyPriceChart = new Chart(ctxBuyPrice, {
        type: 'line',
        data: {
            labels: uploadLabels,
            datasets: [{
                label: 'จำนวนการอัพเดท BuyPrice ในแต่ละเดือน',
                data: buyPriceCounts,
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // กราฟการอัพเดท PriceList
    var ctxPriceList = document.getElementById('priceListChart').getContext('2d');
    var priceListChart = new Chart(ctxPriceList, {
        type: 'line',
        data: {
            labels: uploadLabels,
            datasets: [{
                label: 'จำนวนการอัพเดท PriceList ในแต่ละเดือน',
                data: priceListCounts,
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
