<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require('../admin/inc/db.php');
include('inc/hsidemenu.php');

// Check session
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$showRooms = true;

try {
    // Get user details
    $userQuery = "SELECT * FROM hostelers WHERE username = ?";
    $stmt = mysqli_prepare($conn, $userQuery);
    if (!$stmt) {
        throw new Exception("Error preparing user query: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $userResult = mysqli_stmt_get_result($stmt);
    $userDetails = mysqli_fetch_assoc($userResult);
    
    if (!$userDetails) {
        throw new Exception("User not found");
    }
    
    $name = $userDetails['name'] ?? "User not found";
    $userId = $userDetails['id'] ?? null;

    // Check payment status
    $paymentStatusQuery = "
        SELECT status 
        FROM fee 
        WHERE hid = ? 
        ORDER BY feeid DESC 
        LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $paymentStatusQuery);
    if (!$stmt) {
        throw new Exception("Error preparing payment status query: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $paymentResult = mysqli_stmt_get_result($stmt);
    $paymentStatus = mysqli_fetch_assoc($paymentResult);
    $lastPaymentStatus = $paymentStatus['status'] ?? null;

    // Check user's booking status
    $bookingStatusQuery = "
        SELECT bstatus 
        FROM booking 
        WHERE id = ? 
        ORDER BY bookingdate DESC 
        LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $bookingStatusQuery);
    if (!$stmt) {
        throw new Exception("Error preparing booking status query: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $statusResult = mysqli_stmt_get_result($stmt);
    $lastBooking = mysqli_fetch_assoc($statusResult);
    $lastStatus = $lastBooking['bstatus'] ?? null;

    // Determine if we should show available rooms
    $showRooms = ($lastStatus === null || $lastStatus === 'canceled' || $lastStatus === 'pending' || $lastStatus === 'completed');

    // Initialize filter variables
    $roomTypeFilter = isset($_GET['room_type']) ? mysqli_real_escape_string($conn, $_GET['room_type']) : '';
    $minPriceFilter = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $maxPriceFilter = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 100000;

    // Fetch all user's bookings with check_in and check_out dates
    $bookingsQuery = "
        SELECT 
            b.bid,
            b.rno,
            b.bookingdate,
            b.arrival,
            b.bstatus,
            b.check_in,
            b.check_out,
            r.rtype,
            r.rprice
        FROM booking b 
        JOIN hostelers h ON b.id = h.id 
        JOIN room r ON b.rno = r.rno 
        WHERE h.username = ?
        ORDER BY b.bookingdate DESC";

    $stmt = mysqli_prepare($conn, $bookingsQuery);
    if (!$stmt) {
        throw new Exception("Error preparing bookings query: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $bookingsResult = mysqli_stmt_get_result($stmt);
    $userBookings = [];

    while ($booking = mysqli_fetch_assoc($bookingsResult)) {
        $userBookings[] = [
            'bid' => $booking['bid'],
            'room_number' => $booking['rno'],
            'room_type' => $booking['rtype'],
            'room_price' => $booking['rprice'],
            'booking_date' => $booking['bookingdate'],
            'arrival_time' => $booking['arrival'],
            'bstatus' => $booking['bstatus'],
            'check_in' => $booking['check_in'],
            'check_out' => $booking['check_out']
        ];
    }



    // Fetch available rooms if needed
    $rooms = [];
    if ($showRooms) {
        $roomQuery = "
            SELECT r.rno, r.rtype, r.rprice 
            FROM room r 
            WHERE r.rno NOT IN (
                SELECT b.rno 
                FROM booking b 
                WHERE b.bstatus IN ('confirmed', 'pending')
            )";

        $params = [];
        $types = "";

        if ($roomTypeFilter) {
            $roomQuery .= " AND r.rtype = ?";
            $params[] = $roomTypeFilter;
            $types .= "s";
        }
        if ($minPriceFilter > 0) {
            $roomQuery .= " AND r.rprice >= ?";
            $params[] = $minPriceFilter;
            $types .= "d";
        }
        if ($maxPriceFilter < PHP_INT_MAX) {
            $roomQuery .= " AND r.rprice <= ?";
            $params[] = $maxPriceFilter;
            $types .= "d";
        }

        $stmt = mysqli_prepare($conn, $roomQuery);
        if (!$stmt) {
            throw new Exception("Error preparing rooms query: " . mysqli_error($conn));
        }

        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $roomResult = mysqli_stmt_get_result($stmt);
        
        while ($room = mysqli_fetch_assoc($roomResult)) {
            $rooms[] = [
                'number' => $room['rno'],
                'type' => $room['rtype'],
                'price' => number_format($room['rprice'], 2)
            ];
        }
    }

    // Close the database connection
    mysqli_close($conn);

} catch (Exception $e) {
    // Log the error and show a user-friendly message
    error_log("Error in hostelerdash.php: " . $e->getMessage());
    echo "<div class='alert alert-danger'>An error occurred. Please try again later or contact support.</div>";
    if (isset($conn)) {
        mysqli_close($conn);
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Panel - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --accent-color: #3b82f6;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            line-height: 1.5;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s ease;
            background-color: var(--light-bg);
            padding: 20px;
        }

        .content-wrapper {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            background: var(--card-bg);
            padding: 1.5rem 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-message {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .booking-cards {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .booking-card {
            background: var(--card-bg);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .booking-main-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
        }

        .room-icon {
            width: 52px;
            height: 52px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .booking-details {
            flex: 1;
            min-width: 0;
        }

        .booking-room-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.375rem;
        }

        .booking-meta {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
            flex-wrap: wrap;
        }

        .booking-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .booking-price {
            color: var(--primary-color);
            font-weight: 600;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .status-confirmed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .status-canceled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .join-action {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .join-message {
            color: #0369a1;
            font-size: 0.95rem;
            margin: 0;
        }

        .join-button {
            background-color: #0284c7;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s ease;
            white-space: nowrap;
        }

        .join-button:hover {
            background-color: #0369a1;
            color: white;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .room-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease;
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .content-wrapper {
                padding: 1rem;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Dashboard Header -->
            <div class="dashboard-header fade-in">
                <div class="welcome-message">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4>Welcome back, <?php echo htmlspecialchars($name); ?></h4>
                        <p class="text-secondary mb-0">
                            <?php if (!$showRooms): ?>
                                You have an active booking
                            <?php else: ?>
                                Explore available rooms for booking
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Bookings Section -->
           <section class="bookings-section fade-in">
    <h2 class="section-title">
        Your Bookings
    </h2>
    <?php if (!empty($userBookings)): ?>
        <div class="booking-cards">
            <?php foreach ($userBookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-main-info">
                        <div class="room-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="booking-details">
                            <div class="booking-room-title">
                                Room <?php echo htmlspecialchars($booking['room_number']); ?> - <?php echo htmlspecialchars($booking['room_type']); ?>
                            </div>
                            <div class="booking-meta">
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d M Y', strtotime($booking['booking_date'])); ?>
                                </span>
                                <span>
                                    <i class="fas fa-clock"></i>
                                    <?php 
                                        $arrivalTimes = [
                                            1 => 'Morning',
                                            2 => 'Afternoon',
                                            3 => 'Evening',
                                            4 => 'Night'
                                        ];
                                        echo $arrivalTimes[$booking['arrival_time']] ?? 'Not specified';
                                    ?>
                                </span>
                                <span class="booking-price">
                                    <i class="fas fa-rupee-sign"></i>
                                    <?php echo number_format($booking['room_price'], 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="status-badge status-<?php echo strtolower($booking['bstatus']); ?>">
                        <i class="fas <?php 
                            echo match($booking['bstatus']) {
                                'confirmed' => 'fa-check',
                                'pending' => 'fa-clock',
                                'canceled' => 'fa-times',
                                'completed' => 'fa-check-double',
                                default => 'fa-question'
                            };
                        ?>"></i>
                        <?php echo ucfirst($booking['bstatus']); ?>
                    </div>
                </div>
                <?php if ($booking['bstatus'] === 'confirmed'): ?>
                    <?php if (empty($booking['check_in']) && empty($booking['check_out'])): ?>
                        <div class="join-action">
                            <p class="join-message">
                                <i class="fas fa-info-circle"></i>
                                Ready to join the hostel? Click here to schedule your arrival
                            </p>
                            <a href="booking.php?action=join&bid=<?php echo htmlspecialchars($booking['bid']); ?>" class="join-button">
                                <i class="fas fa-sign-in-alt"></i>
                                Schedule Join Date
                            </a>
                        </div>
                    <?php elseif (!empty($booking['check_in']) && !empty($booking['check_out'])): ?>
                        <?php if ($lastPaymentStatus === 'confirmed'): ?>
    <?php
        $today = new DateTime();
        $checkOut = new DateTime($booking['check_out']);
        $remainingDays = $today > $checkOut ? 0 : $today->diff($checkOut)->days;
    ?>
    <div class="join-action" style="background-color: <?php echo $remainingDays === 0 ? '#fff3cd' : '#d4edda'; ?>; 
                                  border-color: <?php echo $remainingDays === 0 ? '#ffeeba' : '#c3e6cb'; ?>;">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div class="join-message" style="color: <?php echo $remainingDays === 0 ? '#856404' : '#155724'; ?>;">
            <?php if ($remainingDays === 0): ?>
                <p class="mb-1">
                    <i class="fas fa-exclamation-circle"></i>
                    Your stay period has ended
                </p>
                <p class="mb-0">
                    <i class="fas fa-calendar-alt"></i>
                    Check-out Date: <?php echo date('d M Y', strtotime($booking['check_out'])); ?>
                </p>
            <?php else: ?>
                <p class="mb-1">
                    <i class="fas fa-calendar-check"></i>
                    Check-in: <?php echo date('d M Y', strtotime($booking['check_in'])); ?>
                    | Check-out: <?php echo date('d M Y', strtotime($booking['check_out'])); ?>
                </p>
                <p class="mb-0">
                    <i class="fas fa-clock"></i>
                    Remaining Days: <?php echo $remainingDays; ?> days
                </p>
            <?php endif; ?>
        </div>
        <?php if ($remainingDays === 0): ?>
            <button type="button" class="btn btn-danger" onclick="processCheckout(<?php echo $booking['bid']; ?>)">
                <i class="fas fa-sign-out-alt"></i> Check Out
            </button>
        <?php endif; ?>
    </div>
</div>
                        <?php elseif ($lastPaymentStatus === 'canceled' || $lastPaymentStatus === null || $lastPaymentStatus === 'pending'): ?>
                            <div class="join-action" style="background-color: #fff3cd; border-color: #ffeeba;">
                                <p class="join-message" style="color: #856404;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Your payment is pending. Please proceed to complete your payment.
                                </p>
                                <a href="fee.php" class="join-button" style="background-color: #ffc107; color: #000;">
                                    <i class="fas fa-credit-card"></i>
                                    Pay Now
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-bookings fade-in">
            <h3>No Bookings Found</h3>
            <p class="text-secondary">Ready to start your journey? Book a room now!</p>
            <a href="booking.php" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-2"></i>Book a Room
            </a>
        </div>
    <?php endif; ?>
</section>

<!-- Extension Modal -->
<div class="modal fade" id="extendStayModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Extend Your Stay</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="extendStayForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="extend">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['bid']; ?>">
                        <input type="hidden" name="current_checkout" value="<?php echo $booking['check_out']; ?>">
                        <input type="hidden" name="room_price" value="<?php echo $booking['rprice']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Number of Days to Extend</label>
                            <input type="number" class="form-control" name="extension_days" 
                                   min="1" max="30" required>
                            <small class="text-muted">Maximum extension period is 30 days</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Check-out Date</label>
                            <input type="text" class="form-control" id="newCheckout" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Cost</label>
                            <input type="text" class="form-control" id="additionalCost" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Extension</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Checkout Confirmation Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to check out? This will:</p>
                    <ul>
                        <li>Mark your current stay as completed</li>
                        <li>Make the room available for others</li>
                        <li>Complete all current service requests</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="checkoutForm">
                        <input type="hidden" name="action" value="checkout">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['bid']; ?>">
                        <button type="submit" class="btn btn-danger">Confirm Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

            <?php if ($showRooms): ?>
            <!-- Available Rooms Section -->
            <section class="available-rooms-section fade-in">
                <h2 class="section-title">
                    Available Rooms
                </h2>

                <!-- Filter Section -->
                <div class="filter-section mb-4">
                    <form action="" method="GET" class="card p-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Room Type</label>
                                <select name="room_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="Single" <?php echo $roomTypeFilter === 'Single' ? 'selected' : ''; ?>>Single</option>
                                    <option value="Double" <?php echo $roomTypeFilter === 'Double' ? 'selected' : ''; ?>>Double</option>
                                    <option value="Triple" <?php echo $roomTypeFilter === 'Triple' ? 'selected' : ''; ?>>Triple</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Min Price</label>
                                <input type="number" name="min_price" class="form-control" value="<?php echo $minPriceFilter; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Max Price</label>
                                <input type="number" name="max_price" class="form-control" value="<?php echo $maxPriceFilter; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Available Rooms Grid -->
                <div class="rooms-grid">
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <div class="room-card fade-in">
                                <div class="room-header d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="room-title mb-0">
                                        Room <?php echo htmlspecialchars($room['number']); ?>
                                    </h5>
                                    <span class="badge bg-success">Available</span>
                                </div>
                                <div class="room-info">
                                    <p class="mb-2">
                                        <strong>Type:</strong> <?php echo htmlspecialchars($room['type']); ?>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Price:</strong> Rs <?php echo htmlspecialchars($room['price']); ?>/night
                                    </p>
                                    
                                    <div class="d-grid">
                                        <a href="booking.php?room_no=<?php echo htmlspecialchars($room['number']); ?>&room_type=<?php echo urlencode($room['type']); ?>&room_price=<?php echo urlencode($room['price']); ?>" 
                                           class="btn btn-primary">
                                            Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-rooms-message text-center p-5 fade-in">
                            <i class="fas fa-search fa-3x mb-3 text-secondary"></i>
                            <h3>No Rooms Available</h3>
                            <p class="text-secondary">No rooms match your current filters. Try adjusting your search criteria.</p>
                            <a href="hostelerdash.php" class="btn btn-primary mt-3">
                                <i class="fas fa-sync"></i> Reset Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

 <script>
   function processCheckout(bookingId) {
    if (confirm('Are you sure you want to check out? This will make the room available for others.')) {
        const formData = new FormData();
        formData.append('booking_id', bookingId);
        formData.append('confirm_early_checkout', 'yes');

        fetch('ajax/checkout_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'hostelerdash.php';
            } else {
                // Show error message
                alert(data.message || 'An error occurred during checkout');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during checkout. Please try again.');
        });
    }
}
    </script>
