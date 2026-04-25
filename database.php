<?php

$host = "localhost";
$dbname = "cput_stays";
$username = "root";
$password = "#Chelsea4life";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

return $mysqli;