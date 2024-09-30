<?php
require_once 'config/include.php';
global $conn;
date_default_timezone_set("Asia/Bangkok");

// ตรวจสอบการ login
//is_login();

?>

<?php require_once home_path() . 'config/header/header.php';?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show animated fadeInDown" role="alert" style=" width: 20%; position: fixed; top: 10px; left: 25%; width: 50%; z-index: 1000;">
        <p style="text-align: center; font-size: 15px; color: #88141f; margin-bottom: 0px;"><?php error_msg($_GET['error']);?></p>
        <button type="button" class="close" id="close_warning" data-dismiss="alert" aria-label="Close"><span class="fa fa-times"></span></button>
    </div>
<?php endif;?>

<!-- auth-signin start -->
<div class="auth-wrapper">
	<div class="auth-content">
		<div class="card">
			<div class="row align-items-center text-center">
				<div class="col-md-12">
					<div class="card-body">
						<form id="login_form" class="needs-validation" method="post" action="<?=home_url()?>login.php" enctype="multipart/form-data">
							<img src="assets/images/logo.svg" alt="" class="img-fluid mb-4">
							<h4 class="mb-4 f-w-400">ล็อกอินเข้าระบบ</h4>

							<div class="row mb-3">
								<div class="col-12 text-left">
									<div class="input-group">
										<span class="input-group-text"><i data-feather="user"></i></span>
										<input type="text" id="username" name="username" class="form-control" placeholder="ชื่อผู้ใช้งาน" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="row mb-4">
								<div class="col-12 text-left">
									<div class="input-group">
										<span class="input-group-text"><i data-feather="lock"></i></span>
										<input type="password" id="password" name="password" class="form-control" placeholder="รหัสผ่าน" autocomplete="off">
										<div class="hide-show-login">
                                			<i class="fas fa-eye" aria-hidden="true"></i>
                            			</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<button type="submit" name="userLogin" id="userLogin" class="btn btn-block btn-primary mb-4 disabled">เข้าสู่ระบบ</button>
									<p class="mb-2 text-muted"><a href="<?=module_url()?>" class="f-w-400"><i class="fas fa-arrow-left"></i> กลับไปหน้าหลัก (โมดูลอื่นๆ)</a></p>
									<p class="mb-2 text-muted">เพื่อความเสถียรของระบบให้ใช้งานระบบผ่าน<br><i><img src="<?=home_url()?>assets/images/browser/chrome.png" style="width: 35px;"></i> Google Chrome</p>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- auth-signin end -->

<?php require_once home_path() . '/config/footer/footer.php';?>

<script type="text/javascript">
$(document).ready(function () {
	$('#username').keyup(function () {
        var length_username = $('#username').val();
        var length_password = $('#password').val();

        if (length_username != "" && length_password != "") {
            $('#userLogin').removeClass('disabled');
        }
        else {
            $('#userLogin').addClass('disabled');
        }
    });

    $('#password').keyup(function () {
        var length_username = $('#username').val();
        var length_password = $('#password').val();

        if (length_username != "" && length_password != "") {
            $('#userLogin').removeClass('disabled');
        }
        else {
            $('#userLogin').addClass('disabled');
        }
    });

    $('#close_warning').click(function () {
    	var CURRENT_URL = window.location.href.split("#")[0].split("?error")[0];
        window.location = CURRENT_URL;
    });
});
</script>
