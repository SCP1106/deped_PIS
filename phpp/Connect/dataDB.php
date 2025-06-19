<?php

$conn = mysqli_connect("rimsdone.com", "rimsdone_SCP1106", "_#vtR5e4]Rj9q%Sz", "rimsdone_depedPIS");
//$conn = mysqli_connect("localhost", "root", "", "jsonmapdata");

$conn->set_charset("utf8mb4");


ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>