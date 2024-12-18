<?php
session_start();
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "hhh";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteler Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
<style>
.search-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.search-form .form-control {
    border: 1px solid #ced4da;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.search-form .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.search-form .btn {
    padding: 10px 20px;
    margin-right: 10px;
    font-weight: 500;
}
  .search-form .btn-primary {
    background-color: #4169E1 !important;
      border: none;
      border-radius: 8px;
      color: #fff;
      transition: all 0.3s ease;
  }
.search-form .btn-primary:hover {
    background-color: #0b5ed7;
    transform: translateY(-1px);
}


</style>

<div class="container-fluid m-0">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once('inc/sideMenu.php'); ?>

        <!-- Main Content -->
        <div class="col-md-10 content-wrapper py-4 px-4">
            <h2 class="mt-4 mb-4">Hosteler Management</h2>
              <!-- Advanced Search Bar -->
              <form method="GET" action="" class="search-form">
                  <div class="row mb-3">
                      <div class="col-md-3">
                          <input type="text" class="form-control" name="search_term" placeholder="Search by Name, Phone or Email" value="<?php echo isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : ''; ?>">
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">Search</button>
                     </div>
                  </div>
              </form>

              <!-- Table Query Section -->
              <?php
              if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
                  $search_term = $_GET['search_term'];
                  $sql = "SELECT id, name, phone_number, address, email, status 
                          FROM hostelers 
                          WHERE name LIKE CONCAT('%', ?, '%') 
                          OR phone_number LIKE CONCAT('%', ?, '%') 
                          OR email LIKE CONCAT('%', ?, '%')";

                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("sss", $search_term, $search_term, $search_term);
                  $stmt->execute();
                  $result = $stmt->get_result();
              } else {
                  $sql = "SELECT id, name, phone_number, address, email, status FROM hostelers";
                  $result = $conn->query($sql);
              }

              // Debug information
              if (!$result) {
                  echo "Query error: " . $conn->error;
              }
              ?>

              <!-- Table Display Section -->
              <div class="table-responsive">
                  <table class="table table-striped table-hover">
                      <thead class="table-dark">
                          <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Phone Number</th>
                              <th>Address</th>
                              <th>Email</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php
                      if ($result && $result->num_rows > 0) {
                          while($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                              echo "</tr>";
                          }
                      } else {
                          echo "<tr><td colspan='6' class='text-center'>No hostelers found</td></tr>";
                      }
                      ?>
                      </tbody>
                  </table>
              </div>
          </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>