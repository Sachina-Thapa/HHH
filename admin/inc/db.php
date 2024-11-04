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
function filteration($data) {
    foreach ($data as $key => $value) {
        $data[$key] = trim($value);
        $data[$key] = stripslashes($data[$key]);
        $data[$key] = htmlspecialchars($data[$key]);
        $data[$key] = strip_tags($data[$key]);
    }
    return $data;
}

        function selectall($table) {
            $con = $GLOBALS['conn'];
            $res = mysqli_query($con, "SELECT * FROM $table");
            return $res;
        }

        function select($sql, $values, $datatypes) {
            $con = $GLOBALS['conn'];
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
                if (mysqli_stmt_execute($stmt)) {
                    $res = mysqli_stmt_get_result($stmt);
                    return $res;
                } else {
                    mysqli_stmt_close($stmt);
                    die("Query cannot be executed - Select");
                }
            } else {
                die("Query cannot be prepared - Select");
            }
        }
        function update($sql, $values, $datatypes) {
            $con = $GLOBALS['conn'];
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
                if (mysqli_stmt_execute($stmt)) {
                    $res = mysqli_stmt_affected_rows($stmt);
                    return $res;
                } else {
                    mysqli_stmt_close($stmt);
                    die("Query cannot be executed - Update");
                }
            } else {
                die("Query cannot be prepared - Update");
            }
        }
        function insert($sql, $values, $datatypes) {
            $con = $GLOBALS['conn'];
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
                if (mysqli_stmt_execute($stmt)) {
                    $res = mysqli_stmt_affected_rows($stmt);
                    return $res;
                } else {
                    mysqli_stmt_close($stmt);
                    die("Query cannot be executed - Insert");
                }
            } else {
                die("Query cannot be prepared - Insert");
            }
        }
?>
