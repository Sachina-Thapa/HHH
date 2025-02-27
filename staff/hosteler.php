<?php
require('inc/db.php');

// Fetch hostelers data
$stmt = $mysqli->prepare("SELECT * FROM hostelers");
$stmt->execute();
$result = $stmt->get_result();

// API endpoints in the same file
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // Get hosteler details
    if ($_GET['action'] == 'get_hosteler_details' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $mysqli->prepare("SELECT * FROM hostelers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Hosteler not found']);
            exit;
        }
        
        $hosteler = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'hosteler' => $hosteler]);
        exit;
    }
    
    // Get booking information
    if ($_GET['action'] == 'get_hosteler_booking' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $mysqli->prepare("SELECT * FROM booking WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'bookings' => $bookings]);
        exit;
    }
    
    // Get fee information
    if ($_GET['action'] == 'get_hosteler_fees' && isset($_GET['id'])) {
        $hid = intval($_GET['id']);
        
        try {
            // Use the correct table name "fee" instead of "hostelerService"
            $stmt = $mysqli->prepare("SELECT feeid, hid, rid, seid, vid, sid, bid, total, status, confirmed_date FROM fee WHERE hid = ?");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }
            
            $stmt->bind_param("i", $hid);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $fees = [];
            
            while ($row = $result->fetch_assoc()) {
                $fees[] = $row;
            }
            
            echo json_encode(['status' => 'success', 'fees' => $fees]);
        } catch (Exception $e) {
            // Return a JSON response with the error
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    // Get visitor information
    if ($_GET['action'] == 'get_hosteler_visitors' && isset($_GET['id'])) {
        $hid = intval($_GET['id']);
        $stmt = $mysqli->prepare("SELECT * FROM visitorform WHERE hid = ?");
        $stmt->bind_param("i", $hid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $visitors = [];
        while ($row = $result->fetch_assoc()) {
            $visitors[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'visitors' => $visitors]);
        exit;
    }
    
    // Search hostelers
    if ($_GET['action'] == 'search_hosteler' && isset($_GET['query'])) {
        $query = "%" . $mysqli->real_escape_string($_GET['query']) . "%";
        $stmt = $mysqli->prepare("SELECT * FROM hostelers WHERE name LIKE ? OR email LIKE ? OR phone_number LIKE ?");
        $stmt->bind_param("sss", $query, $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $hostelers = [];
        while ($row = $result->fetch_assoc()) {
            $hostelers[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'hostelers' => $hostelers]);
        exit;
    }
    
    // Get all hostelers
    if ($_GET['action'] == 'get_all_hostelers') {
        $stmt = $mysqli->prepare("SELECT * FROM hostelers");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $hostelers = [];
        while ($row = $result->fetch_assoc()) {
            $hostelers[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'hostelers' => $hostelers]);
        exit;
    }
    
    // Check if hosteler can be deleted
    if ($_GET['action'] == 'check_hosteler_deletion' && isset($_GET['id'])) {
        $hid = intval($_GET['id']);
        
        try {
            $mysqli->begin_transaction();
            
            // Check for pending fees
            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM fee WHERE hid = ? AND status IN ('pending', 'canceled')");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $pendingFees = $row['count'];
            
            // Check for pending services
            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM hservice WHERE hid = ? AND status = 'pending'");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $pendingServices = $row['count'];
            
            // Check for pending visitor requests
            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM visitorform WHERE hid = ? AND status = 'pending'");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $pendingVisitors = $row['count'];
            
            $mysqli->commit();
            
            $canDelete = ($pendingFees == 0 && $pendingServices == 0 && $pendingVisitors == 0);
            
            echo json_encode([
                'status' => 'success',
                'can_delete' => $canDelete,
                'reasons' => [
                    'pending_fees' => $pendingFees > 0,
                    'pending_services' => $pendingServices > 0, 
                    'pending_visitors' => $pendingVisitors > 0
                ]
            ]);
            
        } catch (Exception $e) {
            $mysqli->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    // Delete hosteler
    if ($_GET['action'] == 'delete_hosteler' && isset($_GET['id'])) {
        $hid = intval($_GET['id']);
        
        try {
            $mysqli->begin_transaction();
            
            // First check if the hosteler can be deleted
            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM fee WHERE hid = ? AND status IN ('pending', 'canceled')");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $pendingFees = $row['count'];
            
            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM hservice WHERE hid = ? AND status = 'pending'");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $pendingServices = $row['count'];
            
            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM visitorform WHERE hid = ? AND status = 'pending'");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $pendingVisitors = $row['count'];
            
            if ($pendingFees > 0 || $pendingServices > 0 || $pendingVisitors > 0) {
                throw new Exception("Cannot delete hosteler with pending requests");
            }
            
            // If we get here, it's safe to delete
            
            // Delete related records first 
            // Note: In a real application, you might want to archive these rather than delete
            
            // Delete from visitorform
            $stmt = $mysqli->prepare("DELETE FROM visitorform WHERE hid = ?");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            
            // Delete from hservice
            $stmt = $mysqli->prepare("DELETE FROM hservice WHERE hid = ?");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            
            // Delete from fee
            $stmt = $mysqli->prepare("DELETE FROM fee WHERE hid = ?");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            
            // Delete from booking
            $stmt = $mysqli->prepare("DELETE FROM booking WHERE id = ?");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            
            // Finally delete the hosteler
            $stmt = $mysqli->prepare("DELETE FROM hostelers WHERE id = ?");
            $stmt->bind_param("i", $hid);
            $stmt->execute();
            
            $mysqli->commit();
            
            echo json_encode(['status' => 'success', 'message' => 'Hosteler deleted successfully']);
            
        } catch (Exception $e) {
            $mysqli->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    // If no valid action is provided
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            padding: 0;
            margin: 0;
            background-color: #343a40;
            color: white;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        /* Styles for the right side drawer */
        .right-drawer {
            position: fixed;
            top: 0;
            right: -600px; /* Start off-screen */
            width: 550px;
            height: 100vh;
            background-color: white;
            box-shadow: -3px 0 10px rgba(0,0,0,0.2);
            transition: right 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
            padding: 20px;
        }
        .right-drawer.open {
            right: 0;
        }
        .drawer-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .close-drawer {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
        }
        .section-card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .section-card .card-header {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-confirmed {
            background-color: #28a745;
            color: white;
        }
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        /* Action buttons in drawer */
        .drawer-actions {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            margin-top: 20px;
            text-align: right;
        }
        /* Delete confirmation modal */
        .delete-icon {
            color: #dc3545;
            cursor: pointer;
        }
        .delete-reason {
            margin-bottom: 8px;
            color: #721c24;
            background-color: #f8d7da;
            border-radius: 4px;
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row no-gutters">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <?php require('inc/sidemenu.php'); ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4" id="main-content">
                <h3 class="mb-4">HOSTELERS</h3>

                <!-- Search Bar -->
                <div class="text-end mb-4">
                    <input type="text" class="form-control" id="search-user" placeholder="Search user..." oninput="if(this.value === '') get_hosteler(); else search_hosteler(this.value)" 
                    aria-label="Search hosteler by name">
                </div>

                <!-- Hosteler Table -->
                <div class="card border shadow-sm mb-4">
                    <div class="card-body">
                    <div style="overflow-x: auto; white-space: nowrap;">
                        <table class="table table-hover border text-center">
                            <thead>
                                <tr class="bg-dark text-light">
                                    <th scope="col">S.N.</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone No.</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">DOB</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="hosteler-data">
                                <?php
                                $i = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "
                                    <tr>
                                        <td>$i</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone_number']}</td>
                                        <td>{$row['address']}</td>
                                        <td>{$row['date_of_birth']}</td>
                                        <td>{$row['status']}</td>
                                        <td>{$row['created_at']}</td>
                                        <td>
                                            <button onclick='vhosteler({$row['id']})' class='btn btn-info btn-sm'>View</button>
                                            <button onclick='checkDeleteHosteler({$row['id']})' class='btn btn-danger btn-sm'>Delete</button>
                                        </td>
                                    </tr>
                                    ";
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>

                <!-- Hosteler Details Modal (keeping the existing one) -->
                <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel">Hosteler Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>ID:</strong> <span id="hosteler-id"></span></p>
                                <p><strong>Name:</strong> <span id="hosteler-name"></span></p>
                                <p><strong>Email:</strong> <span id="hosteler-email"></span></p>
                                <p><strong>Phone Number:</strong> <span id="hosteler-phone_number"></span></p>
                                <p><strong>Address:</strong> <span id="hosteler-address"></span></p>
                                <p><strong>Status:</strong> <span id="hosteler-status"></span></p>
                                <p><strong>Date of Birth:</strong> <span id="hosteler-date_of_birth"></span></p>
                                <p><strong>Created At:</strong> <span id="hosteler-created_at"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="delete-message">
                                    Are you sure you want to delete this hosteler? This action cannot be undone.
                                </div>
                                <div id="delete-reasons" class="mt-3" style="display: none;">
                                    <p class="text-danger">This hosteler cannot be deleted due to the following reasons:</p>
                                    <div id="delete-reasons-list"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side Drawer for Detailed View -->
                <div class="drawer-backdrop" id="drawer-backdrop"></div>
                <div class="right-drawer" id="right-drawer">
                    <button class="close-drawer" id="close-drawer">&times;</button>
                    <h3 class="mb-4">Hosteler Detailed Information</h3>
                    
                    <!-- Personal Information Section -->
                    <div class="card section-card mb-4">
                        <div class="card-header">
                            Personal Information
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p><strong>Name:</strong> <span id="drawer-hosteler-name">Loading...</span></p>
                                    <p><strong>Email:</strong> <span id="drawer-hosteler-email">Loading...</span></p>
                                    <p><strong>Phone No:</strong> <span id="drawer-hosteler-phone">Loading...</span></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Address:</strong> <span id="drawer-hosteler-address">Loading...</span></p>
                                    <p><strong>Date of Birth:</strong> <span id="drawer-hosteler-dob">Loading...</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Username:</strong> <span id="drawer-hosteler-username">Loading...</span></p>
                                    <p><strong>Status:</strong> <span id="drawer-hosteler-status">Loading...</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room & Booking Information -->
                    <div class="card section-card mb-4">
                        <div class="card-header">
                            Room & Booking Details
                        </div>
                        <div class="card-body" id="booking-details-container">
                            <div class="text-center py-4 booking-placeholder">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading booking information...</p>
                            </div>
                            <!-- Booking data will be dynamically loaded here -->
                        </div>
                    </div>
                    
                    <!-- Fee Information -->
                    <div class="card section-card mb-4">
                        <div class="card-header">
                            Fee Information
                        </div>
                        <div class="card-body" id="fee-details-container">
                            <div class="text-center py-4 fee-placeholder">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading fee information...</p>
                            </div>
                            <!-- Fee data will be dynamically loaded here -->
                        </div>
                    </div>
                    
                    <!-- Visitor Information -->
                    <div class="card section-card mb-4">
                        <div class="card-header">
                            Visitor Information
                        </div>
                        <div class="card-body" id="visitor-details-container">
                            <div class="text-center py-4 visitor-placeholder">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading visitor information...</p>
                            </div>
                            <!-- Visitor data will be dynamically loaded here -->
                        </div>
                    </div>
                    
                    <!-- Drawer actions -->
                    <div class="drawer-actions">
                        <button type="button" class="btn btn-danger mr-2" onclick="checkDeleteHosteler(currentHostelerId)">Delete Hosteler</button>
                        <button type="button" class="btn btn-secondary" id="close-drawer-btn">Close</button>
                    </div>
                </div>
            </div> 
        </div>
    </div> 

    <!-- Include necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    let currentHostelerId = null;
    let deleteModal = null;
    
    function vhosteler(id) {
        // Store current hosteler ID
        currentHostelerId = id;
        
        // Clear previous data
        clearDrawerData();
        
        // Open the drawer
        openDrawer();
        
        // Fetch hosteler data
        fetch(`?action=get_hosteler_details&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update personal information
                    displayHostelerInfo(data.hosteler);
                    
                    // Fetch and display booking information
                    fetchBookingDetails(id);
                    
                    // Fetch and display fee information
                    fetchFeeDetails(id);
                    
                    // Fetch and display visitor information
                    fetchVisitorDetails(id);
                } else {
                    console.error("Error fetching hosteler data:", data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
    }
    
    function search_hosteler(query) {
        fetch(`?action=search_hosteler&query=${query}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateHostelerTable(data.hostelers);
                } else {
                    console.error("Error searching hostelers:", data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
    }
    
    function get_hosteler() {
        fetch('?action=get_all_hostelers')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateHostelerTable(data.hostelers);
                } else {
                    console.error("Error fetching hostelers:", data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
    }
    
    function updateHostelerTable(hostelers) {
        let tableContent = '';
        hostelers.forEach((hosteler, index) => {
            tableContent += `
            <tr>
                <td>${index + 1}</td>
                <td>${hosteler.name}</td>
                <td>${hosteler.email}</td>
                <td>${hosteler.phone_number}</td>
                <td>${hosteler.address || ''}</td>
                <td>${hosteler.date_of_birth || ''}</td>
                <td>${hosteler.status}</td>
                <td>${hosteler.created_at}</td>
                <td>
                    <button onclick='vhosteler(${hosteler.id})' class='btn btn-info btn-sm'>View</button>
                    <button onclick='checkDeleteHosteler(${hosteler.id})' class='btn btn-danger btn-sm'>Delete</button>
                </td>
            </tr>
            `;
        });
        document.getElementById('hosteler-data').innerHTML = tableContent;
    }
    
    // Drawer functions
    function openDrawer() {
        document.getElementById('right-drawer').classList.add('open');
        document.getElementById('drawer-backdrop').style.display = 'block';
    }
    
    function closeDrawer() {
        document.getElementById('right-drawer').classList.remove('open');
        document.getElementById('drawer-backdrop').style.display = 'none';
    }
    
    function clearDrawerData() {
        // Clear personal info
        document.getElementById('drawer-hosteler-name').textContent = 'Loading...';
        document.getElementById('drawer-hosteler-email').textContent = 'Loading...';
        document.getElementById('drawer-hosteler-phone').textContent = 'Loading...';
        document.getElementById('drawer-hosteler-address').textContent = 'Loading...';
        document.getElementById('drawer-hosteler-dob').textContent = 'Loading...';
        document.getElementById('drawer-hosteler-username').textContent = 'Loading...';
        document.getElementById('drawer-hosteler-status').textContent = 'Loading...';
        
        // Show loading placeholders
        document.getElementById('booking-details-container').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading booking information...</p>
            </div>
        `;
        
        document.getElementById('fee-details-container').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading fee information...</p>
            </div>
        `;
        
        document.getElementById('visitor-details-container').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading visitor information...</p>
            </div>
        `;
    }
    
    function displayHostelerInfo(hosteler) {
        document.getElementById('drawer-hosteler-name').textContent = hosteler.name;
        document.getElementById('drawer-hosteler-email').textContent = hosteler.email;
        document.getElementById('drawer-hosteler-phone').textContent = hosteler.phone_number;
        document.getElementById('drawer-hosteler-address').textContent = hosteler.address || 'Not available';
        document.getElementById('drawer-hosteler-dob').textContent = hosteler.date_of_birth || 'Not available';
        document.getElementById('drawer-hosteler-username').textContent = hosteler.username;
        document.getElementById('drawer-hosteler-status').textContent = hosteler.status == 1 ? 'Active' : 'Inactive';
    }
    
    function fetchBookingDetails(hostelerId) {
        fetch(`?action=get_hosteler_booking&id=${hostelerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayBookingInfo(data.bookings);
                } else {
                    document.getElementById('booking-details-container').innerHTML = `
                        <div class="alert alert-info">No booking information available.</div>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching booking data:", error);
                document.getElementById('booking-details-container').innerHTML = `
                    <div class="alert alert-danger">Error loading booking information.</div>
                `;
            });
    }
    
    function displayBookingInfo(bookings) {
        if (bookings.length === 0) {
            document.getElementById('booking-details-container').innerHTML = `
                <div class="alert alert-info">No booking information available.</div>
            `;
            return;
        }
        
        let html = '';
        bookings.forEach(booking => {
            const statusClass = booking.bstatus === 'pending' ? 'badge-pending' : 
                              (booking.bstatus === 'confirmed' ? 'badge-confirmed' : 'badge-rejected');
            
            html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Room Number:</strong> ${booking.rno}</p>
                            <p><strong>Booking Date:</strong> ${booking.bookingdate}</p>
                            <p><strong>Check-in:</strong> ${booking.check_in || 'Not specified'}</p>
                            <p><strong>Check-out:</strong> ${booking.check_out || 'Not specified'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Number of Days:</strong> ${booking.number_of_days}</p>
                            <p><strong>Arrival:</strong> ${booking.arrival ? 'Yes' : 'No'}</p>
                            <p><strong>Status:</strong> <span class="badge ${statusClass}">${booking.bstatus}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            `;
        });
        
        document.getElementById('booking-details-container').innerHTML = html;
    }
    
    function fetchFeeDetails(hostelerId) {
        fetch(`?action=get_hosteler_fees&id=${hostelerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayFeeInfo(data.fees);
                } else {
                    document.getElementById('fee-details-container').innerHTML = `
                        <div class="alert alert-info">No fee information available.</div>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching fee data:", error);
                document.getElementById('fee-details-container').innerHTML = `
                    <div class="alert alert-danger">Error loading fee information.</div>
                `;
            });
    }
    
    function displayFeeInfo(fees) {
        if (fees.length === 0) {
            document.getElementById('fee-details-container').innerHTML = `
                <div class="alert alert-info">No fee information available.</div>
            `;
            return;
        }
        
        let html = '<div class="table-responsive"><table class="table table-striped">';
        html += `
        <thead>
            <tr>
                <th>Fee ID</th>
                <th>Room ID</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        `;
        
        fees.forEach(fee => {
            const statusClass = fee.status === 'pending' ? 'badge-pending' : 
                             (fee.status === 'confirmed' ? 'badge-confirmed' : 'badge-rejected');
            
            html += `
            <tr>
                <td>${fee.feeid}</td>
                <td>${fee.rid || 'N/A'}</td>
                <td>${fee.total}</td>
                <td><span class="badge ${statusClass}">${fee.status}</span></td>
                <td>${fee.confirmed_date || 'Not confirmed'}</td>
            </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        document.getElementById('fee-details-container').innerHTML = html;
    }
    
    function fetchVisitorDetails(hostelerId) {
        fetch(`?action=get_hosteler_visitors&id=${hostelerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayVisitorInfo(data.visitors);
                } else {
                    document.getElementById('visitor-details-container').innerHTML = `
                        <div class="alert alert-info">No visitor information available.</div>
                    `;
                }
            })
            .catch(error => {
                console.error("Error fetching visitor data:", error);
                document.getElementById('visitor-details-container').innerHTML = `
                    <div class="alert alert-danger">Error loading visitor information.</div>
                `;
            });
    }
    
    function displayVisitorInfo(visitors) {
        if (visitors.length === 0) {
            document.getElementById('visitor-details-container').innerHTML = `
                <div class="alert alert-info">No visitor information available.</div>
            `;
            return;
        }
        
        let html = '';
        visitors.forEach(visitor => {
            const statusClass = visitor.status === 'pending' ? 'badge-pending' : 
                             (visitor.status === 'confirmed' ? 'badge-confirmed' : 'badge-rejected');
            
            html += `
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">${visitor.vname}</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Relation:</strong> ${visitor.relation}</p>
                            <p><strong>Reason:</strong> ${visitor.reason}</p>
                            <p><strong>Days:</strong> ${visitor.days}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge ${statusClass}">${visitor.status}</span></p>
                            <p><strong>Fee:</strong> ${visitor.fee}</p>
                            <p><strong>Requires Voucher:</strong> ${visitor.requires_voucher ? 'Yes' : 'No'}</p>
                        </div>
                    </div>
                </div>
            </div>
            `;
        });
        
        document.getElementById('visitor-details-container').innerHTML = html;
    }
    
    // Delete Hosteler Functions
    function checkDeleteHosteler(id) {
        // Store the ID for use in the confirmation step
        currentHostelerId = id;
        
        // Reset the modal content
        document.getElementById('delete-message').style.display = 'block';
        document.getElementById('delete-reasons').style.display = 'none';
        document.getElementById('delete-reasons-list').innerHTML = '';
        document.getElementById('confirm-delete-btn').style.display = 'block';
        
        // Check if the hosteler can be deleted
        fetch(`?action=check_hosteler_deletion&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.can_delete) {
                        // Show the delete confirmation modal
                        deleteModal.show();
                    } else {
                        // Show reasons why deletion is not allowed
                        let reasonsHtml = '';
                        
                        if (data.reasons.pending_fees) {
                            reasonsHtml += `<div class="delete-reason">
                                <i class="fas fa-exclamation-circle"></i> This hosteler has pending or canceled fee records
                            </div>`;
                        }
                        
                        if (data.reasons.pending_services) {
                            reasonsHtml += `<div class="delete-reason">
                                <i class="fas fa-exclamation-circle"></i> This hosteler has pending service requests
                            </div>`;
                        }
                        
                        if (data.reasons.pending_visitors) {
                            reasonsHtml += `<div class="delete-reason">
                                <i class="fas fa-exclamation-circle"></i> This hosteler has pending visitor requests
                            </div>`;
                        }
                        
                        document.getElementById('delete-message').style.display = 'none';
                        document.getElementById('delete-reasons').style.display = 'block';
                        document.getElementById('delete-reasons-list').innerHTML = reasonsHtml;
                        document.getElementById('confirm-delete-btn').style.display = 'none';
                        
                        // Show the modal with reasons
                        deleteModal.show();
                    }
                } else {
                    console.error("Error checking deletion status:", data.message);
                    alert("Error checking deletion status: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
            });
    }
    
    function deleteHosteler() {
        if (!currentHostelerId) return;
        
        fetch(`?action=delete_hosteler&id=${currentHostelerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Close the modal and drawer if open
                    deleteModal.hide();
                    closeDrawer();
                    
                    // Show success message
                    alert("Hosteler deleted successfully!");
                    
                    // Refresh the hosteler list
                    get_hosteler();
                } else {
                    console.error("Error deleting hosteler:", data.message);
                    alert("Error deleting hosteler: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred during deletion. Please try again.");
            });
    }
    
    // Setup event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap modal
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        // Set up delete confirmation button
        document.getElementById('confirm-delete-btn').addEventListener('click', deleteHosteler);
        
        // Close drawer when clicking backdrop
        document.getElementById('drawer-backdrop').addEventListener('click', closeDrawer);
        
        // Close drawer when clicking X button
        document.getElementById('close-drawer').addEventListener('click', closeDrawer);
        
        // Close drawer when clicking the close button
        document.getElementById('close-drawer-btn').addEventListener('click', closeDrawer);
        
        // Handle escape key to close drawer
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && document.getElementById('right-drawer').classList.contains('open')) {
                closeDrawer();
            }
        });
        
        // Add mobile swipe to close functionality
        const drawer = document.getElementById('right-drawer');
        let touchstartX = 0;
        
        drawer.addEventListener('touchstart', function(e) {
            touchstartX = e.changedTouches[0].screenX;
        });
        
        drawer.addEventListener('touchend', function(e) {
            const touchendX = e.changedTouches[0].screenX;
            const diffX = touchendX - touchstartX;
            
            if (diffX > 100) {  // Swipe right to close
                closeDrawer();
            }
            });
        
        // Update search functionality to be more responsive
        const searchInput = document.getElementById('search-user');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            searchTimeout = setTimeout(() => {
                if (query === '') {
                    get_hosteler();
                } else {
                    search_hosteler(query);
                }
            }, 300); // 300ms debounce
        });
        
        // Add refresh data functionality
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('refresh-data-btn')) {
                if (currentHostelerId) {
                    vhosteler(currentHostelerId);
                }
            }
        });
        
        // Load initial data
        get_hosteler();
    });
    </script>
</body>
</html>