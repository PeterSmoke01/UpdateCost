<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();

// ตรวจสอบการส่งข้อมูลผ่าน AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dropdown'])) {
    $dropdownValue = $_POST['dropdown'];

    // ใช้ตัวแปรเชื่อมต่อที่เหมาะสมตามค่าที่เลือก
    switch ($dropdownValue) {
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
        case 'Test':
            $conn = $GLOBAL['conn_test'];
            break;
        default:
            $conn = null;
            break;
    }

    if ($conn === false || $conn === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid database connection.']);
        exit;
    }

    // คำสั่ง SQL สำหรับดึงข้อมูล BrchID
    $sql = "SELECT BrchID, BrchCode, BrchNameEng, BrchName FROM EMBrch";
    $query = sqlsrv_query($conn, $sql);

    if ($query === false) {
        echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    $result = [];
    while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $result[] = $row;
    }

    if(empty($result)) {
        echo json_encode(['status' => 'error', 'message' => 'No data found.']);
        exit;
    }

    echo json_encode(['status' => 'success', 'data' => $result]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
