<?php
// Fetch logo from site_settings
$logo_query = "SELECT logo_path FROM site_settings LIMIT 1";
$logo_result = mysqli_query($conn, $logo_query);
$logo_path = $logo_result && mysqli_num_rows($logo_result) > 0 
    ? mysqli_fetch_assoc($logo_result)['logo_path'] 
    : 'images/logoo.png'; // Fallback to default logo if no logo in database
?>

<style>
     /* Sidebar CSS */
     .sidebar img {
        width: 90px;  /* Fixed width */
        height: 90px; /* Fixed height */
        object-fit: contain; /* Ensure logo fits without distortion */
        border-radius: 50%;  /* Circular shape */
        margin: 0 auto 20px; /* Center the logo and add some bottom margin */
        display: block;
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
    }

    .sidebar img:hover {
        transform: scale(1.03); /* Slight zoom on hover */
        shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
    }
     .sidebar {
             position: fixed;  /* Fix the sidebar position */
             top: 0;           /* Align to the top of the page */
             bottom: 0;        /* Stretch to the bottom of the page */
             left: 0;          /* Align to the left side */
             margin: 0px;
             width: 16.666667%; /* Corresponds to col-md-2 in Bootstrap grid */
             background-color: #343a40;
             padding-top: 10px;
             overflow-y: auto; /* Allow scrolling if content is too long */
             z-index: 1000;    /* Ensure it stays above other content */
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
             margin-top:0px;
             background-color: transparent !important;
             backdrop-filter: blur(3px);
             border: none;
         }

         /* Adjust main content to not be covered by the fixed sidebar */
         .content-wrapper {
             margin-left: 16.666667%; /* Same width as the sidebar */
             width: calc(100% - 16.666667%);
         }
</style>

<!-- Sidebar -->
 <div class=" col-md-2 sidebar">
   <div class="row ">

   <!-- Map logo -->
    <div class="col-md-4"><img src="<?php echo htmlspecialchars($logo_path); ?>" 
    class="w-20 h-20"       
    alt="Logo" 
           style="filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));"></img></div>
           <div class="col-md-8">
            <h3 class="mt-3 text-white text-center ">Her Home Hostel</h3>
        </div> </div>
            <a href="addash.php">Dashboard</a>
            <a href="roomManagement.php">Room Management</a>
            <a href="staffmanagement.php">Staff Management</a>
            <a href="hostelerManagement.php">Hosteller</a>
            <a href="setting.php">Settings</a>
            <hr class = "w-full text-white mb-0"  >
            <button class="btn w-100 "><a href="../index.php">LOG OUT</a></button>
 </div>
