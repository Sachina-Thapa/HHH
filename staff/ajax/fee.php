<?php
// Include database connection
require('../../inc/db.php');

// Get the hosteler ID from the GET request
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Check if ID is provided
if ($id) {
    // Query to fetch the hosteler's latest booking details
    $query = $conn->prepare("
        SELECT 
            b.rid AS room_no,
            b.check_in,
            b.check_out,
            h.name,
            h.room_type,
            h.room_price, 
            h.services
        FROM bookings b
        JOIN hostelers h ON b.id = h.id
        WHERE h.id = ?
        ORDER BY b.bookingdate DESC
        LIMIT 1
    ");
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();

    // If a record is found, return it as JSON
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(null); // No record found
    }

    $query->close();
    $conn->close();
} else {
    echo json_encode(null); // Invalid ID
}