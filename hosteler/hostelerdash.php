<?php 
session_start(); // Start session
require('../admin/inc/db.php');
include('inc/hsidemenu.php'); 

// Check if the session variable 'username' is set
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Retrieve the username from session
} else {
    $username = "Session not set"; // Default message if session is not set
}

// Initialize filter variables
$roomTypeFilter = isset($_GET['room_type']) ? mysqli_real_escape_string($conn, $_GET['room_type']) : '';
$minPriceFilter = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPriceFilter = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_INT_MAX;
$featuresFilter = isset($_GET['features']) ? mysqli_real_escape_string($conn, $_GET['features']) : '';

// Fetch available rooms with filtering
$roomQuery = "
    SELECT r.rno, r.rtype, r.rprice 
    FROM room r 
    WHERE r.rno NOT IN (SELECT b.rno FROM booking b)
";

// Add filters to the query
if ($roomTypeFilter) {
    $roomQuery .= " AND r.rtype = '$roomTypeFilter'";
}
if ($minPriceFilter > 0) {
    $roomQuery .= " AND r.rprice >= $minPriceFilter";
}
if ($maxPriceFilter < PHP_INT_MAX) {
    $roomQuery .= " AND r.rprice <= $maxPriceFilter";
}
if ($featuresFilter) {
    $featuresArray = explode(',', $featuresFilter);
    foreach ($featuresArray as $feature) {
        $feature = trim($feature);
        $roomQuery .= " AND r.rno IN (SELECT rno FROM room_features WHERE feature LIKE '%$feature%')";
    }
}

$roomResult = mysqli_query($conn, $roomQuery);
$rooms = [];

if ($roomResult && mysqli_num_rows($roomResult) > 0) {
    while ($room = mysqli_fetch_assoc($roomResult)) {
        $roomNumber = $room['rno'];
        $roomType = $room['rtype'];
        $roomPrice = number_format($room['rprice'], 2);
        $features = isset($roomFeatures[$roomNumber]) ? implode(", ", $roomFeatures[$roomNumber]) : "No features added";

        $rooms[] = [
            'number' => $roomNumber,
            'type' => $roomType,
            'price' => $roomPrice,
            'features' => $features
        ];
    }
}

// Fetch and display name if session is active and database connected
if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $q = mysqli_query($conn, "SELECT * FROM hostelers WHERE username='$user'");

    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_array($q);
        $name = isset($row['name']) ? $row['name'] : "User   not found"; // Ensure $name is set
    } else {
        $name = "User   not found"; // Default message if user is not found
    }
} else {
    $name = "Session not set"; // Default message if session is not set
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Panel - Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 210px; /* Adjust to the sidebar width */
            padding: 20px;
            flex-grow: 1;
            background-color: #f1f1f1;
        }
        .room-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 10px; /* Space between boxes */
            flex: 1; /* Allow boxes to grow equally */
        }
        .carousel-container {
            display: flex;
            align-items: center; /* Center items vertically */
            overflow: hidden;
            width: 100%;
        }
        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
        }
        .arrow-button {
            background: none;
            border: none;
            font-size: 24px; /* Adjust size as needed */
            cursor: pointer;
            margin: 0 10px; /* Space between arrows and rooms */
        }
        /* Custom styles for dropdown */
        .dropdown-submenu {
            position: relative;
        }
        .dropdown-submenu>.dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -6px;
            margin-left: -1px;
            display: none;
            z-index: 1000;
        }
        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="row">

        <!-- Main Content Column -->
        <div class="col-md-10 p-4">
            <h3 class="mt-3">Hosteler Panel</h3>

            <!-- Display Welcome Message -->
            <div class="alert alert-info mt-3">
                <?php echo "Welcome! " . htmlspecialchars($name); ?>
            </div>

            <!-- Filter Dropdowns -->
            <div class="dropdown mt-4">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter By
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li class="dropdown-submenu">
                        <a class="dropdown-item dropdown-toggle" href="#">Type</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?room_type=Single">Single</a></li>
                            <li><a class="dropdown-item" href="?room_type=Double">Double</a></li>
                            <li><a class="dropdown-item" href="?room_type=Triple">Triple</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a class="dropdown-item dropdown-toggle" href="#">Price</a>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="GET" class="px-2">
                                    <div class="mb-2">
                                        <input type="number" name="min_price" class="form-control" placeholder="Lowest Price" value="<?php echo htmlspecialchars($minPriceFilter); ?>">
                                    </div>
                                    <div class="mb-2">
                                        <input type="number" name="max_price" class="form-control" placeholder="Highest Price" value="<?php echo htmlspecialchars($maxPriceFilter); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Apply</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a class="dropdown-item dropdown-toggle" href="#">Features</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?features=WiFi">WiFi</a></li>
                            <li><a class="dropdown-item" href="?features=AC">AC</a></li>
                            <li><a class="dropdown-item" href="?features=TV">TV</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Available Rooms Section -->
            <h4 class="mt-4">Available Rooms</h4>
            <div class="carousel-container">
                <button class="arrow-button" id="prevBtn">&#60;</button>
                <div class="carousel-inner" id="carousel-inner">
                    <?php if (!empty($rooms)): ?>
                        <?php 
                        $totalRooms = count($rooms);
                        for ($i = 0; $i < $totalRooms; $i++): ?>
                            <div class="carousel-item" style="display: none;">
                                <div class="d-flex justify-content-around">
                                    <?php for ($j = 0; $j < 3; $j++): ?>
                                        <?php if (isset($rooms[($i + $j) % $totalRooms])): ?>
                                            <div class="room-box">
                                                <h5>Room Number: <?php echo $rooms[($i + $j) % $totalRooms]['number']; ?></h5>
                                                <p>Type: <?php echo $rooms[($i + $j) % $totalRooms]['type']; ?></p>
                                                <p>Price: ₹ <?php echo $rooms[($i + $j) % $totalRooms]['price']; ?></p>
                                                <p>Features: <?php echo $rooms[($i + $j) % $totalRooms]['features']; ?></p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="carousel-item active">
                            <div class="d-flex justify-content-center">
                                <div class="room-box">
                                    <p>No available rooms</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="arrow-button" id="nextBtn">&#62;</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (including Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
    const carouselInner = document.getElementById('carousel-inner');
    const totalRooms = <?php echo json_encode($totalRooms); ?>;
    let currentIndex = 0;

    function showRooms(index) {
        const items = carouselInner.children;
        for (let i = 0; i < items.length; i++) {
            items[i].style.display = 'none'; // Hide all items
        }
        items[index].style.display = 'flex'; // Show the current item
    }

    function nextRooms() {
        currentIndex = (currentIndex + 1) % totalRooms; // Move to the next index
        showRooms(currentIndex);
    }

    function prevRooms() {
        currentIndex = (currentIndex - 1 + totalRooms) % totalRooms; // Move to the previous index
        showRooms(currentIndex);
    }

    document.getElementById('nextBtn').addEventListener('click', nextRooms);
    document.getElementById('prevBtn').addEventListener('click', prevRooms);

    // Automatically shift rooms every 5 seconds
    setInterval(nextRooms, 5000);

    // Show the first set of rooms initially
    showRooms(currentIndex);
</script>
</body>
</html>