<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel/IOFactory.php');

date_default_timezone_set("Asia/Bangkok");

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax']) && $_POST['ajax'] == true) {
    $databaseValue = isset($_POST['database']) ? $_POST['database'] : '';
    // $branchValue = isset($_POST['branch']) ? $_POST['branch'] : ''; // เพิ่มตัวแปร branch
    $textInputValue = isset($_POST['textInput']) ? $_POST['textInput'] : '';
    $searchType = isset($_POST['searchType']) ? $_POST['searchType'] : 'both'; // รับค่าจาก dropdown ว่าจะค้นหาด้วยอะไร

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
    $sql = "SELECT b.docuno,b.docutype,b.docudate,b.BeginDate,b.enddate,b.custflag,
                    (CASE b.CustFlag 
                    WHEN 'A' THEN 'ลูกค้าทั้งหมด'
                    WHEN 'G' THEN 'กลุ่มลูกค้า'
                    WHEN 'C' THEN 'ลูกค้ารายตัว'
                    END ) Custflagname,
                    b.goodflag,
                    (CASE b.GoodFlag 
                    WHEN 'A' THEN 'สินค้าทั้งหมด'
                    WHEN 'G' THEN 'กลุ่มสินค้า'
                    WHEN 'C' THEN 'สินค้ารายตัว'
                    END ) Goodflagname,
                    b.begintime,b.endtime,b.docuflag,b.PromotionFlag,
                    (CASE WHEN b.enddate <= GETDATE() THEN 'สิ้นสุดการใช้งาน' ELSE 'ยังเปิดใช้งาน' END) Datestatus,
                    (CASE WHEN b.docuflag = 'N' THEN 'Inactive' ELSE 'Active' END) docstatus,
                    (CASE WHEN b.docutype = 751 THEN 'Price List' ELSE 'Price List Promotion' END) doctype,
                    isnull(d.CustCode,'') Custcode,
                    isnull(d.custname,'') CustName,
                    isnull(cast(nullif(e.CustGroupID,0) AS varchar),'') CustGroupID,
                    isnull(e.CustGroupCode,'') CustGroupCode,
                    isnull(e.CustGroupName,'') CustGroupName,
                    isnull(cast(nullif(h.GoodGroupID,0) AS varchar),'') GoodGroupID,
                    isnull(h.GoodGroupCode,'') GoodGroupCode,
                    isnull(h.GoodGroupName,'') GoodGroupName,
                    isnull (b.Remark1,'') Headremark,
                    a.listno,
                    a.ListID,
                    c.goodcode,
                    c.GoodName1,
                    f.GoodUnitCode,
                    a.GoodPrice,
                    isnull(a.GoodDiscFormula,'') GoodDiscFormula,
                    a.GoodDiscAmnt,
                    a.GoodPriceNet,
                    isnull(a.Remark,'') ItemRemark,
                    a.PriceBaseAmnt,
                    a.startgoodqty,
                    a.endgoodqty
            FROM EMSetPriceDT a
            LEFT OUTER JOIN emsetpricehd b ON (a.SetPriceID = b.SetPriceID) 
            LEFT OUTER JOIN emgood c ON (a.ListID = c.goodid)
            LEFT OUTER JOIN emcust d ON (b.custid = d.custid)
            LEFT OUTER JOIN EMCustGroup e ON (b.CustGroupID = e.CustGroupID)
            LEFT OUTER JOIN EMGoodUnit f ON (a.GoodUnitID = f.GoodUnitID)
            LEFT OUTER JOIN EMDept g ON (b.DeptID = g.DeptID)
            LEFT OUTER JOIN EMGoodGroup h ON (b.goodgroupid = h.goodgroupid)
            WHERE 1=1";

    // เพิ่มเงื่อนไขการค้นหาตามประเภทที่เลือก
    if ($searchType == 'docuno') {
        $sql .= " AND b.docuno LIKE ?";
    } elseif ($searchType == 'goodcode') {
        $sql .= " AND c.goodcode LIKE ?";
    } else { // กรณี 'both'
        $sql .= " AND (b.docuno LIKE ? OR c.goodcode LIKE ?)";
    }

    $searchPattern = isset($_POST['textInput']) ? $_POST['textInput'] : '%';

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
        $result[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $result]);
}
?>
