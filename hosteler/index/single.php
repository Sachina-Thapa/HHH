<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triple Bed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .gradient-bg {
            background: linear-gradient(to right, #e0f7fa, #e0f2f1);
        }

        .price-label {
            font-weight: bold;
            color: #1e88e5;
        }

        .container,
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        .col-md-8 {
            margin-left: 0;
            padding-left: 1rem;
        }

        .my-5 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }

        .p-4 {
            padding: 1rem !important;
        }

        .card-header {
            background-color: #343a40;
        }

        /* Error message styling */
        .error {
            color: red;
            font-size: 0.9em;
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

        .profile-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        /* Main content padding for fixed header */
        .main-content {
            padding-top: 60px;
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
            color: #007bff }
    </style>
</head>

<body>
    <header class="header">
        <div class="container-fluid position-relative">
            <div class="logo">
                <a href="overview.php">
                <img src="../images/logoo.png?height=40&width=120" alt="The Hosteller Logo">
            </div>
            <div class="profile-dropdown">
                <div class="dropdown">
                    <button class="btn btn-link p-0" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://i.pravatar.cc/80" alt="User  Profile" class="rounded-circle profile-image">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-3 shadow" aria-labelledby="profileDropdown">
                        <li>
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://i.pravatar.cc/80" alt="User  Profile" class="rounded-circle dropdown-profile-image me-3">
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

    <div class="container my-5 p-4 main-content">
        <div class="card shadow">
            <div class="card-header text-white text-center position-relative">
                <h2 class="card-title fw-bold">Her Hostel Booking</h2>
            </div>
            <form id="bookingForm" novalidate>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="firstName" class="form-label text-dark">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                            <div class="error" id="firstNameError"></div>
                        </div>
                        <div class="col">
                            <label for="lastName" class="form-label text-dark">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                            <div class="error" id="lastNameError"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label text-dark">Address</label>
                        <input type="text" class="form-control" id="address" required>
                        <div class="error" id="addressError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label text-dark">Phone Number</label>
                        <input type="tel" class="form-control" id="phoneNumber" required>
                        <div class="error" id="phoneError"></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="checkIn" class="form-label text-dark">Check-in Date</label>
                            <input type="date" class="form-control" id="checkIn" required>
                            <div class="error" id="checkInError"></div>
                        </div>
                        <div class="col">
                            <label for="checkOut" class="form-label text-dark">Check-out Date</label>
                            <input type="date" class="form-control" id="checkOut" required>
                            <div class="error" id="checkOutError"></div>
                        </div>
                    </div>
                    <hr>

                    <div class="mb-3">
                        <label class="form-label text-dark">Room Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio name="roomType" id="singleRoom" value="1000" checked>
                            <label class="form-check-label" for="singleRoom">Single ($1000/day)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="roomType" id="doubleRoom" value="1200">
                            <label class="form-check-label" for="doubleRoom">Double ($1200/day)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="roomType" id="tripleRoom" value="1500">
                            <label class="form-check-label" for="tripleRoom">Triple ($1500/day)</label>
                        </div>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-primary" onclick="bookNow()">Book Now</button>
                </div>
            </form>
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

        <script>
            function bookNow() {
                if (validateForm()) {
                    alert("Your Booking is Pending");
                    const userConfirmation = confirm("Would you like to go to the dashboard?");
                    if (userConfirmation) {
                        window.location.href = "/hhh/hosteler/hostelerdash.php";                    }
                }
            }

            function validateForm() {
                let isValid = true;
                document.querySelectorAll('.error').forEach(error => error.textContent = '');

                const firstName = document.getElementById('firstName').value.trim();
                const lastName = document.getElementById('lastName').value.trim();
                const address = document.getElementById('address').value.trim();
                const phoneNumber = document.getElementById('phoneNumber').value.trim();
                const checkInDate = new Date(document.getElementById('checkIn').value);
                const checkOutDate = new Date(document.getElementById('checkOut').value);

                if (!firstName) { isValid = false; document.getElementById('firstNameError').textContent = 'First name is required.'; }
                if (!lastName) { isValid = false; document.getElementById('lastNameError').textContent = 'Last name is required.'; }
                if (!address) { isValid = false; document.getElementById('addressError').textContent = 'Address is required.'; }
                if (!/^\d{10}$/.test(phoneNumber)) { isValid = false; document.getElementById('phoneError').textContent = 'Enter a valid 10-digit phone number.'; }
                if (!checkInDate || !checkOutDate || checkInDate > checkOutDate) {
                    isValid = false;
                    document.getElementById('checkInError').textContent = 'Check-in date must be on or before Check-out date.';
                    document.getElementById('checkOutError').textContent = 'Check-out date must be on or after Check-in date.';
                }

                return isValid;
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>