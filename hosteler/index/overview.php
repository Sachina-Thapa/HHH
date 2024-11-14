<?php
require('../../admin/inc/db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krofile</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        color: #333;
        font-family: 'Segoe UI', Arial, sans-serif;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Header Section */
    .header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        z-index: 1000;
        padding: 10px 0;
    }

    .logo {
        position: absolute;
        top: 10px;
        left: 1rem;
        height: 40px;
    }

    .logo img {
        height: 100%;
        width: auto;
    }

    /* Profile Section */
    .profile-dropdown {
        position: absolute;
        top: 10px;
        right: 1rem;
        height: 40px;
    }
    .profile-dropdown .dropdown-menu {
        width: 250px;
    }
    .profile-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
    .dropdown-profile-image {
        width: 64px;
        height: 64px;
        object-fit: cover;
    }

    /* Stats Dashboard */
    .stats-dashboard {
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin: 1.5rem 0;
    }

    .stat-card {
        background: #111;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        flex: 1;
    }

    .stat-card .stat-title {
        font-size: 0.9rem;
        color: #fff;
        margin-top: 5px;
    }

    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff;
        margin-top: 5px;
    }

    .stat-card.available .stat-value {
        color: #4CAF50;
    }

    .stat-card.booked .stat-value {
        color: #f44336;
    }

    /* Room Cards */
    .card {
        background: #fff;
        color: #333;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin: 10px 0;
        font-size: 0.9rem;
        display: flex;
        flex-direction: column;
        height: 400px;
    }

    .card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .card-header {
        padding: 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        font-size: 1rem;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-content {
        padding: 10px;
        flex-grow: 1;
        overflow: hidden;
    }

    .card-footer {
        padding: 10px;
        text-align: center;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .book-now-btn {
        background-color: #333;
        color: #fff;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 20px;
        display: inline-block;
    }

    .book-now-btn:hover {
        background-color: #495057;
    }

    /* Filter Buttons */
    .btn-outline {
        background: transparent;
        border: 1px solid #333;
        color: #333;
        padding: 6px 12px;
        border-radius: 20px;
        margin: 0 5px;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-outline.active {
        background: #333;
        color: #fff;
    }

    /* Grid Layout */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    /* Hide cards by default for filtering */
    .card.hidden {
        display: none;
    }

    /* Footer Styles */
    footer {
        background-color: #333;
        color: #fff;
        padding: 2rem 0;
        margin-top: auto;
    }

    footer h5 {
        color: #fff;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    footer ul {
        list-style-type: none;
        padding: 0;
    }

    footer ul li {
        margin-bottom: 0.5rem;
    }

    footer ul li a {
        color: #fff;
        text-decoration: none;
    }

    footer ul li a:hover {
        text-decoration: underline;
    }

    .social-icons {
        font-size: 1.5rem;
    }

    .social-icons a {
        color: #fff;
        margin-right: 1rem;
    }

    .social-icons a:hover {
        color: #007bff;
    }

    /* Main content padding for fixed header */
    .main-content {
        padding-top: 60px;
    }
    </style>
</head>
<body>
    <!-- Header with Logo and Profile -->
    <header class="header">
        <div class="container-fluid position-relative">
            <div class="logo">
                <img src="../images/logoo.png?height=40&width=120" alt="The Hosteller Logo">
            </div>
            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <div class="dropdown">
                    <button class="btn btn-link p-0" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://i.pravatar.cc/80" alt="User Profile" class="rounded-circle profile-image">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-3 shadow" aria-labelledby="profileDropdown">
                        <li>
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://i.pravatar.cc/80" alt="User Profile" class="rounded-circle dropdown-profile-image me-3">
                                <div>
                                    <h6 class="mb-0">Jane Smith</h6>
                                    <small class="text-muted">Hosteler</small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <i class="bi bi-telephone me-2"></i>
                                +977 123 456 7890   
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <i class="bi bi-geo-alt me-2"></i>
                                Kathmandu, Nepal
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="#" id="logoutButton">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Log out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container main-content">
        <h1 class="text-center mb-3">The Hosteller</h1>

        <?php 
           
        // Query to count total room
        $sql = "SELECT COUNT(*) as total FROM room;";
        $result = $conn->query($sql);
        $total_room = 0; // Initialize the variable

        if ($result) {
            $row = $result->fetch_assoc();
            $total_room = $row['total']; // Get the total count
        } else {
            echo "Error in query: " . $conn->error; // Handle query error
        }
?>

        <!-- Stats Dashboard -->
        <div class="stats-dashboard">
            <div class="stat-card available">
            <h5>Available</h5>
            <h3><?php echo $total_room; ?></h3>
            </div>
            <div class="stat-card booked">
            <h5>Booked Room</h5>
            <h3><?php echo $total_room; ?></h3>
            </div>
            <div class="stat-card">
            <h5>Total Room</h5>
            <h3><?php echo $total_room; ?></h3>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="text-center mb-3">
            <button class="btn btn-outline active" data-filter="all">All Rooms</button>
            <button class="btn btn-outline" data-filter="single">Single</button>
            <button class="btn btn-outline" data-filter="double">Double</button>
            <button class="btn btn-outline" data-filter="triple">Triple</button>
        </div>

        <!-- Room Cards -->
        <div class="grid">
            <!-- Single Room Card -->
            <div class="card single">
                <img src="../images/single.jpg" alt="Single Room">
                <div class="card-header">Cozy Single</div>
                <div class="card-content">
                    <p>👥 Capacity: 1 person</p>
                    <p>💲 Price: $30/night</p>
                    <h6>Amenities:</h6>
                    <ul>
                        <li>Single bed</li>
                        <li>Work desk</li>
                        <li>Private bathroom</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="single.php" class="book-now-btn">Book Now</a>
                </div>
            </div>

            <!-- Double Room Card -->
            <div class="card double">
                <img src="../images/double.jpg" alt="Double Room">
                <div class="card-header">Comfortable Double</div>
                <div class="card-content">
                    <p>👥 Capacity: 2 people</p>
                    <p>💲 Price: $50/night</p>
                    <h6>Amenities:</h6>
                    <ul>
                        <li>Two single beds</li>
                        <li>Work desk</li>
                        <li>Private bathroom</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="double.php" class="book-now-btn">Book Now</a>
                </div>
            </div>

            <!-- Triple Room Card -->
            <div class="card triple">
                <img src="../images/3 bed.webp" alt="Triple Room">
                <div class="card-header">Spacious Triple</div>
                <div class="card-content">
                    <p>👥 Capacity: 3 people</p>
                    <p>💲 Price: $70/night</p>
                    <h6>Amenities:</h6>
                    <ul>
                        <li>Three single beds</li>
                        <li>Work desk</li>
                        <li>Private bathroom</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="triple.php" class="book-now-btn">Book Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>About The Hosteller</h5>
                    <p>The Hosteller provides comfortable and affordable accommodation for travelers from all around the world. Our goal is to create a home away from home for our guests.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Rooms</a></li>
                        <li><a href="#">Amenities</a></li>
                        <li><a href="#">Location</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact Information</h5>
                    <p>
                        <i class="bi bi-geo-alt-fill me-2"></i>123 Hostel Street, Kathmandu, Nepal<br>
                        <i class="bi bi-telephone-fill me-2"></i>+977 123 456 7890<br>
                        <i class="bi bi-envelope-fill me-2"></i>info@thehosteller.com
                    </p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-4">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; 2023 The Hosteller. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Counting Animation and Filtering -->
    <script>
        
        // Function to animate counting
        function animateCount(element, target) {
            let count = 0;
            const increment = Math.ceil(target / 100);

            function updateCount() {
                count += increment;
                if (count > target) count = target;
                element.innerText = count;
                if (count < target) {
                    requestAnimationFrame(updateCount);
                }
            }
            updateCount();
        }

        // Initialize the animation for each stat card on load
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".stat-value").forEach(stat => {
                const target = parseInt(stat.getAttribute("data-target"), 10);
                animateCount(stat, target);
            });

            // Filter functionality
            const buttons = document.querySelectorAll(".btn-outline");
            const cards = document.querySelectorAll(".card");

            buttons.forEach(button => {
                button.addEventListener("click", () => {
                    const filter = button.getAttribute("data-filter");

                    // Update button active state
                    buttons.forEach(btn => btn.classList.remove("active"));
                
                    button.classList.add("active");

                    // Show or hide cards based on filter
                    cards.forEach(card => {
                        if (filter === "all" || card.classList.contains(filter)) {
                            card.classList.remove("hidden");
                        } else {
                            card.classList.add("hidden");
                        }
                    });
                });
            });
        });
    </script>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>