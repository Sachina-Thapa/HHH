<?php
// Fetch logo from site_settings
$logo_query = "SELECT logo_path FROM site_settings LIMIT 1";
$logo_result = mysqli_query($conn, $logo_query);
$logo_path = $logo_result && mysqli_num_rows($logo_result) > 0 
    ? mysqli_fetch_assoc($logo_result)['logo_path'] 
    : '../images/logoo.png'; // Fallback to default logo if no logo in database
?>

<style>
    /* Sidebar CSS */
    .sidebar {
        position: fixed;  /* Fix the sidebar position */
        top: 0;           /* Align to the top of the page */
        bottom: 0;        /* Stretch to the bottom of the page */
        left: 0;          /* Align to the left side */
        margin: 0px;
        width: 16.666667%; /* Corresponds to col-md-2 in Bootstrap grid */
        background-color: #343a40; /* Dark gray for sidebar */
        padding-top: 10px;
        overflow-y: auto; /* Allow scrolling if content is too long */
        z-index: 1000;    /* Ensure it stays above other content */
    }

    .logo-container {
        display: flex;             /* Enable flexbox */
        justify-content: center;   /* Center horizontally */
        align-items: center;       /* Center vertically */
        margin-bottom: 20px;       /* Add some space below the logo */
    }

    .sidebar img {
        width: 90px;  /* Fixed width */
        height: 90px; /* Fixed height */
        object-fit: contain; /* Ensure logo fits without distortion */
        border-radius: 50%;  /* Circular shape */
        filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5)); /* Shadow effect */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
    }

    .sidebar img:hover {
        transform: scale(1.03); /* Slight zoom on hover */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
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

    .btn {
        margin-top: 0px;
        background-color: transparent !important;
        backdrop-filter: blur(3px);
        border: none;
    }

    /* Main Content Wrapper */
    .content-wrapper {
        margin-left: 16.666667%; /* Same width as the sidebar */
        width: calc(100% - 16.666667%);
        background: linear-gradient(to right, #f3f4f6, #ffffff); /* Soft gradient */
        color: #333; /* Dark gray text for better readability */
        padding: 20px;
        font-family: Arial, sans-serif;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle inner shadow */
    }

    .content-wrapper h1 {
        color: #007bff; /* Professional blue for headings */
        font-weight: bold;
        margin-bottom: 20px;
    }

    .content-wrapper p {
        color: #555; /* Medium gray for paragraphs */
        line-height: 1.6;
    }
</style>

<!-- Sidebar -->
<div class="col-md-2 sidebar">
    <!-- Centered Logo -->
    <div class="logo-container">
        <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo">
    </div>
    <!-- Sidebar Links -->
    <a href="addash.php">Dashboard</a>
    <a href="roomManagement.php">Room Management</a>
    <a href="staffmanagement.php">Staff Management</a>
    <a href="hostelerManagement.php">Hosteller</a>
    <a href="setting.php">Settings</a>
    <hr class="w-full text-white mb-0">
    <button class="btn w-100"><a href="../index.php">LOG OUT</a></button>
</div>
