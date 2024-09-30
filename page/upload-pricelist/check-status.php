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
        case 'Test':
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
    
    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];
        $docuno = $row[0];  // คอลัมน์ A (index 0)
        $listno = $row[17]; // คอลัมน์ Y (index 24)
        $ListID = $row[18];
        $goodcode = $row[19]; // คอลัมน์ Z (index 25)
        $database = $row[30];  // ดึงชื่อฐานข้อมูลจากข้อมูลที่อัปโหลด

        // Check if dropdown value from database matches with the one from the row
        if ($database !== $dropdownValue) {
            $statusArray[] = ['status' => 'ฐานข้อมูลไม่ตรงกัน'];
            continue;
        }

        // SQL Query เพื่อเช็ค docuno, goodcode และ listno
        $sqlCheck = "SELECT b.docuno, c.goodcode, a.listno, a.ListID
                     FROM EMSetPriceHD b
                     LEFT OUTER JOIN EMSetPriceDT a ON a.SetPriceID = b.SetPriceID
                     LEFT OUTER JOIN EMGood c ON a.ListID = c.GoodID
                     WHERE b.docuno = ? AND c.goodcode = ? AND a.listno = ? AND a.ListID = ?";
        $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$docuno, $goodcode, $listno, $ListID]);

        // ตรวจสอบว่ามีข้อผิดพลาดใน query หรือไม่
        if ($stmtCheck === false) {
            $statusArray[] = ['status' => 'Error'];
            continue;
        }
        
        // ดึงข้อมูลจากผลลัพธ์ของ query
        $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
        
        // ตรวจสอบว่าพบข้อมูลหรือไม่
        if ($rowCheck && $rowCheck['goodcode'] === $goodcode && $rowCheck['docuno'] === $docuno && $rowCheck['listno'] === $listno) {
            $statusArray[] = ['status' => 'สามารถอัพเดทได้'];
        } else {
            $statusArray[] = ['status' => 'ไม่พบข้อมูล'];
        }
    }

    // ส่งผลลัพธ์กลับเป็น JSON
    echo json_encode($statusArray);
}
?>
