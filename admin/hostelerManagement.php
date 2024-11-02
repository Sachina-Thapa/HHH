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
    <style>
        .sidebar {
            margin:0px;
            height: 100vh;
            background-color: #343a40;
            padding-top: 10px;
        }
        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
<div class="container-fluid m-0">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h3 class="text-white text-center">Her Home Hostel</h3>
            <a href="addash.php">Dashboard</a>
            <a href="roomManagement.php">Room Management</a>
            <a href="staffmanagement.php">Staff Management</a>
            <a href="hostelerManagement.php">Hosteller</a>
            <a href="setting.php">Settings</a>
            <button class="btn w-100"><a href="../index.php">LOG OUT</a></button>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <h2 class="mt-4 mb-4">Hosteler Management</h2>
              <!-- Advanced Search Bar -->
              <form method="GET" action="">
                  <div class="row mb-3">
                      <div class="col-md-3">
                          <input type="text" class="form-control" name="search_term" placeholder="Search by Name, Phone or Email" value="<?php echo isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : ''; ?>">
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">Search</button>
                          <a href="hostelerManagement.php" class="btn btn-secondary">Clear</a>
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
                              <th>Actions</th>
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
                              echo "<td>";
                              if($row['status'] == 0) {
                                  echo "<button class='btn btn-success btn-sm accept-btn' data-id='" . $row['id'] . "'>Accept</button> ";
                                  echo "<button class='btn btn-danger btn-sm remove-btn' data-id='" . $row['id'] . "'>Remove</button>";
                              } else {
                                  echo "<span class='badge bg-success'>Verified</span>";
                              }
                              echo "</td>";
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

<script>
$(document).ready(function() {
    $('.accept-btn').click(function() {
        let id = $(this).data('id');
        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: {
                id: id,
                action: 'accept'
            },
            success: function(response) {
                location.reload();
            }
        });
    });

    $('.remove-btn').click(function() {
        if(confirm('Are you sure you want to remove this hosteler?')) {
            let id = $(this).data('id');
            $.ajax({
                url: 'update_status.php',
                type: 'POST',
                data: {
                    id: id,
                    action: 'remove'
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
