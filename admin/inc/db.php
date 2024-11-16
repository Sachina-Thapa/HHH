<?php
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "hhh";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consolidated filteration function
if (!function_exists('filteration')) {
    function filteration($data) {
        foreach ($data as $key => $value) {
            $data[$key] = trim($value);
            $data[$key] = stripslashes($data[$key]);
            $data[$key] = htmlspecialchars($data[$key]);
            $data[$key] = strip_tags($data[$key]);
        }
        return $data;
    }
}

if (!function_exists('selectAll')) {
function selectAll($table) {
    global $conn; // Use global variable
    $res = mysqli_query($conn, "SELECT * FROM $table");
    if (!$res) {
        die("Query failed: " . mysqli_error($conn));
    }
    return $res;
}
}

if (!function_exists('select')) {
function select($sql, $values, $datatypes) {
    global $conn; // Use global variable
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
        if (mysqli_stmt_execute($stmt)) {
            $res = mysqli_stmt_get_result($stmt);
            return $res;
        } else {
            mysqli_stmt_close($stmt);
            throw new Exception("Query cannot be executed - Select: " . mysqli_error($conn));
        }
    } else {
        throw new Exception("Query cannot be prepared - Select: " . mysqli_error($conn));
    }
}
}

if (!function_exists('update')) {
function update($sql, $values, $datatypes) {
    global $conn; // Use global variable
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
        if (mysqli_stmt_execute($stmt)) {
            $res = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $res;
        } else {
            mysqli_stmt_close($stmt);
            throw new Exception("Query cannot be executed - Update: " . mysqli_error($conn));
        }
    } else {
        throw new Exception("Query cannot be prepared - Update: " . mysqli_error($conn));
    }
}
}

if (!function_exists('insert')) {
function insert($sql, $values, $datatypes) {
    global $conn; // Use global variable
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
        if (mysqli_stmt_execute($stmt)) {
            $res = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $res;
        } else {
            mysqli_stmt_close($stmt);
            throw new Exception("Query cannot be executed - Insert: " . mysqli_error($conn));
        }
    } else {
        throw new Exception("Query cannot be prepared - Insert: " . mysqli_error($conn));
    }
}
}

// Close the connection when done
// $conn->close(); // Uncomment this line when you are done with database operations
?>