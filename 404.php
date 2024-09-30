<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/service-sc/config/include.php');
require_once(home_path().'controller/fn-users.php');
global $conn;
date_default_timezone_set("Asia/Bangkok");

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();

?>

<!-- include header start -->
<?php require_once(home_path().'config/header/header-page.php'); ?>
<!-- include header End -->

<div class="pc-container" style="display: flex; align-items: center; justify-content: center;">
    <div class="pcoded-content pb-4">

        <!-- Main Content start -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-3">
                <h1 class="text-center text-danger"><i class="far fa-face-frown fa-3x"></i></h1>
                <h3 class="text-center">คุณไม่ได้รับอนุญาตให้เข้าถึงหน้านี้</h3>
                <p class="text-center">ติดต่อผู้ดูแลระบบของคุณ หากคุณต้องการความช่วยเหลือ</p>
                <h2 class="text-center mt-5">
                    <a class="btn btn-dark" href="<?=home_url()?>page/"><i class="fas fa-arrow-circle-left"></i> กลับไปหน้าหลัก</a>
                </h2>
            </div>
        </div>
        <!-- Main Content end -->

    </div>
</div>

<?php require_once(home_path().'config/footer/footer.php'); ?>


