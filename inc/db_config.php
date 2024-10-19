<?php
// db_functions.php

// Define your database connection
$conn = mysqli_connect("localhost", "root", "", "hhh");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Example of the update function
function update($query, $values, $types) {
    global $conn;

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, $types, ...$values);
    
    $exec = mysqli_stmt_execute($stmt);
    if ($exec === false) {
        die('MySQL execute error: ' . mysqli_error($conn));
    }

    return mysqli_stmt_affected_rows($stmt);
}

// You can add other functions here like insert, select, etc.
?>
