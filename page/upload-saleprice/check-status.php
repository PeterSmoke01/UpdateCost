<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
    $dropdownValue = isset($_POST['database']) ? $_POST['database'] : '';

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
        case 'option6':
            $conn = $GLOBAL['conn_test'];
            break;
        default:
            $conn = null;
            break;
    }

    if ($conn === null) {
        echo json_encode([]);
        exit;
    }

    $statusArray = [];
    
    for ($i = 1; $i < count($data); $i++) {  // Start from 1 to skip header
        $row = $data[$i];
        $goodid = $row[0];
        $goodcode = $row[1];
        $database = $row[10];

        // Check if dropdown value from database matches with the one from the row
        if ($database !== $dropdownValue) {
            $statusArray[] = ['status' => 'ฐานข้อมูลไม่ตรงกัน'];
            continue;
        }

        $sqlCheck = "SELECT Goodcode FROM EMGood WHERE Goodid = ?";
        $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$goodid]);

        if ($stmtCheck === false) {
            $statusArray[] = ['status' => 'Error'];
            continue;
        }
        
        $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
        
        if ($rowCheck && $rowCheck['Goodcode'] === $goodcode) {
            $statusArray[] = ['status' => 'สามารถอัพเดทได้'];
        } else {
            $statusArray[] = ['status' => 'ไม่พบข้อมูล'];
        }
    }

    echo json_encode($statusArray);
}
?>
