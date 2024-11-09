<?php
// db.php
$host = 'localhost';
$db = 'hhh'; // database name
$user = 'root'; // database user
$pass = ''; // database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
