<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Include your database connection file
require('inc/db.php');
require('inc/hsidemenu.php'); // Assuming this includes your sidebar

if (!isset($_SESSION['username'])) {
    echo "<div class='alert alert-danger'>Please log in first.</div>";
    exit();
}

$username = $_SESSION['username'];

// Fetch user information
function getUserInfoByUsername($conn, $username) {
    $sql = "SELECT id, name, email, phone_number, picture_path, address, date_of_birth, username FROM hostelers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$userInfo = getUserInfoByUsername($conn, $username);
if (!$userInfo) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit();
}

$hosteler_id = $userInfo['id'];
$name = $userInfo['name'];
$email = $userInfo['email'];
$phone_number = $userInfo['phone_number'];
$address = $userInfo['address'];
$date_of_birth = $userInfo['date_of_birth'];
$picture_path = $userInfo['picture_path'];

// Fetch booking status
$booking_sql = "SELECT bstatus FROM booking WHERE id = ? ORDER BY bookingdate DESC LIMIT 1"; 
$stmt = $conn->prepare($booking_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $hosteler_id);
$stmt->execute();
$result = $stmt->get_result();
$bookingStatus = $result->fetch_assoc()['bstatus'] ?? null;

// Fetch available rooms
$room_sql = "
    SELECT r.rno, r.rtype, r.rprice 
    FROM room r 
    LEFT JOIN booking b ON r.rno = b.rno 
    WHERE b.rno IS NULL OR b.bstatus = 'declined'";

$room_result = $conn->query($room_sql);
if (!$room_result) {
    die("Query failed: " . $conn->error);
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/booking.css">
    <script src="scripts/booking.js" defer></script>
</head>
<body>
    <div class="content">
        <h1>Booking Form</h1>
        <div class="booking-box">
            <h5>User Information</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($name); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email); ?></p>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($phone_number); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($address); ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($date_of_birth); ?></p>
        </div>

        <div id="bookingStatusMessage" class="alert hidden" style="display: none;"></div>
        
        <div class="room-selection-box" id="roomSelection">
            <h5>Select Available Room</h5>
            <form id="bookingForm" action="ajax/booking_process.php" method="POST" onsubmit="submitBooking(event);">
                <div class="mb-3">
                    <label for="room_type" class="form-label">Choose Room</label>
                    <select name="room_type" id="room_type" class="form-select" required>
                        <?php if ($room_result->num_rows > 0): ?>
                            <?php while ($row = $room_result->fetch_assoc()): ?>
                                <option value="<?= $row['rno']; ?>" data-room-no="<?= $row['rno']; ?>" data-room-type="<?= $row['rtype']; ?>" data-room-price="<?= $row['rprice']; ?>">
                                    <?= htmlspecialchars($row['rno'] . ' - ' . $row['rtype'] . ' - ' . $row['rprice']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option disabled>No available rooms</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button id="bookNowButton" type="submit" class="btn btn-primary">Book Now</button>
                <button type="button" id="cancelButton" class="btn btn-secondary" onclick="cancelBooking();">Cancel</button>
            </form>
        </div>

        <div id="additionalFieldsContainer" style="display: none;">
            <h5>Additional Booking Information</h5>
            <div class="mb-3">
                <label for="check_in" class="form-label">Check In Date</label>
                <input type="date" id="check_in" name="check_in" class="form-control">
            </div>
            <div class="mb-3">
                <label for="check_out" class="form-label">Check Out Date</label>
                <input type="date" id="check_out" name="check_out" class="form-control">
            </div>
            <div class="mb-3">
                <label for="arrival" class="form-label">Arrival Time</label>
                <input type="time" id="arrival" name="arrival" class="form-control">
            </div>
            <div class="mb-3">
                <label for="number_of_days" class="form-label">Number of Days</label>
                <input type="number" id="number_of_days" name="number_of_days" class="form-control" min="1">
            </div>
        </div>
    </div>
</body>
</html>
