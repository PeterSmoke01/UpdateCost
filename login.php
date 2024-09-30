<?php 
require_once('config/include.php');
global $conn;

if (isset($_POST['userLogin'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("Location: ".home_url()."?error=err01");
        exit;
    } 
    else {
      	create_login();
    }
}
else {
    is_login();
}

$current_user = current_user();
$arr_user = array(1, 460, 461);

if (in_array($current_user['user_id'], $arr_user, true)) {
    header("Location: ".home_url()."page/dashboard.php");
    exit();
}
else {
   header("Location: ".home_url()."?error=err03");
   exit();
}

?>


