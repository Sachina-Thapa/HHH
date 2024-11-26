<!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Dashboard</title>
 
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
 
     <!-- Chart.js -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
     <!-- Custom CSS -->
     <style>
         body {
             background-color: #f5f5f5;
             font-family: Arial, sans-serif;
         }
 
         /* Sidebar CSS */

         img, svg {
         vertical-align: middle;
         height: 60px;
        }
         .sidebar {
             margin: 0px;
             height: 140vh;
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
                  <!-- Logo -->
         <div class="text-center mb-3">
         <img src="../images/logoo.png" alt="Logo">
      </div>
                 <a href="addash.php">Dashboard</a>
                 <a href="admanagement.php">Room Management</a>
                 <a href="adstaff.php">Staff management</a>
                 <a href="adhosteller.php">Hosteller</a>
                 <a href="setting.php">Settings</a>
                 <button class="logout-btn w-100">LOG OUT</button>
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
              <strong>Site Title:</strong> Her Home Hostel
            </div>
            <button class="btn btn-outline-secondary">Edit</button>

          </div>
          <p><strong>About us:</strong>  <p class="text-muted mb-4">
            Her Home Hostel is more than just a place to stay; it's a community where travelers from all over the world come
            together. Our modern facilities and welcoming atmosphere ensure that your stay is comfortable, memorable, and full of new experiences.
          </p>
          <p class="text-muted mb-4">
           We are committed to providing a convenient, secure, and efficient way for students, travelers, and working professionals to find and book hostel room according to their choice. 
          Our mission is to simplify the process of finding and booking hostels, ensuring a seamless experience for users seeking affordable and comfortable accommodation.
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