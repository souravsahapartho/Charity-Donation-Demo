<?php
// db_connect.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'UniDonateDB'); 

$mysqli = null;

function getDBConnection() {
    global $mysqli;
    if ($mysqli) {
        return $mysqli;
    }

    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($mysqli->connect_error) {
        error_log("Connection failed: " . $mysqli->connect_error);
        return false;
    }
    
    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}

function closeDBConnection() {
    global $mysqli;
    if ($mysqli) {
        $mysqli->close();
        $mysqli = null;
    }
}
?>