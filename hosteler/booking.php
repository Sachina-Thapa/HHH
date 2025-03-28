<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Include your database connection file
require('inc/db.php');
require('inc/hsidemenu.php');

// Handle check-in/check-out form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_dates'])) {
    $booking_id = $_POST['booking_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $number_of_days = $_POST['number_of_days'];

    $update_sql = "UPDATE booking SET 
                   check_in = ?, 
                   check_out = ?, 
                   number_of_days = ? 
                   WHERE bid = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssii", $check_in, $check_out, $number_of_days, $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Stay schedule updated successfully!";
        header("Location: hostelerdash.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update stay schedule.";
    }
}

if (!isset($_SESSION['username'])) {
    echo "<div class='alert alert-danger'>Please log in first.</div>";
    exit();
}

// Get room details from GET parameters
$selected_room_no = $_GET['room_no'] ?? null;
$selected_room_type = $_GET['room_type'] ?? null;
$selected_room_price = $_GET['room_price'] ?? null;

$username = $_SESSION['username'];

// Fetch user information
function getUserInfoByUsername($conn, $username) {
    $sql = "SELECT id, name, email, phone_number, address, date_of_birth, username FROM hostelers WHERE username = ?";
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
// $picture_path = $userInfo['picture_path'];

// Check if user has a confirmed booking
$check_booking_sql = "
    SELECT b.*, r.rtype, r.rprice 
    FROM booking b 
    JOIN room r ON b.rno = r.rno 
    WHERE b.id = ? AND b.bstatus = 'confirmed'
    ORDER BY b.bookingdate DESC 
    LIMIT 1";

$stmt = $conn->prepare($check_booking_sql);
$stmt->bind_param("i", $hosteler_id);
$stmt->execute();
$booking_result = $stmt->get_result();
$confirmed_booking = $booking_result->fetch_assoc();

// Only fetch available rooms if no confirmed booking
$rooms = [];
if (!$confirmed_booking) {
    $room_sql = "
        SELECT r.rno, r.rtype, r.rprice 
        FROM room r 
        LEFT JOIN booking b ON r.rno = b.rno AND b.bstatus = 'pending'
        WHERE b.rno IS NULL";
    if ($selected_room_no) {
        $room_sql .= " OR r.rno = " . intval($selected_room_no);
    }

    $room_result = $conn->query($room_sql);
    if (!$room_result) {
        die("Query failed: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/booking.css">
    <style>
        .confirmed-room-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border: 2px solid #28a745;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .check-in-out-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .text-purple {
            color: #6c5ce7;
        }
        .service-highlight {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .service-highlight:hover {
            background: #e8e9ea;
            transform: translateY(-2px);
        }
        .service-highlight i {
            font-size: 24px;
            margin-bottom: 8px;
            display: block;
        }
        .service-highlight span {
            font-size: 0.9rem;
            color: #4a4a4a;
        }
        .service-preview {
            padding-top: 15px;
            border-top: 1px solid rgba(108, 92, 231, 0.2);
        }
        .btn-primary {
            background-color: #6c5ce7;
            border-color: #6c5ce7;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5b4bc4;
            border-color: #5b4bc4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2);
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="booking-container">
            <h1><i class="fas fa-hotel"></i> Room Booking</h1>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']); ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error_message']); ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="booking-box">
                <h5><i class="fas fa-user-circle"></i> User Information</h5>
                <div class="user-info-grid">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= htmlspecialchars($name); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= htmlspecialchars($email); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?= htmlspecialchars($phone_number); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?= htmlspecialchars($address); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-birthday-cake"></i>
                        <span class="info-label">DOB:</span>
                        <span class="info-value"><?= htmlspecialchars($date_of_birth); ?></span>
                    </div>
                </div>
            </div>

            <?php if ($confirmed_booking): ?>
            <!-- Display Confirmed Room Details -->
            <div class="confirmed-room-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-check-circle text-success"></i> Your Confirmed Room</h5>
                    <span class="status-badge status-confirmed">
                        <i class="fas fa-check"></i> Confirmed
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Room Number:</strong> <?= htmlspecialchars($confirmed_booking['rno']); ?></p>
                        <p><strong>Room Type:</strong> <?= htmlspecialchars($confirmed_booking['rtype']); ?></p>
                        <p><strong>Room Price:</strong> ₹<?= number_format($confirmed_booking['rprice'], 2); ?>/night</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Booking Date:</strong> <?= date('d M Y', strtotime($confirmed_booking['bookingdate'])); ?></p>
                        <p><strong>Arrival Time:</strong> 
                            <?php 
                            $arrivalTimes = [
                                1 => 'Morning (6 AM - 12 PM)',
                                2 => 'Afternoon (12 PM - 4 PM)',
                                3 => 'Evening (4 PM - 8 PM)',
                                4 => 'Night (8 PM - 11 PM)'
                            ];
                            echo $arrivalTimes[$confirmed_booking['arrival']] ?? 'Not specified';
                            ?>
                        </p>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="hostelerdash.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if (!$confirmed_booking['check_in']): ?>
            <!-- Check-in/Check-out Form -->
            <div class="confirmed-room-box mt-4">
                <h5 class="mb-4"><i class="fas fa-calendar-check"></i> Schedule Your Stay</h5>
                <form id="checkInOutForm" method="POST" onsubmit="return validateDates(event);">
                    <input type="hidden" name="update_dates" value="1">
                    <input type="hidden" name="booking_id" value="<?= $confirmed_booking['bid']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="check_in" class="form-label">
                                    <i class="fas fa-calendar-plus"></i> Check-in Date
                                </label>
                                <input type="date" id="check_in" name="check_in" class="form-control" required 
                                       min="<?= date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="check_out" class="form-label">
                                    <i class="fas fa-calendar-minus"></i> Check-out Date
                                </label>
                                <input type="date" id="check_out" name="check_out" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Number of Days
                                </label>
                                <input type="number" id="number_of_days" name="number_of_days" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-money-bill"></i> Total Amount
                                </label>
                                <input type="text" id="total_amount" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="button-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> Confirm Schedule
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <!-- Display scheduled stay details -->
            <div class="confirmed-room-box mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-check text-primary"></i> Your Scheduled Stay</h5>
                    <span class="status-badge">
                        <i class="fas fa-calendar"></i> Scheduled
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            <span class="info-label">Check-in Date:</span>
                            <span class="info-value"><?= date('d M Y', strtotime($confirmed_booking['check_in'])); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-minus text-primary"></i>
                            <span class="info-label">Check-out Date:</span>
                            <span class="info-value"><?= date('d M Y', strtotime($confirmed_booking['check_out'])); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <i class="fas fa-clock text-primary"></i>
                            <span class="info-label">Number of Days:</span>
                            <span class="info-value"><?= $confirmed_booking['number_of_days']; ?> days</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-money-bill text-primary"></i>
                            <span class="info-label">Total Amount:</span>
                            <span class="info-value">Rs <?= number_format($confirmed_booking['number_of_days'] * $confirmed_booking['rprice'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- New Services Offer Section -->
                <div class="confirmed-room-box mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h5 class="mb-0">
                                Want to Make Your Stay More Comfortable?
                            </h5>
                            <p class="text-muted mt-2 mb-0">
                                Explore our premium services tailored for your comfort
                            </p>
                        </div>
                        <div class="ms-3">
                            <a href="http://localhost/HHH/hosteler/services.php" 
                               class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>
                                Explore Services
                            </a>
                        </div>
                    </div>
                   
                </div>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <!-- Room Selection Form -->
            <div class="room-selection-box" id="roomSelection">
                <h5><i class="fas fa-bed"></i> Select Available Room</h5>
                <form id="bookingForm" action="ajax/booking_process.php" method="POST" onsubmit="return validateAndSubmit(event);">
                    <div class="form-group">
                        <label for="room_type" class="form-label">
                            <i class="fas fa-door-open"></i> Choose Room
                        </label>
                        <select name="rno" id="room_type" class="form-select" required>
                            <?php if ($room_result && $room_result->num_rows > 0): ?>
                                <?php while ($row = $room_result->fetch_assoc()): ?>
                                    <option value="<?= $row['rno']; ?>" 
                                            <?= ($selected_room_no == $row['rno']) ? 'selected' : ''; ?>>
                                        Room <?= htmlspecialchars($row['rno']); ?> - 
                                        <?= htmlspecialchars($row['rtype']); ?> - 
                                        Rs <?= number_format($row['rprice']); ?>/night
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option disabled>No available rooms</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="arrival" class="form-label">
                            <i class="fas fa-clock"></i> Expected Arrival Time
                        </label>
                        <select id="arrival" name="arrival" class="form-control" required>
                            <option value="1">Morning (6 AM - 12 PM)</option>
                            <option value="2">Afternoon (12 PM - 4 PM)</option>
                            <option value="3">Evening (4 PM - 8 PM)</option>
                            <option value="4">Night (8 PM - 11 PM)</option>
                        </select>
                    </div>

                    <div class="button-group">
                        <button id="bookNowButton" type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Book Now
                        </button>
                        <button type="button" id="cancelButton" class="btn btn-secondary">
                            <i class="fas fa-times-circle"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize elements
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        const numberOfDaysInput = document.getElementById('number_of_days');
        const totalAmountInput = document.getElementById('total_amount');
        const cancelButton = document.getElementById('cancelButton');
        const roomPrice = <?= $confirmed_booking['rprice'] ?? 0 ?>;

        // Calculate days and total amount when dates change
        function updateDates() {
            if (checkInInput?.value && checkOutInput?.value) {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                const diffTime = Math.abs(checkOut - checkIn);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    numberOfDaysInput.value = diffDays;
                    totalAmountInput.value = 'Rs' + (diffDays * roomPrice).toLocaleString();
                } else {
                    numberOfDaysInput.value = '';
                    totalAmountInput.value = '';
                }
            }
        }

        // Set min dates and add event listeners for date inputs
        if (checkInInput && checkOutInput) {
            const today = new Date().toISOString().split('T')[0];
            checkInInput.min = today;
            
            checkInInput.addEventListener('change', function() {
                checkOutInput.min = checkInInput.value;
                if (checkOutInput.value && checkOutInput.value < checkInInput.value) {
                    checkOutInput.value = '';
                }
                updateDates();
            });

            checkOutInput.addEventListener('change', updateDates);
        }

        // Handle cancel button
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                window.location.href = 'hostelerdash.php';
            });
        }
    });

    // Validate dates before form submission
    function validateDates(event) {
        const checkIn = new Date(document.getElementById('check_in').value);
        const checkOut = new Date(document.getElementById('check_out').value);
        
        if (checkOut <= checkIn) {
            alert('Check-out date must be after check-in date');
            return false;
        }

        const diffTime = Math.abs(checkOut - checkIn);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 15) {
            alert('You cannot book for less than 15 days');
            return false;
        }

        return true;
    }

    // Handle room booking form submission
    function validateAndSubmit(event) {
        event.preventDefault();
        
        const roomNo = document.getElementById('room_type')?.value;
        const arrival = document.getElementById('arrival')?.value;
        
        if (!roomNo) {
            alert('Please select a room');
            return false;
        }
        
        if (!arrival) {
            alert('Please select an arrival time');
            return false;
        }

        const form = document.getElementById('bookingForm');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'hostelerdash.php';
            } else {
                alert(data.message || 'Booking failed. Please try again.');
                window.location.href = 'booking.php';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });

        return false;
    }
    </script>
</body>
</html>