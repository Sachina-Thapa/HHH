<?php
$conn = new mysqli("localhost", "root", "", "hhh");

if(isset($_POST['id']) && isset($_POST['action'])) {
    $id = $_POST['id'];
    
    if($_POST['action'] == 'accept') {
        $sql = "UPDATE hostelers SET status = 1 WHERE id = ?";
    } else {
        $sql = "DELETE FROM hostelers WHERE id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
