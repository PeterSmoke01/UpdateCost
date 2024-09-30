<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['term']) && isset($_POST['database']) && isset($_POST['searchType'])) {
    $term = $_POST['term'];
    $databaseValue = $_POST['database'];
    $searchType = $_POST['searchType'];

    // ใช้ตัวแปรเชื่อมต่อที่เหมาะสมตามค่าที่เลือก
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
        case 'Test':
            $conn = $GLOBAL['conn_test'];
            break;
        default:
            $conn = null;
            break;
    }

    // ตรวจสอบการเชื่อมต่อ
    if ($conn === false || $conn === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid database connection.']);
        exit;
    }

    // คำสั่ง SQL เบื้องต้น
    $sql = "SELECT TOP 15 b.docuno, c.goodcode
            FROM EMSetPriceDT a
            LEFT OUTER JOIN emsetpricehd b ON (a.SetPriceID = b.SetPriceID) 
            LEFT OUTER JOIN emgood c ON (a.ListID = c.goodid)
            WHERE 1=1";

    // เพิ่มเงื่อนไขการค้นหาตามประเภทที่เลือก
    if ($searchType == 'docuno') {
        $sql .= " AND b.docuno LIKE ?";
    } elseif ($searchType == 'goodcode') {
        $sql .= " AND c.goodcode LIKE ?";
    } 
    // else { // กรณี 'both'
    //     $sql .= " AND (b.docuno LIKE ? OR c.goodcode LIKE ?)";
    // }

    $searchPattern = "%" . $term . "%"; // ใช้ '%' เพื่อให้การค้นหามีประสิทธิภาพมากขึ้น

    // ใช้ search pattern ใน SQL query
    if ($searchType == 'both') {
        $params = [$searchPattern, $searchPattern];
    } else {
        $params = [$searchPattern];
    }

    $query = sqlsrv_query($conn, $sql, $params);
    
    if ($query === false) {
        echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    $result = [];
    while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        if ($searchType === 'docuno') {
            $result[] = [
                'value' => $row['docuno'],
            ];
        } elseif ($searchType === 'goodcode') {
            $result[] = [
                'value' => $row['goodcode'],
            ];
        } 
        // else { // กรณี 'both'
        //     // เพิ่มข้อมูล docuno
        //     $result[] = [
        //         'value' => $row['docuno'],
        //     ];
        //     $result[] = [
        //         'value' => $row['goodcode'],
        //     ];
        // }
    }

    echo json_encode(['status' => 'success', 'data' => $result]);
}
?>
