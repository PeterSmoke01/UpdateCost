<?php 
require_once ($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php'); 
require_once ($_SERVER["DOCUMENT_ROOT"].'/update-cost/controller/fn-users.php');
global $conn;
date_default_timezone_set("Asia/Bangkok");

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();

if (isset($_GET['ID'])) {
    $id = json_decode(base64_decode($_GET['ID']));
}
else {
    $id = '';
}

?>

<!-- include header start -->
<?php require_once(HOME_PATH.'config/header/header-page.php'); ?>
<!-- include header End -->

<div class="pc-container">
    <div class="pcoded-content pb-4">
        <!-- breadcrumb start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10"><i data-feather="lock"></i> เปลี่ยนรหัสผ่าน</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">บัญชีผู้ใช้งาน</li>
                            <li class="breadcrumb-item">เปลี่ยนรหัสผ่าน</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb end -->

        <!-- Main Content start -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-3">
                <form id="change_password_form" class="needs-validation" method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                    <?php 
                    $allrow = count(getUsersById($id));
                    if ($allrow != 0) {
                    foreach (getUsersById($id) as $key => $value) {
                    ?>

                        <input type="hidden" name="user_id" id="user_id" value="<?=$value['ID']?>">
                        <div class="col-xl-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-xl-8">
                                            <h5>เปลี่ยนรหัสผ่าน</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                                            <div class="row">
                                                <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                                    <label for="reset_username" class="form-label">ชื่อผู้ใช้งาน <span class="mark">*</span></label>
                                                    <input type="text" class="form-control" id="reset_username" name="reset_username" value="<?=$value['UserName']?>" readonly>
                                                    <label id="reset_username-error" class="mb-0" for="reset_username"></label>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-2">
                                                    <label for="reset_password" class="form-label">รหัสผ่านใหม่  <span class="mark">*</span></label>
                                                    <input type="password" class="form-control" id="reset_password" name="reset_password" value="">
                                                    <div class="hide-show-repass">
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </div>
                                                    <p class="text-muted mb-0 mt-1" style="font-size: 12px;">- ประกอบด้วย 0-9, a-z, A-Z อย่างละ 1 ตัว</p>
                                                    <p class="text-muted mb-0" style="font-size: 12px;">- อักขระพิเศษในวงเล็บ (!@#$&*) อย่างน้อย 1 ตัว</p>
                                                    <p class="text-muted mb-0" style="font-size: 12px;">- จำนวนอย่างน้อย 8 ตัว แต่ไม่เกิน 16 ตัว</p>
                                                    <label id="reset_password-error" class="error mb-0" for="reset_password"></label>
                                                    <div id="strengthResult1"></div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-2">
                                                    <label for="reset_password_confirm" class="form-label">ยืนยันรหัสผ่าน <span class="mark">*</span></label>
                                                    <input type="password" class="form-control" id="reset_password_confirm" name="reset_password_confirm" value="">
                                                    <div class="hide-show-confirmpass">
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </div>
                                                    <label id="reset_password_confirm-error" class="mb-0" for="reset_password_confirm"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <button type="submit" id="changePasswordByUser" name="changePasswordByUser" class="btn btn-success float-right ml-1"><i data-feather="lock"></i> เปลี่ยนรหัสผ่าน</button>
                        </div>
                    <?php 
                    } //end foreach 
                    } 
                    else {
                    ?>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-xl-8">
                                            <h5>เปลี่ยนรหัสผ่าน</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h2 class="text-center">ไม่พบข้อมูล!</h2>
                                </div>
                            </div>
                        </div>
                    <?php 
                    } 
                    ?>
                    </div>
                </form>
            </div>
        </div>
        <!-- Main Content end -->

    </div>
</div>

<?php require_once(HOME_PATH.'/config/footer/footer.php'); ?>

<script type="text/javascript">

    $(function() {
        $.validator.addMethod(
         "regex",
         function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
         },
      );

        // configure your validation
        $("#change_password_form").validate({
            ignore: ":hidden",
            validClass: "is-valid",
            errorClass: "is-invalid",
            errorElement: "label",
            rules: {
                reset_password: {
                    required: true, 
                    regex: "^(?=.*\\d)(?=.*[a-z])(?=.*[!@#$&*])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$",
                    maxlength: 16,
                },
                reset_password_confirm: {
                    required: true,
                    equalTo: "#reset_password",
                },
            },
            messages: {
                reset_password: { 
                    required: 'โปรดกำหนดรหัสผ่านใหม่...', 
                    regex: 'โปรดระบุให้ถูกต้องตามรูปแบบ...',
                    maxlength: 'โปรดระบุอย่างน้อย 8-16 ตัวอักษร...',
                },
                reset_password_confirm: { 
                    required: 'โปรดยืนยันรหัสผ่าน...',
                    equalTo: 'โปรดระบุรหัสผ่านให้ตรงกัน...', 
                },
            }, 
        });

    });
</script>

<?php 
if(isset($_POST['changePasswordByUser'])) {
    changePasswordByUser();
}
?>