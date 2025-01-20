<?php
require('../inc/db.php');

if (isset($_POST['room_number'])) {
    $room_number = $_POST['room_number'];
    $check_sql = "SELECT * FROM room WHERE rno = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $room_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "This room already exists.";
    } else {
        echo "Room number is available.";
    }

    $check_stmt->close();
}
?>