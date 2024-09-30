<?php 
session_start(); 
require_once('connect.php');
require_once('error-msg.php');

// $user_ip = $_SERVER["REMOTE_ADDR"];
// $ex_ip = explode(".", $user_ip);
// $IP_ADDR = $ex_ip[0].".".$ex_ip[1];

//server production
// if ($IP_ADDR == "192.168") {
//    define('HOME_URI', 'http://192.168.11.252:41984/update-cost/');
//    define('MODULE_URI', 'http://192.168.11.252:41984/internal-systems/');
// }
// elseif ($IP_ADDR == "172.31") {
//    define('HOME_URI', 'http://192.168.11.252:41984/update-cost/');
//    define('MODULE_URI', 'http://192.168.11.252:41984/internal-systems/');
// }
// else {
//    define('HOME_URI', 'http://sangchaistock.dyndns.org:41984/update-cost/');
//    define('MODULE_URI', 'http://sangchaistock.dyndns.org:41984/internal-systems/');
// }

//server test
define('HOME_URI', 'http://localhost/update-cost/');
define('MODULE_URI', 'http://192.168.11.252:41984/internal-systems/');


define('HOME_PATH', $_SERVER["DOCUMENT_ROOT"].'/update-cost/');
define('HEADER_PATH', HOME_PATH.'/config/header');
define('FOOTER_PATH', HOME_PATH.'/config/footer');

function home_url($path=''){
  return HOME_URI.$path;
}

function home_path($path=''){
  return HOME_PATH.$path;
}

function module_url($path=''){
  return MODULE_URI.$path;
}

function get_header($path=''){
  require_once(HEADER_PATH.'/header.php');
}

function get_footer($path=''){
  require_once(FOOTER_PATH.'/footer.php');
}

function css_link(){
?>
   <!-- bootstrap css -->
   <link rel="stylesheet" href="<?=home_url();?>assets/css/plugins/bootstrap.min.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/library/select2/css/select2.min.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/library/dataTable/dataTables.bootstrap4.min.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/library/dataTable/responsive.bootstrap4.min.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/library/bootstrap-toggle/bootstrap4-toggle.min.css">

   <!-- animate css -->
   <link rel="stylesheet" href="<?=home_url();?>assets/css/plugins/animate.min.css">

   <!-- icon css -->
   <link rel="stylesheet" href="<?=home_url();?>assets/fonts/feather.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/fonts/all.min.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/fonts/material.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/fonts/themify.css">

   <!-- library css -->
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/jquery-ui/jquery-ui.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/pagination/pagination.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/datepicker/bootstrap-datepicker.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/magnificPopup/magnific-popup.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/sweetAlert/sweetalert.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/viewbox/viewbox.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/autocomplete/easy-autocomplete.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/autocomplete/easy-autocomplete.themes.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/swiper/swiper.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/timepicker/material-timepicker.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/daterangepicker/daterangepicker.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/datetimepicker/jquery.datetimepicker.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/jquery-timepicker/jquery.timepicker.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/owl-carousel/owl.carousel.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/owl-carousel/owl.theme.default.min.css">
   <link rel="stylesheet" href="<?php echo home_url(); ?>assets/library/fullcalendar-3.10.2/fullcalendar.css">

   <!-- custom css -->
   <link rel="stylesheet" href="<?=home_url();?>assets/css/style.css">
   <link rel="stylesheet" href="<?=home_url();?>assets/css/custom.css">

<?php
}

function js_link(){
?>
   <!-- jquery -->
   <script src="<?php echo home_url(); ?>assets/library/modernizr/jquery.modernizr.min.js"></script>
   <!-- <script src="<?php echo home_url(); ?>assets/js/plugins/jquery-2.2.4.min.js"></script> -->
   <script src="<?php echo home_url(); ?>assets/js/plugins/jquery-3.5.1.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/js/vendor-all.min.js"></script>

   <!-- bootstrap js -->
   <script src="<?php echo home_url(); ?>assets/js/plugins/bootstrap.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/select2/js/select2.full.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/js/plugins/feather.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/js/pcoded.min.js"></script>

   <!-- plugins js -->
   <script src="<?php echo home_url(); ?>assets/js/plugins/apexcharts.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/js/plugins/perfect-scrollbar.min.js"></script>

   <!-- library js -->
   <script src="<?php echo home_url(); ?>assets/library/bootstrap-toggle/bootstrap4-toggle.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/validate/jquery.validate.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/jquery-cookie/jquery.cookie.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/jquery-ui/jquery-ui.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/magnificPopup/magnific-popup.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/viewbox/viewbox.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/sweetAlert/sweetalert.all.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/swiper/swiper.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/moment/moment.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/moment/moment-with-locales.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/datepicker/bootstrap-datepicker.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/datepicker/bootstrap-datepicker.th.min.js"></script>
   <!-- <script src="<?php echo home_url(); ?>assets/library/datepicker/bootstrap-datepicker-thai.js"></script> -->
   <script src="<?php echo home_url(); ?>assets/library/combodate/combodate.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/ckeditor/ckeditor.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/autocomplete/easy-autocomplete.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/dataTable/jquery.dataTables.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/dataTable/dataTables.bootstrap4.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/dataTable/dataTables.responsive.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/dataTable/responsive.bootstrap4.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/timepicker/material-timepicker.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/autosize/autosize.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/daterangepicker/daterangepicker.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/datetimepicker/jquery.datetimepicker.full.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/jquery-timepicker/jquery.timepicker.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/owl-carousel/owl.carousel.min.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/fullcalendar-3.10.2/fullcalendar.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/fullcalendar-3.10.2/locale/th.js"></script>
   <script src="<?php echo home_url(); ?>assets/library/jquery-matchHeight/jquery.matchHeight-min.js"></script>


   <!-- custom js -->
    <script src="<?php echo home_url(); ?>assets/js/custom.js"></script>

<?php
}

function error_msg($error_code) {
   switch ($error_code) {
      case $GLOBALS['error1']:
         echo $GLOBALS['login_incorrect'];
         break;

      case $GLOBALS['error2']:
         echo $GLOBALS['please_login'];
         break;

      case $GLOBALS['error3']:
         echo $GLOBALS['no_permission'];
         break;

      case $GLOBALS['error4']:
         echo $GLOBALS['user_expire'];
         break;

      case $GLOBALS['error5']:
         echo $GLOBALS['username_already'];
         break;

      case $GLOBALS['error6']:
         echo $GLOBALS['username_emptry'];
         break;

      case $GLOBALS['error7']:
         echo $GLOBALS['password_emptry'];
         break;

      case $GLOBALS['error8']:
         echo $GLOBALS['already_login'];
         break;

      case $GLOBALS['error9']:
         echo $GLOBALS['login_timeout'];
         break;

      case $GLOBALS['error10']:
         echo $GLOBALS['no_access'];
         break;
   }
}

function control_screen($env_name) {
   return $GLOBALS[$env_name];
}

function create_login() {
   global $conn;
   date_default_timezone_set("Asia/Bangkok");

   $username = $_POST['username'];
   $password = $_POST['password'];

   $username = stripslashes($username);
   $password = stripslashes($password);
   $username = trim($username);
   $password = trim($password);

   $sql = "SELECT * from admin where UserName = '$username' and Password = '$password'";
   $query = sqlsrv_query($conn, $sql, array(), array("Scrollable"=>"buffered"));
   $allRow = sqlsrv_num_rows($query);
   $arrMember = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);

   if ($allRow == 1) {
      $_SESSION['auth_sc'] = $arrMember['UserName'];
   }
   else {
      header("Location:".HOME_URI."?error=err01");
      exit;
   }
}

function logout() {
   global $conn;

   unset($_SESSION['auth_sc']);
   if(empty($_SESSION['auth_sc'])) {
      header("Location:".HOME_URI);
   }
}

function is_login() {
   global $conn;

   if(!empty($_SESSION['auth_sc'])) {
      $session_user = $_SESSION['auth_sc'];

      $sql = "SELECT * from admin where UserName = '$session_user'";
      $query = sqlsrv_query($conn, $sql);
      while ($arr = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
         $login_session = $arr['UserName'];
      }

      if(!isset($login_session)) {
         header("Location:".HOME_URI."?error=err02");
         exit;
      }
   } 
   else {
      header("Location:".HOME_URI."?error=err02");
      exit;
   }  
}

function current_user(){
   global $conn;

   $current_user = $_SESSION['auth_sc'];
   $sql = "SELECT * from admin where UserName = '$current_user'";
   $query = sqlsrv_query($conn, $sql);
   $arr = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);

   $user_id          = $arr['ID'];
   $user_fullname    = trim($arr['Full_Name']);
   $user_username    = trim($arr['UserName']);
   $user_role        = trim($arr['Status']);

   $user_info           = array(
      'user_id'         => $user_id,
      'user_fullname'   => $user_fullname, 
      'user_username'   => $user_username, 
      'user_role'       => $user_role);

   return $user_info;
}

function user_info(){
   $user_info = current_user();
   $result_user =  $user_info['user_fullname'] != '' ? $user_info['user_fullname'] : $user_info['user_username'];

   return $result_user;
}

?>