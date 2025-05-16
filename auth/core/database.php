<?php

/**
 * Database connection
 * 
 * This file establishes a connection to the MySQL database
 * and returns the connection object.
 */

$host = "rimsdone.com";
$dbname = "rimsdone_depedPIS";
$username = "rimsdone_SCP1106";
$password = "M$6-yK]u(#Q2";

try {
    $mysqli = new mysqli(
        hostname: $host,
        username: $username,
        password: $password,
        database: $dbname
    );

    if ($mysqli->connect_errno) {
        throw new Exception("Connection error: " . $mysqli->connect_error);
    }

    // Set charset to ensure proper encoding
    $mysqli->set_charset("utf8mb4");

    // Configure the connection for security
    $mysqli->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

    return $mysqli;
} catch (Exception $e) {
    // Log the error but don't expose details to users
    error_log($e->getMessage());
    die("A database error occurred. Please try again later.");
}
