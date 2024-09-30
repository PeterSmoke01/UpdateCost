<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel/IOFactory.php');

date_default_timezone_set("Asia/Bangkok");

is_login();
$current_user = current_user();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['export']) && $_POST['export'] == 'excel') {
    $dropdownValue = isset($_POST['dropdown']) ? $_POST['dropdown'] : '';
    $textInputValue = isset($_POST['textInput']) ? $_POST['textInput'] : '';
    $searchType = isset($_POST['searchType']) ? $_POST['searchType'] : 'both';

    switch ($dropdownValue) {
        case 'SCA':
            $conn = $GLOBAL['conn_sca'];
            $databaseName = 'SCA'; // Replace with actual database name or description
            break;
        case 'SCA10':
            $conn = $GLOBAL['conn_sca10'];
            $databaseName = 'SCA10';
            break;
        case 'SCO':
            $conn = $GLOBAL['conn_sco'];
            $databaseName = 'SCO';
            break;
        case 'SCORP':
            $conn = $GLOBAL['conn_scorp'];
            $databaseName = 'SCORP';
            break;
        case 'WECHILL':
            $conn = $GLOBAL['conn_wechill'];
            $databaseName = 'WECHILL';
            break;
        case 'Test':
            $conn = $GLOBAL['conn_test'];
            $databaseName = 'Test';
            break;
        default:
            $conn = null;
            $databaseName = 'Unknown';
            break;
    }
    
    if ($conn === false || $conn === null) {
        echo "Invalid database connection.";
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
        echo "Query failed: " . print_r(sqlsrv_errors(), true);
        exit;
    }

    $filename = $textInputValue . "_" . date('Ymd') . "_data-Price-List.xlsx";

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);

    // Set header cells
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'docuno');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'docutype');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'docudate');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'BeginDate');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'enddate');
    // $objPHPExcel->getActiveSheet()->setCellValue('F1', 'custflag');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Custflagname');
    // $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Goodflag');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Goodflagname');
    // $objPHPExcel->getActiveSheet()->setCellValue('J1', 'begintime');
    // $objPHPExcel->getActiveSheet()->setCellValue('K1', 'endtime');
    // $objPHPExcel->getActiveSheet()->setCellValue('L1', 'docuflag');
    // $objPHPExcel->getActiveSheet()->setCellValue('M1', 'PromotionFlag');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Datestatus');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'docstatus');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'doctype');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Custcode');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'CustName');
    // $objPHPExcel->getActiveSheet()->setCellValue('S1', 'CustGroupID');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'CustGroupCode');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'CustGroupName');
    // $objPHPExcel->getActiveSheet()->setCellValue('V1', 'GoodGroupID');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', 'GoodGroupCode');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', 'GoodGroupName');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Headremark');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', 'listno');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'ListID');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'goodcode');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', 'GoodName1');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', 'GoodUnitCode');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'GoodPrice');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', 'GoodDiscFormula');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', 'GoodDiscAmnt');
    $objPHPExcel->getActiveSheet()->setCellValue('Z1', 'GoodPriceNet');
    $objPHPExcel->getActiveSheet()->setCellValue('AA1', 'ItemRemark');
    $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'PriceBaseAmnt');
    $objPHPExcel->getActiveSheet()->setCellValue('AC1', 'startgoodqty');
    $objPHPExcel->getActiveSheet()->setCellValue('AD1', 'endgoodqty');
    $objPHPExcel->getActiveSheet()->setCellValue('AE1', 'database');

    // สร้างฟังก์ชันสำหรับสร้างชื่อคอลัมน์
    function getExcelColumnRange($start, $end) {
        $columns = [];
        $current = $start;
        
        while ($current != $end) {
            $columns[] = $current;
            $current++;
        }
        
        $columns[] = $end; // เพิ่มคอลัมน์สุดท้าย
        
        return $columns;
    }

    // เรียกใช้ฟังก์ชัน getExcelColumnRange เพื่อครอบคลุมคอลัมน์ตั้งแต่ A ถึง AD
    $columns = getExcelColumnRange('A', 'AE');

    // ปรับขนาดคอลัมน์ให้เหมาะสม
    foreach ($columns as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    // ก่อนที่คุณจะเพิ่มข้อมูลลงในคอลัมน์ V และ W
    $objPHPExcel->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    $objPHPExcel->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    
    // Add data
    $row = 2;
    while ($data = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $data['docuno']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $data['docutype']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $data['docudate']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $data['BeginDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $data['enddate']);
        // $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $data['custflag']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $data['Custflagname']);
        // $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $data['Goodflag']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $data['Goodflagname']);
        // $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $data['begintime']);
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $data['endtime']);
        // $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $data['docuflag']);
        // $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $data['PromotionFlag']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $data['Datestatus']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $data['docstatus']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $data['doctype']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $data['Custcode']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $data['CustName']);
        // $objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $data['CustGroupID']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $data['CustGroupCode']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $data['CustGroupName']);
        // $objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $data['GoodGroupID']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $data['GoodGroupCode']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $data['GoodGroupName']);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $data['Headremark']);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $data['listno']);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $data['ListID']);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $row, $data['goodcode']);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $data['GoodName1']);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $data['GoodUnitCode']);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $data['GoodPrice']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('X' . $row, $data['GoodDiscFormula'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $data['GoodDiscAmnt']);
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $data['GoodPriceNet']);
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, $data['ItemRemark']);
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $data['PriceBaseAmnt']);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $row, $data['startgoodqty']);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $row, $data['endgoodqty']);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $row, $dropdownValue); // Write database name
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $branchValue); // Write database name
        $row++;
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Price List');

    // Set active sheet index to the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Output to the client’s browser
    ob_end_clean(); // ล้าง output buffer
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');

    // Use PHPExcel IOFactory to create the Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

    // Ensure no extra output
    exit;
}
?>
