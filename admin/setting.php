<?php
require('inc/db.php');
require('inc/essentials.php');


// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['site_logo'])) {
    $target_dir = "uploads/logo/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = uniqid() . '_' . basename($_FILES['site_logo']['name']);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES['site_logo']['tmp_name']);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION['error'] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES['site_logo']['size'] > 5000000) {
        $_SESSION['error'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_formats = ["jpg", "jpeg", "png", "gif", "webp"];
    if(!in_array($imageFileType, $allowed_formats)) {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG, GIF & WebP files are allowed.";
        $uploadOk = 0;
    }

     // Upload and save logo
     if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_file)) {
            // Prepare SQL to insert logo path
            $logo_path = mysqli_real_escape_string($conn, $target_file);
            
            // Check if a logo already exists
            $check_existing = "SELECT id FROM site_settings LIMIT 1";
            $existing_result = mysqli_query($conn, $check_existing);

            if (mysqli_num_rows($existing_result) > 0) {
                // Update existing record
                $update_query = "UPDATE site_settings SET logo_path = '$logo_path'";
                $result = mysqli_query($conn, $update_query);
            } else {
                // Insert new record
                $insert_query = "INSERT INTO site_settings (logo_path) VALUES ('$logo_path')";
                $result = mysqli_query($conn, $insert_query);
            }

            if ($result) {
                $_SESSION['success_message'] = "Logo uploaded successfully!";
            } else {
                $_SESSION['error_message'] = "Database error: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
        }
    } else {
        $_SESSION['error_message'] = $error_message;
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch current logo
$logo_query = "SELECT logo_path FROM site_settings LIMIT 1";
$logo_result = mysqli_query($conn, $logo_query);
$current_logo = $logo_result && mysqli_num_rows($logo_result) > 0 
    ? mysqli_fetch_assoc($logo_result)['logo_path'] 
    : null;

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet">


<style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
        }
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .btn-modern {

            border-radius: 25px;
            color : black;
            text-transform: uppercase;
            font-weight: 550;
            transition: all 0.3s ease;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .component-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        .component-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .component-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
        }
        .component-body {
            padding: 20px;
        }
        .badge-modern {
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php require('inc/sideMenu.php'); ?>
            
            <!-- Main Content -->
            <div class="col-md-10 content-wrapper py-4 px-4">
                <h2>Settings</h2>

                <div class="col-md-10 p-4">
                <div class="row">
                  <!-- Logo Management Section -->
<div class="col-md-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Logo Management</h5>
        </div>
        <div class="card-body text-center">
            <!-- Display current logo if exists -->
            <?php if ($current_logo): ?>
                <img src="<?php echo htmlspecialchars($current_logo); ?>" 
                     alt="Current Logo" 
                     class="img-fluid rounded-circle mb-3" 
                     style="max-width: 200px; max-height: 200px; object-fit: cover;">
            <?php endif; ?>

            <!-- Logo Upload Form -->
            <form id="logoUploadForm" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file"
                           name="site_logo"
                           id="siteLogo"
                           accept="image/png,image/jpeg,image/gif,image/webp"
                           class="form-control"
                           required>
                    
                    <!-- Image Preview Container -->
                    <div id="logoPreviewContainer" class="mt-3 text-center" style="display: none;">
                        <img id="logoPreview"
                             src=""
                             alt="Logo Preview"
                             class="img-fluid rounded-circle shadow-sm"
                             style="max-height: 200px; max-width: 200px; object-fit: cover;">
                    </div>
                </div>
                <button type="submit" class="btn btn-modern btn-primary">
                    Upload New Logo
                </button>
            </form>
        </div>
    </div>
</div>

                <!-- Facilities Section -->
                <div class="col-md-8 border-0">
                    <div class="card component-card mb-4">
                            <div class="component-header">
                                <h5 class="mb-0">Facilities Management</h5>
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#add-facility">
                                    <i class="bi bi-plus-lg"></i> Add
                                </button>
                            </div>

                            <div class="component-body">
                                <div class="list-group">
                                    <?php while ($facility = mysqli_fetch_assoc($facilities_result)): ?>
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($facility['title']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo substr(htmlspecialchars($facility['description']), 0, 50) . '...'; ?>
                                                </small>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary badge-modern me-2">
                                                    <?php echo date('M d, Y', strtotime($facility['created_at'])); ?>
                                                </span>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="editFacility(<?php echo $facility['id']; ?>, 
                                                            '<?php echo addslashes($facility['title']); ?>', 
                                                            '<?php echo addslashes($facility['description']); ?>')"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#edit-facility">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="?delete_facility=<?php echo $facility['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rooms Management Section -->
                    <div class="col-md-4">
                        <div class="card component-card mb-4">
                            <div class="component-header">
                                <h5 class="mb-0">Rooms Management</h5>
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#add-room">
                                    <i class="bi bi-plus-lg"></i> Add
                                </button>
                            </div>
                            <div class="component-body">
                                <div class="list-group">
                                    <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($room['room_type']); ?></h6>
                                                <small class="text-muted">
                                                    Price: $<?php echo number_format($room['price'], 2); ?>
                                                </small>
                                            </div>
                                            <div>
                                                <span class="badge bg-success badge-modern me-2">
                                                    <?php echo date('M d, Y', strtotime($room['created_at'])); ?>
                                                </span>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="editRoom(<?php echo $room['id']; ?>, 
                                                            '<?php echo addslashes($room['room_type']); ?>', 
                                                            '<?php echo $room['price']; ?>')"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#edit-room">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="?delete_room=<?php echo $room['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
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
    
     <!-- Modern JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        
          // Logo preview functionality
          document.getElementById('siteLogo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('logoPreviewContainer');
        const previewImage = document.getElementById('logoPreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // Display success/error messages
    <?php if(isset($_SESSION['success_message'])): ?>
        alert('<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>');
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
        alert('<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>');
    <?php endif; ?>

        // Logo preview
        document.getElementById('siteLogo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.querySelector('.logo-preview');
                if (preview) {
                    preview.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('logo-preview', 'mb-3');
                    event.target.closest('.card-body').insertBefore(img, event.target);
                }
            }

            reader.readAsDataURL(file);
        });

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
         // Enhanced delete confirmation
         function confirmDelete(message) {
            return Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                return result.isConfirmed;
            });
        }
    </script>
</body>
</html>