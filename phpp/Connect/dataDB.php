<?php

//$conn = mysqli_connect("localhost", "rimsdone_SCP1106", "1j9#b#jfo)JS", "rimsdone_depedPIS");
$conn = mysqli_connect("localhost", "root", "", "jsonmapdata");

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>