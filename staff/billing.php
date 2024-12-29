<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         /* Sidebar CSS */
         .sidebar {
             margin: 0px;
             height: 150vh;
             background-color: #343a40;
             padding-top: 10px;
         }
 
         .sidebar a {
             color: #fff;
             padding: 25px;
             display: block;
             text-decoration: none;
         }
 
         .sidebar a:hover {
             background-color: #495057;
         }
 
         .logout-btn {
             margin-top: 30px;
             background-color: #f8f9fa;
             border: none;
             color: #000;
             padding: 6px;
         }
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .hotel-name {
            font-family: serif;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="row g-4">
            
            <!-- Left Side - Stay Duration Calculation -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h2 class="mb-0">Stay Duration Calculation</h2>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label for="idNo" class="form-label">ID No.</label>
                                <input type="text" class="form-control" id="idNo">
                            </div>
                            <div class="mb-3">
                                <label for="customerName" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customerName" >
                            </div>
                            <div class="mb-3">
                                <label for="roomType" class="form-label">Room Type</label>
                                <select class="form-select" id="roomType">
                                    <option selected>Select Room Type</option>
                                    <option value="Single">Single Room</option>
                                    <option value="Double">Double Room</option>
                                    <option value="Triple">Triple Room</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="roomNo" class="form-label">Room No</label>
                                <select class="form-select" id="roomNo">
                                    <option selected>Select Room No</option>
                                    <option value="101">101</option>
                                    <option value="102">102</option>
                                    <option value="103">103</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="checkIn" class="form-label">Check In</label>
                                <input type="date" class="form-control" id="checkIn">
                            </div>
                            <div class="mb-3">
                                <label for="checkOut" class="form-label">Check Out</label>
                                <input type="date" class="form-control" id="checkOut">
                            </div>
                            <div class="mb-3">
                                <label for="noOfDays" class="form-label">No. of Days</label>
                                <input type="number" class="form-control" id="noOfDays" placeholder="4">
                            </div>
                            <div class="mb-3">
                                <label for="roomPrice" class="form-label">Room Price</label>
                                <input type="number" class="form-control" id="roomPrice" placeholder="2000">
                            </div>
                            <div class="mb-3">
                                <label for="services" class="form-label">Services</label>
                                <input type="number" class="form-control" id="foodOrders" placeholder="3000">
                            </div>
                            <div class="mb-3">
                                <label for="total" class="form-label">Total</label>
                                <input type="number" class="form-control" id="total" placeholder="11000">
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Check Out</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side - Invoice -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col">
                                <h1 class="hotel-name display-4 mb-2">Her Hostel Home</h1>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>