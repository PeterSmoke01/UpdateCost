<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

date_default_timezone_set("Asia/Bangkok");

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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $databaseValue = isset($_GET['database']) ? $_GET['database'] : '';
    $branchValue = isset($_GET['branch']) ? $_GET['branch'] : ''; // รับค่า branch
    $query = $_GET['query'];

    // กำหนดการเชื่อมต่อกับฐานข้อมูลตามค่าที่เลือก
    switch ($databaseValue) {
        case 'SCA':
            $conn = $GLOBAL['conn_sca'];
            break;
        case 'SCA10':
            $conn = $GLOBAL['conn_sca10'];
            break;
        case 'SCO':
            $conn = $GLOBAL['conn_sco'];
            break;
        case 'SCORP':
            $conn = $GLOBAL['conn_scorp'];
            break;
        case 'WECHILL':
            $conn = $GLOBAL['conn_wechill'];
            break;
        default:
            $conn = null;
            break;
    }

    if ($conn === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid database connection.']);
        exit;
    }

    // ปรับ SQL ให้เหมือนกับที่แก้ไขแล้ว
    $sql = "SELECT TOP 15 a.Goodcode
    FROM EMGood a
    WHERE a.Goodcode LIKE ?
    AND EXISTS (
        SELECT 1
        FROM iccostdetail dt
        WHERE dt.goodid = a.Goodid
        AND (dt.brchid = ? OR ? = 0)
    )";

    $params = ["%".$query."%", $branchValue, $branchValue];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . print_r(sqlsrv_errors(), true)]);
    exit;
    }

    $result = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $result[] = [
    'Goodcode' => $row['Goodcode'],
    ];
    }

echo json_encode(['status' => 'success', 'data' => $result]);
}
?>
