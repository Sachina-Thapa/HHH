<?php
require_once 'config.php';

$email = $_POST['email'];
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode(['exists' => $result->num_rows > 0]);
?>
