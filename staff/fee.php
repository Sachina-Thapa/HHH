<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Fee Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 200px;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
    </style>
    <script>
        function fetchHostelerDetails() {
            const id = document.getElementById('idno').value.trim();

            if (id) {
                // AJAX call to fetch hosteler details
                fetch(`ajax/fee.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            // Populate fields with received data
                            document.getElementById('name').value = data.name || '';
                            document.getElementById('roomtype').value = data.room_type || '';
                            document.getElementById('roomno').value = data.room_no || '';
                            document.getElementById('checkIn').value = data.check_in || '';
                            document.getElementById('checkOut').value = data.check_out || '';
                            document.getElementById('days').value = calculateDays(data.check_in, data.check_out);
                            document.getElementById('roomPrice').value = data.room_price || '';
                            document.getElementById('services').value = data.services || '';
                            document.getElementById('total').value = calculateTotal(data.room_price, document.getElementById('days').value);
                        } else {
                            alert('No details found for this ID.');
                        }
                    })
                    .catch(error => console.error('Error fetching details:', error));
            } else {
                alert('Please enter a valid ID.');
            }
        }

        function calculateDays(checkIn, checkOut) {
            const inDate = new Date(checkIn);
            const outDate = new Date(checkOut);
            if (!isNaN(inDate) && !isNaN(outDate)) {
                const diffTime = Math.abs(outDate - inDate);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
            }
            return '';
        }

        function calculateTotal(roomPrice, days) {
            if (roomPrice && days) {
                return parseFloat(roomPrice) * parseInt(days);
            }
            return '';
        }
    </script>
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>
    <div class="main-content">
        <div class="container-fluid p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h2>Hosteler Details</h2>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label for="id no" class="form-label">Hosteler ID</label>
                                    <input type="text" class="form-control" id="idno" onblur="fetchHostelerDetails()">
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="roomtype" class="form-label">Room Type</label>
                                    <input type="text" class="form-control" id="roomtype" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="roomno" class="form-label">Room No</label>
                                    <input type="text" class="form-control" id="roomno" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="checkIn" class="form-label">Check-In</label>
                                    <input type="text" class="form-control" id="checkIn" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="checkOut" class="form-label">Check-Out</label>
                                    <input type="text" class="form-control" id="checkOut" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="days" class="form-label">No. of Days</label>
                                    <input type="text" class="form-control" id="days" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="roomPrice" class="form-label">Room Price</label>
                                    <input type="text" class="form-control" id="roomPrice" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="services" class="form-label">Services</label>
                                    <input type="text" class="form-control" id="services" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total</label>
                                    <input type="text" class="form-control" id="total" readonly>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
