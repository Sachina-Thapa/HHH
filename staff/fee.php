<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee</title>
    <style>
        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 200px; /* Width of the sidebar */
            background-color: #343a40;
            padding-top: 20px;
            z-index: 1000; /* Ensures the sidebar stays above other content */
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

        /* Main content styling */
        .main-content {
            margin-left: 200px; /* Matches the width of the sidebar */
            padding: 20px;
        }

        /* Card styling inside the main content */
        .card {
            margin-bottom: 20px;
        }

        /* Invoice table styling */
        .table {
            width: 100%;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require('inc/sidemenu.php'); ?>
    <!-- Main Content -->
    <div class="main-content">
    <?php 
    require('inc/db.php'); ?>
        <div class="container-fluid p-4" id="main-content">
            <div class="row g-4">
                <!-- Left Side - Stay Duration Calculation -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h2 class="mb-0">Stay Duration</h2>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label for="id" class="form-label">ID No.</label>
                                    <input type="text" class="form-control" id="idNo">
                                </div>
                                <div class="mb-3">
                                    <label for="hostelername" class="form-label">Hosteler Name</label>
                                    <input type="text" class="form-control" id="hostelername">
                                </div>
                                <div class="mb-3">
                                    <label for="roomtype" class="form-label">Room Type</label>
                                    <input type="text" class="form-control" id="roomtype">
                                </div>
                                <div class="mb-3">
                                    <label for="roomno" class="form-label">Room No</label>
                                    <input type="text" class="form-control" id="roomno">
                                </div>
                                <div class="mb-3">
                                    <label for="checkIn" class="form-label">Check In</label>
                                    <input type="text" class="form-control" id="checkIn">
                                </div>
                                <div class="mb-3">
                                    <label for="checkOut" class="form-label">Check Out</label>
                                    <input type="text" class="form-control" id="checkOut">
                                </div>
                                <div class="mb-3">
                                    <label for="days" class="form-label">No. of Days</label>
                                    <input type="text" class="form-control" id="days" >
                                </div>
                                <div class="mb-3">
                                    <label for="roomPrice" class="form-label">Room Price</label>
                                    <input type="text" class="form-control" id="roomPrice" >
                                </div>
                                <div class="mb-3">
                                    <label for="services" class="form-label">Services</label>
                                    <input type="text" class="form-control" id="foodOrders" >
                                </div>
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total</label>
                                    <input type="text" class="form-control" id="total" >
                                </div>
                                <!-- <button type="submit" class="btn btn-dark w-100">Check Out</button> -->
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Invoice -->
                <!-- <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col">
                                    <h1 class="hotel-name display-4 mb-2">Her Home Hostel</h1>
                                    <p class="mb-0">Gandaki, Nepal</p>
                                    <p class="mb-0">Damauli, Tanahun</p>
                                    <p class="mb-0">33900, Vyas-2</p>
                                    <p class="mb-0">Phone: 065-560000</p>
                                    <p>Website: www.herhomehostel.com</p>
                                </div>
                                <div class="col-auto">
                                    <label for="date" class="form-label">Date:</label>
                                    <input type="date" id="date" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="invoiceNo" class="form-label">Invoice No:</label>
                                    <input type="text" id="invoiceNo" class="form-control" placeholder="Invoice No">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Billed To:</label>
                                <input type="text" class="form-control mb-2" placeholder="Customer Name">
                                <input type="text" class="form-control" placeholder="Customer Address">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Check In:</label>
                                    <input type="date" id="date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Check Out:</label>
                                    <input type="date" id="date" class="form-control">
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Type</th>
                                            <th>Item/Service</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <div class="mb-2 row">
                                        <label class="col-sm-4 col-form-label">Sub Total:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Sub Total">
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label class="col-sm-4 col-form-label">Discount:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Discount">
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label class="col-sm-4 col-form-label">VAT (13%):</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="VAT 13%">
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label class="col-sm-4 col-form-label">Total:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Total">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
