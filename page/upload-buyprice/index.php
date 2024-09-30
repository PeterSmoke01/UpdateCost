<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');

date_default_timezone_set("Asia/Bangkok");
global $conn;

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();


?>

<?php 
header("Location:".HOME_URI."page/upload-buyprice/buyprice.php");
?>


