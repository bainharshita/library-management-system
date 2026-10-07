<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "library_management";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>