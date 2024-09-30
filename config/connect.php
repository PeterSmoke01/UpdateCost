<?php
$db_server = "192.168.11.252,52527";
$db_username = 'sa';
$db_password = 'Trcth1159p1';
$db_charset = 'UTF-8';

$connectionInfo = array(
    'Database' => 'Barcode_New',
    'UID' => $db_username,
    'PWD' => $db_password,
    'CharacterSet' => $db_charset
);

$conn = sqlsrv_connect($db_server, $connectionInfo);
if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
    exit;
}


// connect winspeed database 
$GLOBAL['conn_sca'] = sqlsrv_connect($db_server, array('Database' => 'dbwins_sca55', 'UID' => $db_username, 'PWD' => $db_password, 'CharacterSet' => $db_charset));
$GLOBAL['conn_sca10'] = sqlsrv_connect($db_server, array('Database' => 'dbwins_sca10', 'UID' => $db_username, 'PWD' => $db_password, 'CharacterSet' => $db_charset));
$GLOBAL['conn_sco'] = sqlsrv_connect($db_server, array('Database' => 'dbwins_sco55', 'UID' => $db_username, 'PWD' => $db_password, 'CharacterSet' => $db_charset));
$GLOBAL['conn_scorp'] = sqlsrv_connect($db_server, array('Database' => 'dbwins_scorp55', 'UID' => $db_username, 'PWD' => $db_password, 'CharacterSet' => $db_charset));
$GLOBAL['conn_wechill'] = sqlsrv_connect($db_server, array('Database' => 'dbwins_wcm', 'UID' => $db_username, 'PWD' => $db_password, 'CharacterSet' => $db_charset));
$GLOBAL['conn_test'] = sqlsrv_connect($db_server, array('Database' => 'dbwins_test', 'UID' => $db_username, 'PWD' => $db_password, 'CharacterSet' => $db_charset));

$ssp_conn = array(
	'user' => 'sa',
	'pass' => 'Trcth1159p1',
	'db'   => 'Barcode_New',
	'host' => '192.168.11.252,52527',
    'charset' => 'utf8'
);

?>