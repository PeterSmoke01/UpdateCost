<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

// ฟังก์ชันคำนวณส่วนลด
function calculate_total_discount($good_price, $goods_dis_formula)
{
    $discounts = explode(',', $goods_dis_formula);
    $current_price = $good_price;
    $total_discount = 0;

    foreach ($discounts as $discount) {
        if (strpos($discount, '%') !== false) {
            $discount_value = floatval(str_replace('%', '', $discount));
            $current_discount = ($discount_value / 100) * $current_price;
        } else {
            $current_discount = floatval($discount);
        }

        $total_discount += $current_discount;
        $current_price -= $current_discount;
    }

    return [
        'good_price_net' => $current_price,
        'good_dis_amount' => $total_discount
    ];
}

if (isset($_POST['submit']) && isset($_FILES['file'])) {
    $dropdownValue = isset($_POST['database']) ? $_POST['database'] : '';
    $file = $_FILES['file'];

    // เชื่อมต่อฐานข้อมูลตาม dropdownValue
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
        echo '<script>alert("Invalid database connection."); window.location.href="pricelist.php";</script>';
        exit;
    }

    $fileUploaded = false;

    // ตรวจสอบการอัปโหลดไฟล์
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension === 'xlsx' || $fileExtension === 'xls') {
            require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel.php');
            require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel/IOFactory.php');

            $objPHPExcel = PHPExcel_IOFactory::load($fileTmpPath);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $updateSuccess = true;
            $dropdownValueMismatch = false;

            for ($row = 2; $row <= $highestRow; $row++) {
                $docuno = trim($worksheet->getCell('A'.$row)->getValue());
                $docutype = (int) $worksheet->getCell('B'.$row)->getValue();
                $listno = (int) $worksheet->getCell('R'.$row)->getValue();
                $ListID = (int) $worksheet->getCell('S'.$row)->getValue();
                $goodcode = trim($worksheet->getCell('T'.$row)->getValue());
                $goodPrice = (float) $worksheet->getCell('W'.$row)->getValue();
                $goodDiscFormula = trim($worksheet->getCell('X'.$row)->getValue());
                $database = trim($worksheet->getCell('AE'.$row)->getValue());
                

                if ($database !== $dropdownValue) {
                    $dropdownValueMismatch = true;
                    break;
                }

                // คำนวณ GoodDiscAmnt และ GoodPriceNet
                $discountResult = calculate_total_discount($goodPrice, $goodDiscFormula);
                $goodDiscAmnt = $discountResult['good_dis_amount'];
                $goodPriceNet = $discountResult['good_price_net'];

                echo "Checking docuno: $docuno, goodcode: $goodcode, listno: $listno , ListID: $ListID<br>";

                // ตรวจสอบข้อมูลในฐานข้อมูล
                $sqlCheck = "SELECT b.docuno, c.goodcode, a.listno, a.ListID
                            FROM EMSetPriceHD b
                            LEFT OUTER JOIN EMSetPriceDT a ON a.SetPriceID = b.SetPriceID
                            LEFT OUTER JOIN EMGood c ON a.ListID = c.GoodID
                            WHERE b.docuno = ? AND c.goodcode = ? AND a.listno = ? AND a.ListID";
                $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$docuno, $goodcode, $listno, $ListID]);

                if ($stmtCheck === false) {
                    echo '<script>alert("SQL Check failed: ' . print_r(sqlsrv_errors(), true) . '");</script>';
                    $updateSuccess = false;
                    break;
                }

                $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
                
                if ($rowCheck && $rowCheck['docuno'] === $docuno && $rowCheck['goodcode'] === $goodcode && $rowCheck['listno'] === $listno && $rowCheck['ListID'] === $ListID) {
                    // ถ้า docutype ไม่ใช่ 751 จะไม่อัปเดต PriceBaseAmnt
                    $sqlUpdate = "  UPDATE a 
                                    SET 
                                        a.GoodPrice = ?, 
                                        a.GoodDiscFormula = ?, 
                                        a.GoodDiscAmnt = ?, 
                                        a.GoodPriceNet = ?,
                                        a.PriceBaseAmnt = CASE 
                                                            WHEN b.docutype = '751' THEN ? 
                                                            ELSE a.PriceBaseAmnt 
                                                        END
                                    FROM EMSetPriceDT a
                                    LEFT OUTER JOIN emsetpricehd b ON a.SetPriceID = b.SetPriceID 
                                    LEFT OUTER JOIN emgood c ON a.ListID = c.goodid
                                    WHERE b.docuno = ? 
                                    AND c.goodcode = ? 
                                    AND listno = ?
                                    AND ListID = ?";
                    $paramsUpdate = [$goodPrice, $goodDiscFormula, $goodDiscAmnt, $goodPriceNet, $goodPrice, $docuno, $goodcode, $listno, $ListID];
                    // ทำการอัปเดตฐานข้อมูล
                    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);                
                    if ($stmtUpdate === false) {
                        echo '<script>alert("SQL Update failed: ' . print_r(sqlsrv_errors(), true) . '");</script>';
                        $updateSuccess = false;
                        break;
                    }
                } else {
                    echo '<script>alert("Data not found for docuno: ' . $docuno . ', goodcode: ' . $goodcode . ', listno: ' . $listno . ', ListID: ' . $ListID . '");</script>';
                    $updateSuccess = false;
                    break;
                }
            }

            if ($dropdownValueMismatch) {
                echo '<script>alert("ฐานข้อมูลไม่ถูกต้อง อัพเดทไม่สำเร็จ"); window.location.href="pricelist.php";</script>';
                exit;
            }

            if ($updateSuccess) {
                $uploadDir = $_SERVER["DOCUMENT_ROOT"] . '/update-cost/file-upload/pricelist/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newFileName = time() . '_' . basename($fileName);
                $uploadFilePath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                    $line_notify_msg =  "\nUpdate Price List\n" .
                                        "------------------------\n" .
                                        "ฐานข้อมูล : " . $dropdownValue . "\n" . 
                                        "วันที่อัพเดท : " . date('Y-m-d H:i:s') . "\n" .
                                        "ชื่อไฟล์ : " . $fileName . "\n" .
                                        "ผู้อัพเดท : " . $current_user['user_fullname'] . "\n" . // เปลี่ยนเป็นชื่อผู้ใช้ที่เกี่ยวข้อง
                                        "ผลการอัพเดท : อัพเดทสำเร็จ";
                    sendLineNotify($line_notify_msg);
                    echo '<script>alert("อัพเดทสำเร็จ และบันทึกไปยัง: ' . $uploadFilePath . '"); window.location.href="pricelist.php";</script>';
                } else {
                    echo '<script>alert("Failed to move uploaded file."); window.location.href="pricelist.php";</script>';
                }
            } else {
                echo '<script>alert("ไม่พบข้อมูล อัพเดทไม่สำเร็จ"); window.location.href="pricelist.php";</script>';
            }
        } else {
            echo '<script>alert("Invalid file type. Please upload an Excel file."); window.location.href="pricelist.php";</script>';
        }
    } else {
        echo '<script>alert("File upload error. Please try again."); window.location.href="pricelist.php";</script>';
    }
} else {
    echo '<script>alert("No file uploaded or submit button not clicked."); window.location.href="pricelist.php";</script>';
}
?>
