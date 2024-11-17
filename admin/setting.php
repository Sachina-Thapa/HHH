<?php
require('inc/db.php');

// Handle POST request for adding facility
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Facility
    if (isset($_POST['facility_title'])) {
        $title = mysqli_real_escape_string($conn, $_POST['facility_title']);
        $description = mysqli_real_escape_string($conn, $_POST['facility_description']);
        
        $query = "INSERT INTO facilities (title, description) VALUES ('$title', '$description')";
        
        if (mysqli_query($conn, $query)) {
            header('Location: setting.php');
            exit();
        }
    }
    
    // Add Room
    if (isset($_POST['room_type'])) {
        $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
        $price = mysqli_real_escape_string($conn, $_POST['room_price']);
        
        $query = "INSERT INTO rooms (room_type, price) VALUES ('$room_type', '$price')";
        
        if (mysqli_query($conn, $query)) {
            header('Location: setting.php');
            exit();
        }
    }

// Edit Facility
if (isset($_POST['edit_facility'])) {
    $id = mysqli_real_escape_string($conn, $_POST['facility_id']);
    $title = mysqli_real_escape_string($conn, $_POST['facility_title']);
    $description = mysqli_real_escape_string($conn, $_POST['facility_description']);
    
    // First check if the facility exists
    $check_query = "SELECT * FROM facilities WHERE id = $id";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        // Facility exists, proceed with update
        $query = "UPDATE facilities SET title = '$title', description = '$description' WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            header('Location: setting.php?success=1');
            exit();
        } else {
            // Handle update error
            header('Location: setting.php?error=update_failed');
            exit();
        }
    } else {
        // Facility doesn't exist
        header('Location: setting.php?error=facility_not_found');
        exit();
    }
}

    // Edit Room
    if (isset($_POST['edit_room'])) {
        $id = mysqli_real_escape_string($conn, $_POST['room_id']);
        $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
        $price = mysqli_real_escape_string($conn, $_POST['room_price']);
        
        $query = "UPDATE rooms SET room_type='$room_type', price='$price' WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            header('Location: setting.php');
            exit();
        }
    }
}

// Handle DELETE requests
if (isset($_GET['delete_facility'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_facility']);
    $query = "DELETE FROM facilities WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header('Location: setting.php');
        exit();
    }
}

if (isset($_GET['delete_room'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_room']);
    $query = "DELETE FROM rooms WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header('Location: setting.php');
        exit();
    }
}

// Get facilities data
$query = "SELECT * FROM facilities ORDER BY id ASC";
$facilities_result = mysqli_query($conn, $query);

// Get rooms data
$query = "SELECT * FROM rooms ORDER BY id ASC";
$rooms_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="common.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .table thead {
            background-color: #000;
            color: #a06666;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php require('inc/sideMenu.php'); ?>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>Settings</h2>

                <!-- Facilities Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0" style="cursor: pointer" onclick="toggleFacilitySection()">
                                Our Facilities <i class="bi bi-chevron-down"></i>
                            </h5>
                        </div>

                        <div id="facilitySection" style="display: none;">
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary shadow-md text-black btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#add-facility">
                                    <i class="bi bi-plus-lg"></i> Add Facility
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i = 1;
                                        while ($facility = mysqli_fetch_assoc($facilities_result)): 
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($facility['title']); ?></td>
                                            <td><?php echo htmlspecialchars($facility['description']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary text-black" 
                                                        onclick="editFacility(<?php echo $facility['id']; ?>, 
                                                        '<?php echo addslashes($facility['title']); ?>', 
                                                        '<?php echo addslashes($facility['description']); ?>')"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#edit-facility">
                                                    Edit
                                                </button>
                                                <a href="?delete_facility=<?php echo $facility['id']; ?>" 
                                                   class="btn btn-sm btn-danger text-black" 
                                                   onclick="return confirm('Are you sure you want to delete this facility?')">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Rooms Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0" style="cursor: pointer" onclick="toggleRoomSection()">
                                Hostel Rooms <i class="bi bi-chevron-down"></i>
                            </h5>
                        </div>

                        <div id="roomSection" style="display: none;">
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary shadow-md text-black btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#add-room">
                                    <i class="bi bi-plus-lg"></i> Add Room
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Room Type</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i = 1;
                                        while ($room = mysqli_fetch_assoc($rooms_result)): 
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                            <td><?php echo htmlspecialchars($room['price']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary text-black" 
                                                        onclick="editRoom(<?php echo $room['id']; ?>, 
                                                        '<?php echo addslashes($room['room_type']); ?>', 
                                                        '<?php echo $room['price']; ?>')"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#edit-room">
                                                    Edit
                                                </button>
                                                <a href="?delete_room=<?php echo $room['id']; ?>" 
                                                   class="btn btn-sm btn-danger text-black" 
                                                   onclick="return confirm('Are you sure you want to delete this room?')">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Facility Modal -->
                <div class="modal fade" id="add-facility" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Facility</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Facility Title</label>
                                        <input type="text" name="facility_title" class="form-control shadow-none" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea name="facility_description" class="form-control shadow-none" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary text-black" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary text-black">Add Facility</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

               <!-- Edit Facility Modal -->
<div class="modal fade" id="edit-facility" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Facility</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Important: This hidden input stores the facility ID -->
                    <input type="hidden" name="facility_id" id="edit_facility_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Facility Title</label>
                        <input type="text" name="facility_title" id="edit_facility_title" class="form-control shadow-none" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="facility_description" id="edit_facility_description" class="form-control shadow-none" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-black" data-bs-dismiss="modal">Cancel</button>
                    <!-- Important: This button must have name="edit_facility" -->
                    <button type="submit" name="edit_facility" class="btn btn-primary text-black">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
                <!-- Add Room Modal -->
                <div class="modal fade" id="add-room" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Room</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Room Type</label>
                                        <input type="text" name="room_type" class="form-control shadow-none" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Price</label>
                                        <input type="number" name="room_price" class="form-control shadow-none" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary text-black" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary text-black">Add Room</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Room Modal -->
                <div class="modal fade" id="edit-room" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Room</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="room_id" id="edit_room_id">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Room Type</label>
                                        <input type="text" name="room_type" id="edit_room_type" class="form-control shadow-none" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Price</label>
                                        <input type="number" name="room_price" id="edit_room_price" class="form-control shadow-none" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary text-black" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="edit_room" class="btn btn-primary text-black">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editFacility(id, title, description) {
            document.getElementById('edit_facility_id').value = id;
            document.getElementById('edit_facility_title').value = title;
            document.getElementById('edit_facility_description').value = description;
        }

        function editRoom(id, roomType, price) {
            document.getElementById('edit_room_id').value = id;
            document.getElementById('edit_room_type').value = roomType;
            document.getElementById('edit_room_price').value = price;
        }

        function toggleFacilitySection() {
            const facilitySection = document.getElementById('facilitySection');
            const isHidden = facilitySection.style.display === 'none';
            facilitySection.style.display = isHidden ? 'block' : 'none';
        }

        function toggleRoomSection() {
            const roomSection = document.getElementById('roomSection');
            const isHidden = roomSection.style.display === 'none';
            roomSection.style.display = isHidden ? 'block' : 'none';
        }
    </script>
</body>
</html>