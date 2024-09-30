<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
date_default_timezone_set("Asia/Bangkok");

function getUsersAll() {
    global $conn;
    $result = array();

    $select = "*";
    $where = "";
    $groupby = "";
    $orderby = "ID";
    $orderby_key = "asc";

    $query = selectUsers($select, $where, $groupby, $orderby, $orderby_key);
    while($arr = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $data = array(
        'ID'            => $arr['ID'],
        'UserName'      => $arr['UserName'], 
        'Password'      => $arr['Password'], 
        'Full_Name'     => $arr['Full_Name'], 
        'Status'        => $arr['Status'],
        'Major'         => $arr['Major'], 
        'Group'         => $arr['Group']);

        $result[] = $data;
    }

    return $result;
}

function getUsersById($id) {
    global $conn;
    $result = array();

    $select = "*";
    $where = "ID = '".$id."'";
    $groupby = "";
    $orderby = "ID";
    $orderby_key = "asc";

    $query = selectUsers($select, $where, $groupby, $orderby, $orderby_key);
    while($arr = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $data = array(
        'ID'            => $arr['ID'],
        'UserName'      => $arr['UserName'], 
        'Password'      => $arr['Password'], 
        'Full_Name'     => $arr['Full_Name'], 
        'Status'        => $arr['Status'],
        'Major'         => $arr['Major'], 
        'Group'         => $arr['Group']);

        $result[] = $data;
    }

    return $result;
}

function changePasswordByUser() {
    global $conn;

    $user_id = $_POST['user_id'];
    $reset_username = $_POST['reset_username'];
    $reset_password = $_POST['reset_password'];
    $user_id_encode = base64_encode(json_encode($user_id));

    $set_arr = "Password = '".$reset_password."'";
    $where_arr = "ID = '".$user_id."'";
    $data_update = updateUsers($set_arr, $where_arr);

    if ($data_update) {
        echo '<script>
            Swal.fire({
                title: "Success!",
                text: "เปลี่ยนรหัสผ่านสำเร็จ สามารถใช้รหัสผ่านใหม่ล็อกอินเข้าใช้งานระบบได้แล้ว กรุณาออกจากระบบ",
                icon: "info",
                showCancelButton: false,
                confirmButtonColor: "#1690ed",
                confirmButtonText: "ออกจากระบบ",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = "'.home_url().'logout.php";
                }
            });
            </script>';
    }
    else {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "เปลี่ยนรหัสผ่านไม่สำเร็จ",
                icon: "error",
                confirmButtonColor: "#1690ed",
            }).then(function() {
                window.location = "'.home_url().'page/user-account/change-password.php?ID='.$user_id_encode.'";
            });
            </script>';
    }
}

function selectUsers($select = '', $where = '', $groupby = '', $orderby = '', $orderby_key) {
    global $conn;
    if (!empty($where)) { $where_all = "WHERE $where"; } 
    else { $where_all = ""; }

    if (!empty($groupby)) { $groupby_all = "GROUP BY $groupby"; }
    else { $groupby_all = ""; }

    if (!empty($orderby)) { $orderby_all = "ORDER BY $orderby $orderby_key"; }
    else { $orderby_all = ""; }

    $sql = "SELECT $select FROM admin $where_all $groupby_all $orderby_all";
    $result = sqlsrv_query($conn, $sql);
    return $result;
}

function joinUsers($select = '', $table = '', $join = '', $where = '', $groupby = '', $orderby = '', $orderby_key = '') {
    global $conn;
    if (!empty($where)) { $where_all = "WHERE $where"; } 
    else { $where_all = ""; }

    if (!empty($groupby)) { $groupby_all = "GROUP BY $groupby"; }
    else { $groupby_all = ""; }

    if (!empty($orderby)) { $orderby_all = "ORDER BY $orderby $orderby_key"; }
    else { $orderby_all = ""; }

    $sql = "SELECT $select FROM $table $join $where_all $groupby_all $orderby_all";
    $result = sqlsrv_query($conn, $sql);

    return $result;
}

function deleteUsers($key, $id) {
    global $conn;
    $sql = "DELETE FROM admin WHERE $key ='$id'";
    $result   = sqlsrv_query($conn, $sql);

    return $result;
}

function insertUsers($col_arr, $val_arr) {
    global $conn;
    $sql = "INSERT INTO admin($col_arr) VALUES ($val_arr)";
    $result = sqlsrv_query($conn, $sql);
    return $result;

}

function updateUsers($set_arr, $where_arr) {
    global $conn;
    $sql = "UPDATE admin SET $set_arr WHERE $where_arr";
    $result = sqlsrv_query($conn, $sql);
    return $result;
}

?>