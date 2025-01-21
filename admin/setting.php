<?php
require('inc/db.php');
require('inc/essentials.php');

// All existing backend code remains exactly the same until the HTML part
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

    // Check if image file is actual image or fake image
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
                $update_query = "UPDATE site_settings SET logo_path = '$logo_path'";
                $result = mysqli_query($conn, $update_query);
            } else {
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

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Map Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['map_latitude'])) {
    $latitude = mysqli_real_escape_string($conn, $_POST['map_latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['map_longitude']);
    $address = mysqli_real_escape_string($conn, $_POST['map_address']);
    
    $query = "UPDATE site_settings SET 
              map_latitude = '$latitude',
              map_longitude = '$longitude',
              map_address = '$address'";
              
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Map location updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating map location";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Handle Site Title Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['site_title'])) {
    $site_title = mysqli_real_escape_string($conn, $_POST['site_title']);
    
    // Check if settings exist
    $check_existing = "SELECT id FROM site_settings LIMIT 1";
    $existing_result = mysqli_query($conn, $check_existing);
    
    if (mysqli_num_rows($existing_result) > 0) {
        $query = "UPDATE site_settings SET site_title = '$site_title'";
    } else {
        $query = "INSERT INTO site_settings (site_title) VALUES ('$site_title')";
    }
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Site title updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating site title: " . mysqli_error($conn);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch current site settings
$settings_query = "SELECT * FROM site_settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$site_settings = mysqli_fetch_assoc($settings_result);

// Handle About Us upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['about_image'])) {
    $target_dir = "uploads/about/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = uniqid() . '_' . basename($_FILES['about_image']['name']);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is actual image or fake image
    $check = getimagesize($_FILES['about_image']['tmp_name']);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION['error'] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES['about_image']['size'] > 5000000) {
        $_SESSION['error'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_formats = ["jpg", "jpeg", "png", "gif", "webp"];
    if(!in_array($imageFileType, $allowed_formats)) {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG, GIF & WebP files are allowed.";
        $uploadOk = 0;
    }

    // Upload and save about image
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['about_image']['tmp_name'], $target_file)) {
            // Prepare SQL to insert about us data
            $about_image = mysqli_real_escape_string($conn, $target_file);
            $about_description = mysqli_real_escape_string($conn, $_POST['about_description']);
            
            // Check if about us data already exists
            $check_existing = "SELECT id FROM about_us LIMIT 1";
            $existing_result = mysqli_query($conn, $check_existing);

            if (mysqli_num_rows($existing_result) > 0) {
                $update_query = "UPDATE about_us SET description = '$about_description', image_path = '$about_image'";
                $result = mysqli_query($conn, $update_query);
            } else {
                $insert_query = "INSERT INTO about_us (description, image_path) VALUES ('$about_description', '$about_image')";
                $result = mysqli_query($conn, $insert_query);
            }

            if ($result) {
                $_SESSION['success_message'] = "About Us updated successfully!";
            } else {
                $_SESSION['error_message'] = "Database error: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
        }
    } else {
        $_SESSION['error_message'] = $error_message;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch current About Us data
$about_query = "SELECT description, image_path FROM about_us LIMIT 1";
$about_result = mysqli_query($conn, $about_query);
$current_about = $about_result && mysqli_num_rows($about_result) > 0 
    ? mysqli_fetch_assoc($about_result) 
    : null;


// Fetch current logo
$logo_query = "SELECT logo_path FROM site_settings LIMIT 1";
$logo_result = mysqli_query($conn, $logo_query);
$current_logo = $logo_result && mysqli_num_rows($logo_result) > 0 
    ? mysqli_fetch_assoc($logo_result)['logo_path'] 
    : null;

// Handle POST request for adding facility
// Handle POST request for adding or editing facility
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['facility_title'])) {
        $title = mysqli_real_escape_string($conn, $_POST['facility_title']);
        $description = mysqli_real_escape_string($conn, $_POST['facility_description']);
        
        if (isset($_POST['facility_id']) && !empty($_POST['facility_id'])) {
            // Edit Facility
            $id = mysqli_real_escape_string($conn, $_POST['facility_id']);
            $query = "UPDATE facilities SET title = '$title', description = '$description' WHERE id = $id";
        } else {
            // Add Facility
            $query = "INSERT INTO facilities (title, description) VALUES ('$title', '$description')";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = isset($_POST['facility_id']) ? "Facility updated successfully!" : "Facility added successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
        header('Location: setting.php');
        exit();
    }
    
    // Handle POST request for adding or editing room
    if (isset($_POST['room_type'])) {
        $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
        $price = mysqli_real_escape_string($conn, $_POST['room_price']);
        
        if (isset($_POST['room_id']) && !empty($_POST['room_id'])) {
            // Edit Room
            $id = mysqli_real_escape_string($conn, $_POST['room_id']);
            $query = "UPDATE rooms SET room_type='$room_type', price='$price' WHERE id=$id";
        } else {
            // Add Room
            $query = "INSERT INTO rooms (room_type, price) VALUES ('$room_type', '$price')";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = isset($_POST['room_id']) ? "Room updated successfully!" : "Room added successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
        header('Location: setting.php');
        exit();
    }
}
// Handle DELETE requests
if (isset($_GET['delete_facility'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_facility']);
    $query = "DELETE FROM facilities WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Facility deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting facility: " . mysqli_error($conn);
    }
    header('Location: setting.php');
    exit();
}

if (isset($_GET['delete_room'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_room']);
    $query = "DELETE FROM rooms WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Room deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting room: " . mysqli_error($conn);
    }
    header('Location: setting.php');
    exit();
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

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="common.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #3b82f6;
        --success-color: #059669;
        --danger-color: #dc2626;
        --background-color: #f8fafc;
        --card-background: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --hover-bg: #f1f5f9;
    }

    body {
        background-color: var(--background-color);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--text-primary);
        min-height: 100vh;
    }

    /* Fixed Layout Styles */
    .dashboard-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
        flex-shrink: 0;
        background: #1f2937;
        min-height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
    }

    .main-content {
        flex-grow: 1;
        margin-left: 250px;
        /* Match sidebar width */
        padding: 2rem;
        width: calc(100% - 250px);
        min-height: 100vh;
    }

    .section-card {
        background: var(--card-background);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .section-card:hover {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .card-header {
        background: #1f2937;
        color: white;
        padding: 0.5rem 0.5rem;
        font-weight: 300;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .content-area {
        padding: 1rem;
    }

    .modern-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 300;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .modern-btn:hover {
        transform: translateY(-1px);
    }

    .logo-preview-container {
        width: 200px;
        height: 200px;
        border-radius: 1rem;
        overflow: hidden;
        margin: 0 auto;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .logo-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .list-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .list-item:hover {
        background-color: var(--hover-bg);
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .badge-modern {
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 85px;
        height: 28px;
        background: var(--primary-color);
        color: white;
        text-align: center;
        margin: 0;
        vertical-align: middle;
        white-space: nowrap;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid var(--border-color);
        background: transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .action-btn:hover {
        background-color: var(--hover-bg);
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 400;
        color: var(--text-primary);
        border-bottom-width: 2px;
    }

    .table td {
        vertical-align: middle;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    }

    .modal-header {
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .form-control {
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--success-color);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem;
        }

        .sidebar {
            display: none;
            /* Or implement a mobile menu solution */
        }
    }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php require('inc/sideMenu.php'); ?>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">

                <div class="row">

                <!-- Site Title Management Section -->
<div class="col-md-12 mb-4">
    <div class="section-card">
        <div class="card-header">
            <h5 class="mb-0">Site Title Management</h5>
        </div>
        <div class="content-area">
            <form method="POST" action="" id="siteTitleForm">
                <div class="input-group mb-3">
                    <input type="text" name="site_title" class="form-control" 
                           value="<?php echo isset($site_settings['site_title']) ? htmlspecialchars($site_settings['site_title']) : ''; ?>" 
                           placeholder="Enter Site Title" required>
                    <button class="modern-btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i>
                        Update Title
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

                    <!-- Logo Management Section -->
                    <div class="col-md-4 mb-4">
                        <div class="section-card">
                            <div class="card-header">
                                <h5 class="mb-0">Logo Management</h5>
                            </div>
                            <div class="content-area text-center">
                                <?php if ($current_logo): ?>
                                <div class="logo-preview-container mb-4">
                                    <img src="<?php echo htmlspecialchars($current_logo); ?>" alt="Current Logo"
                                        class="logo-preview">
                                </div>
                                <?php endif; ?>

                                <form id="logoUploadForm" method="POST" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <input type="file" name="site_logo" id="siteLogo"
                                            accept="image/png,image/jpeg,image/gif,image/webp" class="form-control"
                                            required>

                                        <div id="logoPreviewContainer" class="mt-3 d-none">
                                            <img id="logoPreview" src="" alt="Logo Preview" class="logo-preview">
                                        </div>
                                    </div>
                                    <button type="submit" class="modern-btn btn-primary">
                                        <i class="bi bi-cloud-upload"></i>
                                        Upload New Logo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities Section -->
                    <div class="col-md-8 mb-4">
                        <div class="section-card">
                            <div class="card-header">
                                <h5 class="mb-0">Facilities Management</h5>
                                <button class="modern-btn btn-light" data-bs-toggle="modal"
                                    data-bs-target="#add-facility">
                                    <i class="bi bi-plus-lg"></i>
                                    Add Facility
                                </button>
                            </div>
                            <div class="content-area">
                                <div class="facilities-list">
                                    <?php while ($facility = mysqli_fetch_assoc($facilities_result)): ?>
                                    <div class="list-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">
                                                <?php echo htmlspecialchars($facility['title']); ?></h6>
                                            <p class="mb-0 text-secondary">
                                                <?php echo substr(htmlspecialchars($facility['description']), 0, 50) . '...'; ?>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge-modern bg-success">
                                                <?php echo date('M d, Y', strtotime($facility['created_at'])); ?>
                                            </span>
                                            <div class="action-buttons">
                                                <button class="action-btn text-primary" onclick="editFacility(<?php echo $facility['id']; ?>, 
                                                            '<?php echo addslashes($facility['title']); ?>', 
                                                            '<?php echo addslashes($facility['description']); ?>')"
                                                    data-bs-toggle="modal" data-bs-target="#edit-facility">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="?delete_facility=<?php echo $facility['id']; ?>"
                                                    class="action-btn text-danger"
                                                    onclick="confirmDelete('Are you sure you want to delete this facility?', <?php echo $facility['id']; ?>, 'facility')">
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
                    <div class="col-12">
                        <div class="section-card">
                            <div class="card-header">
                                <h5 class="mb-0">Rooms Management</h5>
                                <button class="modern-btn btn-light" data-bs-toggle="modal" data-bs-target="#add-room">
                                    <i class="bi bi-plus-lg"></i>
                                    Add Room
                                </button>
                            </div>
                            <div class="content-area">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Room Type</th>
                                                <th>Price</th>
                                                <th>Created Date</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
                                            <tr>
                                                <td>
                                                    <span
                                                        class="fw-medium"><?php echo htmlspecialchars($room['room_type']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge-modern bg-success">
                                                        Rs <?php echo number_format($room['price'], 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($room['created_at'])); ?>
                                                </td>
                                                <td class="text-end">
                                                    <div class="action-buttons justify-content-end">
                                                        <button class="action-btn text-primary" onclick="editRoom(<?php echo $room['id']; ?>, 
                                                                    '<?php echo addslashes($room['room_type']); ?>', 
                                                                    '<?php echo $room['price']; ?>')"
                                                            data-bs-toggle="modal" data-bs-target="#edit-room">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="?delete_room=<?php echo $room['id']; ?>"
                                                            class="action-btn text-danger"
                                                            onclick="confirmDelete('Are you sure you want to delete this room?', <?php echo $room['id']; ?>, 'room')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- About Us Management Section -->
<div class="col-12">
    <div class="section-card">
        <div class="card-header">
            <h5 class="mb-0">About Us Management</h5>
            <button class="modern-btn btn-light" data-bs-toggle="modal" data-bs-target="#edit-about-us">
                <i class="bi bi-pencil"></i>
                Edit About Us
            </button>
        </div>
        <div class="content-area">
            <div class="row">
                <div class="col-md-4">
                    <?php if ($current_about && $current_about['image_path']): ?>
                    <div class="logo-preview-container mb-4">
                        <img src="<?php echo htmlspecialchars($current_about['image_path']); ?>" alt="About Us Image" class="logo-preview">
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <p class="text-secondary">
                        <?php echo $current_about ? htmlspecialchars($current_about['description']) : 'No description available'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact US Settings -->
<div class="col-12 mb-4">
    <div class="section-card">
        <div class="card-header">
            <h5 class="mb-0">Contact Us Settings</h5>
        </div>
        <div class="content-area">
            <form method="POST" action="" id="mapSettingsForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="map_latitude" class="form-control" 
                                value="<?php echo isset($site_settings['map_latitude']) ? $site_settings['map_latitude'] : '27.7090'; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="map_longitude" class="form-control" 
                                value="<?php echo isset($site_settings['map_longitude']) ? $site_settings['map_longitude'] : '85.2911'; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="map_address" class="form-control" 
                                value="<?php echo isset($site_settings['map_address']) ? htmlspecialchars($site_settings['map_address']) : 'Kathmandu 44600'; ?>" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="modern-btn btn-primary">Update Location</button>
            </form>
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
                            <label class="form-label">Facility Title</label>
                            <input type="text" name="facility_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="facility_description" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="modern-btn btn-primary">Add Facility</button>
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
                        <input type="hidden" name="facility_id" id="edit_facility_id">
                        <div class="mb-3">
                            <label class="form-label">Facility Title</label>
                            <input type="text" name="facility_title" id="edit_facility_title" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="facility_description" id="edit_facility_description" class="form-control"
                                rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_facility" class="modern-btn btn-primary">Save Changes</button>
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
                            <label class="form-label">Room Type</label>
                            <input type="text" name="room_type" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs</span>
                                <input type="number" name="room_price" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="modern-btn btn-primary">Add Room</button>
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
                            <label class="form-label">Room Type</label>
                            <input type="text" name="room_type" id="edit_room_type" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="room_price" id="edit_room_price" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_room" class="modern-btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit About Us Modal -->
<div class="modal fade" id="edit-about-us" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit About Us</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="about_description" class="form-control" rows="5" required><?php 
                            echo $current_about ? htmlspecialchars($current_about['description']) : ''; 
                        ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="about_image" id="aboutImage"
                            accept="image/png,image/jpeg,image/gif,image/webp" class="form-control">

                        <div id="aboutImagePreviewContainer" class="mt-3 d-none">
                            <img id="aboutImagePreview" src="" alt="About Image Preview" class="logo-preview">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modern-btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="modern-btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- JavaScript Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        // About Image preview functionality
document.getElementById('aboutImage').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('aboutImagePreviewContainer');
    const previewImage = document.getElementById('aboutImagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.classList.add('d-none');
    }
});

    // Logo preview functionality
    document.getElementById('siteLogo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('logoPreviewContainer');
        const previewImage = document.getElementById('logoPreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('d-none');
        }
    });

    // Success/Error messages
    <?php if(isset($_SESSION['success_message'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>',
        timer: 3000,
        showConfirmButton: false
    });
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>',
        timer: 3000,
        showConfirmButton: false
    });
    <?php endif; ?>

    // Edit functionality
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
    function confirmDelete(message, id, type) {
        event.preventDefault(); // Prevent the default action of the link
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `?delete_${type}=${id}`;
            }
        });
    }
    </script>
</body>

</html>