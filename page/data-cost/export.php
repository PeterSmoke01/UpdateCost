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
    $branchValue = isset($_POST['branch']) ? $_POST['branch'] : ''; // เพิ่มตัวแปร branch
    $textInputValue = isset($_POST['textInput']) ? $_POST['textInput'] : '';

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

    $sql = "SELECT  a.Goodid, a.Goodcode, a.Goodname1, a.Maingoodunitid,
                    b.GoodUnitCode, a.Standardcost, a.StandardSalePrce, a.StandardBuyPrce,
                    (SELECT SUM(dt.remacost)
                    FROM iccostdetail dt
                    WHERE dt.goodid = a.Goodid
                    AND (dt.brchid = ? OR ? = 0)
                    AND dt.stockflag in (1,-1)
                    AND dt.costdetailid = (SELECT MAX(costdt.costdetailid)
                                        FROM iccostdetail costdt
                                        WHERE costdt.goodid = a.Goodid
                                        AND (costdt.brchid = ? OR ? = 0)
                                        AND costdt.stockflag in (1,-1))) AS remacost,
                                        (CASE
                                            (SELECT sum(dt.remacost)
                                                                FROM iccostdetail dt
                                                                WHERE dt.goodid = a.Goodid
                                                                AND (dt.brchid = ? OR ? = 0)
                                                                AND dt.stockflag in (1,-1)
                                                                AND dt.costdetailid = (SELECT MAX(costdt.costdetailid)
                                                                                    FROM iccostdetail costdt
                                                                                    WHERE costdt.goodid = a.Goodid
                                                                                    AND (costdt.brchid = ? OR ? = 0)
                                                                                    AND costdt.stockflag in (1,-1)))  
                                            WHEN  0.00
                                            THEN 
                                            (SELECT sum(dt.PayCost)
                                                                FROM iccostdetail dt
                                                                WHERE dt.goodid = a.Goodid
                                                                AND (dt.brchid = ? OR ? = 0)
                                                                AND dt.stockflag in (1,-1)
                                                                AND dt.costdetailid = (SELECT MAX(costdt.costdetailid)
                                                                                    FROM iccostdetail costdt
                                                                                    WHERE costdt.goodid = a.Goodid
                                                                                    AND (costdt.brchid = ? OR ? = 0)
                                                                                    AND costdt.stockflag in (1,-1))) 
                                            ELSE
                                                (SELECT sum(dt.remacost)
                                                                FROM iccostdetail dt
                                                                WHERE dt.goodid = a.Goodid
                                                                AND (dt.brchid = ? OR ? = 0)
                                                                AND dt.stockflag in (1,-1)
                                                                AND dt.costdetailid = (SELECT MAX(costdt.costdetailid)
                                                                                    FROM iccostdetail costdt
                                                                                    WHERE costdt.goodid = a.Goodid
                                                                                    AND (costdt.brchid = ? OR ? = 0)
                                                                                    AND costdt.stockflag in (1,-1)))  
                                            END)AVGCOST
                FROM EMGood a
                LEFT OUTER JOIN EMGoodUnit b ON a.MainGoodUnitID = b.GoodUnitID
                WHERE (a.Costestimate = '1') 
                AND (a.GoodTypeflag NOT IN ('S','E')) 
                AND a.GoodCode LIKE ?
                order by a.Goodcode asc";

    // เตรียมคำสั่ง SQL และ execute
    $params = [$branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $branchValue, $textInputValue];
    $query = sqlsrv_query($conn, $sql, $params);

    if ($query === false) {
        echo "Query failed: " . print_r(sqlsrv_errors(), true);
        exit;
    }

    $filename = $textInputValue . "_" . date('Ymd') . "_Data-Cost.xlsx";

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);

    // Set header cells
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Goodid');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Goodcode');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Goodname1');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Maingoodunitid');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'GoodUnitCode');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Standardcost');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'StandardSalePrce');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'StandardBuyPrce');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'remacost');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'AVGCOST');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Database'); // New column for database name
    // $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Brchid'); // New column for database name

    // Adjust column width to fit the content
    foreach(range('A','J') as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Add data
    $row = 2;
    while ($data = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $data['Goodid']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $data['Goodcode']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $data['Goodname1']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $data['Maingoodunitid']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $data['GoodUnitCode']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $data['Standardcost']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $data['StandardSalePrce']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $data['StandardBuyPrce']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $data['remacost']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $data['AVGCOST']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $dropdownValue); // Write database name
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $branchValue); // Write database name
        $row++;
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Cost');

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
