<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/include.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/config/connect.php');

// ตรวจสอบการ login
is_login();

// Current User
$current_user = current_user();

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
        case 'option6':
            $conn = $GLOBAL['conn_test'];
            break;
        default:
            $conn = null;
            break;
    }

    if ($conn === null) {
        echo '<script>alert("Invalid database connection."); window.location.href="standardcost.php";</script>';
        exit;
    }

    // ตรวจสอบการอัปโหลดไฟล์
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // ตรวจสอบประเภทไฟล์
        if ($fileExtension === 'xlsx' || $fileExtension === 'xls') {
            require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel.php');
            require_once($_SERVER["DOCUMENT_ROOT"].'/update-cost/assets/PHPExcel/PHPExcel/IOFactory.php');

            // โหลดไฟล์ Excel ที่อัปโหลด
            $objPHPExcel = PHPExcel_IOFactory::load($fileTmpPath);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $updateSuccess = true;
            $dropdownValueMismatch = false;  // Flag to check mismatch

            for ($row = 2; $row <= $highestRow; $row++) {
                $goodid = $worksheet->getCell('A'.$row)->getValue();
                $goodcode = $worksheet->getCell('B'.$row)->getValue();
                $standardCost = $worksheet->getCell('F'.$row)->getValue();
                $database = $worksheet->getCell('K'.$row)->getValue();  // สมมติค่า dropdown อยู่ในคอลัมน์ K

                // ตรวจสอบว่าค่า dropdownvalue ในไฟล์ตรงกับ dropdownValue ที่ผู้ใช้เลือกหรือไม่
                if ($database !== $dropdownValue) {
                    $dropdownValueMismatch = true;
                    break;
                }

                $sqlCheck = "SELECT Goodcode FROM EMGood WHERE Goodid = ?";
                $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$goodid]);
                
                if ($stmtCheck === false) {
                    $updateSuccess = false;
                    break;
                }

                $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
                
                if ($rowCheck && $rowCheck['Goodcode'] === $goodcode) {
                    $sqlUpdate = "UPDATE EMGood SET Standardcost = ? WHERE Goodid = ?";
                    $paramsUpdate = [$standardCost, $goodid];
                    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);
                    
                    if ($stmtUpdate === false) {
                        $updateSuccess = false;
                        break;
                    }
                } else {
                    $updateSuccess = false;
                    break;
                }
            }

            // ถ้าพบการ mismatch ค่า dropdownvalue
            if ($dropdownValueMismatch) {
                echo '<script>alert("ฐานข้อมูลไม่ถูกต้อง อัพเดทไม่สำเร็จ"); window.location.href="standardcost.php";</script>';
                exit;
            }

            // ถ้าการอัปเดตฐานข้อมูลสำเร็จ
            if ($updateSuccess) {
                // บันทึกไฟล์ลงในโฟลเดอร์
                $uploadDir = $_SERVER["DOCUMENT_ROOT"] . '/update-cost/file-upload/standardcost/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newFileName = time() . '_' . basename($fileName);
                $uploadFilePath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                    $line_notify_msg =  "\nUpdate Standard Cost\n" .
                                        "------------------------\n" .
                                        "ฐานข้อมูล : " . $dropdownValue . "\n" . 
                                        "วันที่อัพเดท : " . date('Y-m-d H:i:s') . "\n" .
                                        "ชื่อไฟล์ : " . $fileName . "\n" .
                                        "ผู้อัพเดท : " . $current_user['user_fullname'] . "\n" . // เปลี่ยนเป็นชื่อผู้ใช้ที่เกี่ยวข้อง
                                        "ผลการอัพเดท : อัพเดทสำเร็จ";
                    sendLineNotify($line_notify_msg);
                    echo '<script>alert("อัพเดทสำเร็จ และบันทึกไปยัง: ' . $uploadFilePath . '"); window.location.href="standardcost.php";</script>';
                } else {
                    echo '<script>alert("Failed to move uploaded file."); window.location.href="standardcost.php";</script>';
                }
            } else {
                echo '<script>alert("ไม่พบข้อมูล อัพเดทไม่สำเร็จ"); window.location.href="standardcost.php";</script>';
            }
        } else {
            echo '<script>alert("Invalid file type. Please upload an Excel file."); window.location.href="standardcost.php";</script>';
        }
    } else {
        echo '<script>alert("File upload error. Please try again."); window.location.href="standardcost.php";</script>';
    }
} else {
    echo '<script>alert("No file uploaded or submit button not clicked."); window.location.href="standardcost.php";</script>';
}
?>
