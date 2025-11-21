<?php
// db_connect.php

function getDBConnection() {
    // -----------------------------------------------------------
    // FILL THESE WITH YOUR INFINITYFREE DETAILS
    // -----------------------------------------------------------
    $servername = "sql300.infinityfree.com";  // MySQL Hostname
    $username   = "if0_40476456";             // MySQL Username
    $password   = "4rlXi1hL7j";       // Your Hosting Password
    $dbname     = "if0_40476456_unidonate";   // Database Name
    // -----------------------------------------------------------

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        // This writes the error to a hidden server log instead of showing it to the user
        error_log("Connection failed: " . $conn->connect_error);
        return null;
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

function closeDBConnection() {
    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>