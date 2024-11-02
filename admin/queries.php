<?php
require('../admin/inc/db.php');
require('../admin/inc/essentials.php');

// Handle AJAX delete request
if (isset($_POST['del'])) {
    $sr_no = $_POST['del'];

    $delete_query = "DELETE FROM `queries` WHERE `sr_no` = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $sr_no);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    exit;
}

// Handle AJAX mark as read request
if (isset($_POST['seen'])) {
    $sr_no = $_POST['seen'];

    $update_query = "UPDATE `queries` SET `seen` = 1 WHERE `sr_no` = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('i', $sr_no);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queries</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .seen-row {
            opacity: 0.5; /* Makes the row look shadowed when marked as read */
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
                <a href="staffmanagement.php">Staff management</a>
                <a href="hostelerManagement.php">Hosteller</a>
                <a href="queries.php">Queries</a>
                <a href="setting.php">Settings</a>
                <button class="btn w-100"><a href="../index.php">LOG OUT</a></button>
            </div>
            <div class="col-md-10">
                <h2 class="mt-4 mb-4">User Queries</h2>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
                            <table class="table table-hover border">
                                <thead class="sticky-top">
                                    <tr class="bg-dark text-light">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col" width="30%">Message</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $q = "SELECT * FROM `queries` ORDER BY `sr_no` DESC"; 
                                        
                                        $data = mysqli_query($conn, $q);

                                        if ($data === false) {
                                            die("Error in query: " . mysqli_error($conn));
                                        }
                                        $i = 1;

                                        while ($row = mysqli_fetch_assoc($data)) {
                                            $row_class = $row['seen'] ? 'seen-row' : '';
                                            $seen_button = $row['seen'] ? '' : "<button onclick='markAsRead({$row['sr_no']})' class='btn btn-sm rounded-pill btn-primary'>Mark as Read</button>";
                                            $delete_button = "<button onclick='deleteRecord({$row['sr_no']})' class='btn btn-sm rounded-pill btn-danger'>Delete</button>";

                                            echo <<<query
                                            <tr id="row-{$row['sr_no']}" class="$row_class">
                                                <td>$i</td>
                                                <td>{$row['name']}</td>
                                                <td>{$row['email']}</td>
                                                <td>{$row['message']}</td>
                                                <td>{$row['date']}</td>
                                                <td>$seen_button $delete_button</td>
                                            </tr>
                                            query;
                                            $i++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AJAX Scripts for Delete and Mark as Read -->
    <script>
        function deleteRecord(sr_no) {
            $.ajax({
                url: 'queries.php',
                type: 'POST',
                data: { del: sr_no },
                success: function(response) {
                    if (response.trim() === "success") {
                        $("#row-" + sr_no).remove();
                    } else {
                        alert("Failed to delete record");
                    }
                },
                error: function() {
                    alert("Error deleting record");
                }
            });
        }

        function markAsRead(sr_no) {
            $.ajax({
                url: 'queries.php',
                type: 'POST',
                data: { seen: sr_no },
                success: function(response) {
                    if (response.trim() === "success") {
                        $("#row-" + sr_no).addClass("seen-row"); // Shadow effect
                        $("#row-" + sr_no).find('.btn-primary').remove(); // Remove "Mark as Read" button
                    } else {
                        alert("Failed to mark as read");
                    }
                },
                error: function() {
                    alert("Error marking as read");
                }
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
