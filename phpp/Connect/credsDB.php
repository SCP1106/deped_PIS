<?php
$conn = mysqli_connect("localhost", "root", "", "depedcreds");

if ($conn->connect_error) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>