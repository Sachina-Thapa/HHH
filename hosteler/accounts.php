<?php
session_start();
require('../admin/inc/db.php'); // Ensure you have the correct database connection
require('inc/hsidemenu.php'); // Include the sidebar if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container-fluid col-md-10 p-4">
    <div class="row">
        <div class="col-md-10">
            <h2 class="mt-4 mb-4">My Account</h2>

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
