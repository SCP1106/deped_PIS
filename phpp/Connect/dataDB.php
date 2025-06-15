<?php

$conn = mysqli_connect("rimsdone.com", "rimsdone_SCP1106", "M$6-yK]u(#Q2", "rimsdone_depedPIS");
//$conn = mysqli_connect("localhost", "root", "", "jsonmapdata");

$conn->set_charset("utf8mb4");


ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>