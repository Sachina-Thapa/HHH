<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triple Bed</title>
    <!-- Bootstrap 5 -->
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

        /* Hide the summary section and voucher upload initially */
        #bookingSummary,
        #voucherUploadSection,
        #editButton {
            display: none;
        }

        /* Error message styling */
        .error {
            color: red;
            font-size: 0.9em;
        }

        /* Edit Button CSS */
        #editButton {
            position: absolute;
            right: 20px;
            top: 15px;
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
            color: #007bff;
        }
    </style>
</head>

<body>
    <!-- Header with Logo and Profile -->
    <header class="header">
        <div class="container-fluid position-relative">
            <div class="logo">
                <a href="overview.php">
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

    <div class="container my-5 p-4 main-content">
        <div class="card shadow">
            <div class="card-header text-white text-center position-relative">
                <h2 class="card-title fw-bold">Her Hostel Booking</h2>
                <button id="editButton" class="btn btn-outline-light btn-sm">Edit</button>
            </div>
            <form id="bookingForm" novalidate>
                <div class="card-body">
                    <!-- Personal Info -->
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

                    <!-- Dates Selection -->
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

                    <!-- Room Type -->
                    <div class="mb-3">
                        <label class="form-label text-dark">Room Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="roomType" id="singleRoom" value="1000" checked>
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

                    <!-- Food Options -->
                    <div class="mb-3">
                        <label class="form-label text-dark">Food Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="breakfast" value="10">
                            <label class="form-check-label" for="breakfast">Breakfast ($10/day)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lunch" value="15">
                            <label class="form-check-label" for="lunch">Lunch ($15/day)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dinner" value="20">
                            <label class="form-check-label" for="dinner">Dinner ($20/day)</label>
                        </div>
                    </div>
                    <hr>

                    <!-- Laundry Service -->
                    <div class="mb-3">
                        <label class="form-label text-dark">Laundry Service</label>
                        <div id="laundryOptions"></div>
                    </div>

                    <!-- Other Services -->
                    <div class="mb-3">
                        <label for="otherServices" class="form-label text-dark">Other Services (Optional)</label>
                        <input type="text" class="form-control" id="otherServices" placeholder="Specify any additional services">
                    </div>
                </div>

                <!-- Total Price Display -->
                <div class="card-footer gradient-bg text-dark d-flex justify-content-between align-items-center p-3">
                    <div class="price-label">
                        Total Price: $<span id="totalPrice">0</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Book Now</button>
                </div>
            </form>
        </div>

        <!-- Booking Summary Section -->
        <div id="bookingSummary" class="card shadow mt-4">
            <div class="card-header text-center bg-secondary text-white">
                <h3 class="card-title">Booking Summary</h3>
            </div>
            <div class="card-body">
                <p><strong>First Name:</strong> <span id="summaryFirstName"></span></p>
                <p><strong>Last Name:</strong> <span id="summaryLastName"></span></p>
                <p><strong>Address:</strong> <span id="summaryAddress"></span></p>
                <p><strong>Phone Number:</strong> <span id="summaryPhoneNumber"></span></p>
                <p><strong>Check-in Date:</strong> <span id="summaryCheckIn"></span></p>
                <p><strong>Check-out Date:</strong> <span id="summaryCheckOut"></span></p>
                <p><strong>Room Type:</strong> <span id="summaryRoomType"></span></p>
                <p><strong>Food Options:</strong> <span id="summaryFoodOptions"></span></p>
                <p><strong>Laundry Service:</strong> <span id="summaryLaundryService"></span></p>
                <p><strong>Other Services:</strong> <span id="summaryOtherServices"></span></p>
                <p><strong>Total Price:</strong> $<span id="summaryTotalPrice"></span></p>
                <p><strong>Status:</strong> <span id="bookingStatus" class="badge bg-warning text-dark">Pending</span></p>
                <p><strong>Voucher Status:</strong> <span id="voucherStatus" class="badge bg-warning text-dark">Pending</span></p>
                <div id="voucherImageContainer"></div>
                <button id="acceptButton" class="btn btn-success mt-3">Accept Booking</button>
            </div>
        </div>

        <!-- Voucher Upload Section -->
        <div id="voucherUploadSection" class="card shadow mt-4">
            <div class="card-header text-center bg-secondary text-white">
                <h3 class="card-title">Upload Payment Voucher</h3>
            </div>
            <div class="card-body">
                <form id="voucherForm">
                    <div class="mb-3">
                        <label for="voucherFile" class="form-label">Choose Voucher File</label>
                        <input type="file" class="form-control" id="voucherFile" accept="image/*" required>
                        <div class="error" id="voucherError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Voucher</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer  class="mt-5">
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
        function updateLaundryOptions(days) {
            const laundryOptionsContainer = document.getElementById('laundryOptions');
            laundryOptionsContainer.innerHTML = '';
            let laundryOptionsHtml = '';
            if (days < 7) {
                laundryOptionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="laundryService" id="dailyLaundry" value="10">
                        <label class="form-check-label" for="dailyLaundry">Daily ($10/day)</label>
                    </div>
                `;
            } else if (days === 7) {
                laundryOptionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="laundryService" id="weeklyLaundry" value="50">
                        <label class="form-check-label" for="weeklyLaundry">Weekly ($50)</label>
                    </div>
                `;
            } else {
                laundryOptionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="laundryService" id="monthlyLaundry" value="150">
                        <label class="form-check-label" for="monthlyLaundry">Monthly ($150)</label>
                    </div>
                `;
            }
            laundryOptionsContainer.innerHTML = laundryOptionsHtml;
        }

        function calculateTotalPrice() {
            const checkIn = new Date(document.getElementById('checkIn').value);
            const checkOut = new Date(document.getElementById('checkOut').value);
            const days = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)) + 1;
            if (days <= 0 || isNaN(days)) {
                document.getElementById('totalPrice').innerText = 0;
                return;
            }
            updateLaundryOptions(days);
            const roomType = document.querySelector('input[name="roomType"]:checked').value;
            const roomTotal = roomType * days;
            const foodOptions = ['breakfast', 'lunch', 'dinner'];
            const foodTotal = foodOptions.reduce((total, optionId) => {
                const option = document.getElementById(optionId);
                return option.checked ? total + (Number(option.value) * days) : total;
            }, 0);
            const laundryOption = document.querySelector('input[name="laundryService"]:checked');
            const laundryTotal = laundryOption ? (laundryOption.id === 'dailyLaundry' ? laundryOption.value * days : laundryOption.value) : 0;
            const totalPrice = roomTotal + foodTotal + Number(laundryTotal);
            document.getElementById('totalPrice').innerText = totalPrice;
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

        function getSelectedFoodOptions() {
            const options = [];
            document.querySelectorAll('#breakfast, #lunch, #dinner').forEach(option => {
                if (option.checked) options.push(option.nextElementSibling.textContent.trim());
            });
            return options.length > 0 ? options.join(', ') : 'None';
        }

        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            if (validateForm()) {
                document.getElementById('bookingForm').style.display = 'none';
                document.getElementById('summaryFirstName').innerText = document.getElementById('firstName').value;
                document.getElementById('summaryLastName').innerText = document.getElementById('lastName').value;
                document.getElementById('summaryAddress').innerText = document.getElementById('address').value;
                document.getElementById('summaryPhoneNumber').innerText = document.getElementById('phoneNumber').value;
                document.getElementById('summaryCheckIn').innerText = document.getElementById('checkIn').value;
                document.getElementById('summaryCheckOut').innerText = document.getElementById('checkOut').value;
                document.getElementById('summaryRoomType').innerText = document.querySelector('input[name="roomType"]:checked').nextElementSibling.innerText;
                document.getElementById('summaryFoodOptions').innerText = getSelectedFoodOptions();
                document.getElementById('summaryLaundryService').innerText = document.querySelector('input[name="laundryService"]:checked')?.nextElementSibling.innerText || 'None';
                document.getElementById('summaryOtherServices').innerText = document.getElementById('otherServices').value || 'None';
                document.getElementById('summaryTotalPrice').innerText = document.getElementById('totalPrice').innerText;
                document.getElementById('bookingSummary').style.display = 'block';
                document.getElementById('editButton').style.display = 'block';
            }
        });

        document.getElementById('editButton').addEventListener('click', function () {
            document.getElementById('bookingForm').style.display = 'block';
            document.getElementById('bookingSummary').style.display = 'none';

            // Enable only specific fields for editing
            document.getElementById('firstName').disabled = false;
            document.getElementById('lastName').disabled = false;
            document.getElementById('address').disabled = false;
            document.getElementById('phoneNumber').disabled = false;

            // Disable all other fields
            document.getElementById('checkIn').disabled = true;
            document.getElementById('checkOut').disabled = true;
            document.querySelectorAll('input[name="roomType"]').forEach(el => el.disabled = true);
            document.getElementById('breakfast').disabled = true;
            document.getElementById('lunch').disabled = true;
            document.getElementById('dinner').disabled = true;
            document.querySelectorAll('input[name="laundryService"]').forEach(el => el.disabled = true);
            document.getElementById('otherServices').disabled = true;
        });

        document.getElementById('acceptButton').addEventListener('click', function () {
            document.getElementById('bookingStatus').innerText = 'Accepted';
            document.getElementById('voucherUploadSection').style.display = 'block';
            document.getElementById('bookingStatus').className = 'badge bg-success text-white';
        });

        document.getElementById('voucherForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const voucherFile = document.getElementById('voucherFile').files[0];
            if (voucherFile) {
                document.getElementById('voucherError').textContent = '';

                // Display uploaded voucher as an image
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.alt = "Voucher Image";
                    img.classList.add("img-thumbnail", "mt-3");
                    img.style.maxWidth = "200px";
                    document.getElementById('voucherImageContainer').innerHTML = '<strong>Voucher Image:</strong><br>';
                    document.getElementById('voucherImageContainer').appendChild(img);
                };
                reader.readAsDataURL(voucherFile);

                // Update voucher status to "Pending" after submission
                document.getElementById('voucherStatus').innerText = 'Pending';
                document.getElementById('voucherStatus').className = 'badge bg-warning text-dark';

                // Hide voucher upload section
                document.getElementById('voucherUploadSection').style.display = 'none';
            } else {
                document.getElementById('voucherError').textContent = 'Please upload a voucher file.';
            }
        });

        document.getElementById('checkIn').addEventListener('change', calculateTotalPrice);
        document.getElementById('checkOut').addEventListener('change', calculateTotalPrice);
        document.querySelectorAll('input[name="roomType"], #breakfast, #lunch, #dinner').forEach(element => {
            element.addEventListener('change', calculateTotalPrice);
        });
    </script>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>