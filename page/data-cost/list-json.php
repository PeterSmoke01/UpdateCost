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
    $branchValue = isset($_POST['branch']) ? $_POST['branch'] : ''; // เพิ่มตัวแปร branch
    $textInputValue = isset($_POST['textInput']) ? $_POST['textInput'] : '';

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
    } else {
        // คำสั่ง SQL พร้อมการค้นหาด้วย LIKE
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
            echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . print_r(sqlsrv_errors(), true)]);
            exit;
        }

        $result = [];
        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
            $result[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $result]);
    }
}
?>
