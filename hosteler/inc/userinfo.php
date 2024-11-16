<?php
require('../admin/inc/db.php'); // Ensure you have the correct database connection

function getUser_uinfo($conn, $username) 
{
    $sql = "SELECT id, name, email, phone_number, picture_path, address, date_of_birth, username, password FROM hostelers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result();
}
?>