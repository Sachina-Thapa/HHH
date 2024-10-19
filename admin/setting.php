<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
   body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
    .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 10px;
        }
        .sidebar a {
            color: #ffffff;
            padding: 15px;
            display: block;
            text-decoration: none;
            
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .logout-btn {
            margin-top: 20px;
            background-color: #f8f9fa;
            border: none;
            color: #000;
            padding: 10px;
        }
        .table thead {
            background-color: #000;
            color: #a06666;
        }
        .table th, .table td {
            text-align: center;
        }
    .settings-card {
      background-color: rgb(255, 255, 255);
      padding: 1.5rem;
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .toggle-switch {
      display: flex;
      align-items: center;
    }
    .toggle-switch input[type="checkbox"] {
      margin-left: auto;
    }
    logo img {
      width: 10%;
      max-width: 12px;
      margin-bottom: rem;
      
    }
   
  </style>
</head>
<body>

</div>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h4 class="text-white text-center">Her Home Hostel</h4>
            <a href="addash.php">Dashboard</a>
            <a href="roomManagement.php">Room Management</a>
            <a href="staffmanagement.php">Staff management</a>
            <a href="hostelerManagement.php">Hosteller</a>
            <a href="usersquery.php">Queries</a>
            <a href="setting.php">Settings</a>
            
           <button class="btn w-100" ><a href="../index.php">LOG OUT</a></button>
        </div>

      <!-- Main Content -->
      <div class="col-md-10 p-4">
        <h2>Settings</h2>


        <!-- General Settings -->
  
        <div class="settings-card mb-4">
          <h4>General Settings</h4>

           <!-- Logo -->
           <div class="logo">
            <img id="logoPreview" src="https://via.placeholder.com/120x50.png?text=Logo" alt="Logo">
          </div>

          <div class="mb-3">
            <label for="logoUpload" class="form-label"><strong>Change Logo:</strong></label>
            <input type="file" class="form-control" id="logoUpload" accept="image/*">
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <strong>Change Password</strong>
              <form action="" class="d-flex align-items-center">
                <input type="password" class="form-control mr-5" id="currentPassword" placeholder="Current Password">
                <input type="password" class="form-control" id="newPassword" placeholder="New Password">
                <button type="submit" class="btn btn-primary ">Submit</button>
              </form>
            </div>
          
           

          </div>
          <!-- <p><strong>About us:</strong> <p>Welcome to Her Home Hostel, your trusted online platform for hassle-free hostel bookings. We are committed to providing a convenient, secure, and efficient way for students, travelers, and working professionals to find and book hostel room according to their choice. 
            Our mission is to simplify the process of finding and booking hostels, ensuring a seamless experience for users seeking affordable and comfortable accommodation. Whether you're a student looking for a long-term stay or a traveler needing short-term accommodation, we have a solution for you.</p> -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  

  <!-- Script to Preview Logo and Profile Photo Image -->
   <script>
    // Preview for Logo Upload
    document.getElementById('logoUpload').addEventListener('change', function(event) {
      const [file] = event.target.files;
      if (file) {
        const logoPreview = document.getElementById('logoPreview');
        logoPreview.src = URL.createObjectURL(file);
      }
      
    });
 </script>
</body>
</html>
