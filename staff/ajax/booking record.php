<?php
require('../inc/db.php');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// if (isset($_POST['get_newbooking'])) {
//     $res = $mysqli->query("SELECT * FROM booking");

//     if (!$res) {
//         die("Query failed: " . $mysqli->error);
//     }

//     $i = 1;
//     $data = "";

//     while ($row = $res->fetch_assoc()) {
//         $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
//         if (!$row['status']) {
//             $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
//         }
//         $date = date("d-m-Y", strtotime($row['created_at']));
//         $data .= "
//         <tr>
//             <td>$i</td>
//             <td>{$row['bid']}</td>
//             <td>{$row['name']}</td>
//             <td>{$row['phone_number']}</td>
//             <td>{$row['email']}</td>
//             <td>{$row['address']}</td>
//             <td>{$row['rtype']}</td>
//             <td>{$row['bdate']}</td>
//             <td>$status</td>
//             <td>$action</td>
//         </tr>
//         ";
//         $i++;
//     }
//     echo $data;
// }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Handling Confirm Booking
        // if ($action == 'confirm') {
        //     $bid = intval($_POST['bid']);
        //     $id = intval($_POST['id']);

        //     // Log the incoming data for debugging
        //     error_log("Confirming booking with bid: $bid and id: $id");

        //     // Update booking status to 'confirmed'
        //     $update_query = "UPDATE booking SET bstatus = 'confirmed' WHERE bid = ?";
        //     if ($stmt = $mysqli->prepare($update_query)) {
        //         $stmt->bind_param('i', $bid);
        //         if ($stmt->execute()) {
        //             if ($stmt->affected_rows > 0) {
        //                 // Insert notification
        //                 $message = "Your booking has been confirmed!";
        //                 $notif_query = "INSERT INTO notifications (id, message) VALUES (?, ?)";
        //                 if ($notif_stmt = $mysqli->prepare($notif_query)) {
        //                     $notif_stmt->bind_param('is', $id, $message);
        //                     if ($notif_stmt->execute()) {
        //                         echo '1'; // Successfully confirmed
        //                     } else {
        //                         echo "Error inserting notification: " . $mysqli->error;
        //                     }
        //                 } else {
        //                     echo "Error preparing notification insert query: " . $mysqli->error;
        //                 }
        //             } else {
        //                 echo "No rows affected. Booking may not exist.";
        //             }
        //         } else {
        //             echo "Error executing booking update query: " . $stmt->error;
        //         }
        //     } else {
        //         echo "Error preparing booking update query: " . $mysqli->error;
        //     }
        // }

        // Handling Cancel Booking
        // if ($action == 'cancel') {
        //     $bid = intval($_POST['bid']);
        //     $id = intval($_POST['id']);

        //     // Log the incoming data for debugging
        //     error_log("Canceling booking with bid: $bid and id: $id");

        //     // Update booking status to 'canceled'
        //     $update_query = "UPDATE booking SET bstatus = 'canceled' WHERE bid = ?";
        //     if ($stmt = $mysqli->prepare($update_query)) {
        //         $stmt->bind_param('i', $bid);
        //         if ($stmt->execute()) {
        //             if ($stmt->affected_rows > 0) {
        //                 // Insert notification
        //                 $message = "Your booking has been canceled.";
        //                 $notif_query = "INSERT INTO notifications (id, message) VALUES (?, ?)";
        //                 if ($notif_stmt = $mysqli->prepare($notif_query)) {
        //                     $notif_stmt->bind_param('is', $id, $message);
        //                     if ($notif_stmt->execute()) {
        //                         echo '1'; // Successfully canceled
        //                     } else {
        //                         echo "Error inserting notification: " . $mysqli->error;
        //                     }
        //                 } else {
        //                     echo "Error preparing notification insert query: " . $mysqli->error;
        //                 }
        //             } else {
        //                 echo "No rows affected. Booking may not exist.";
        //             }
        //         } else {
        //             echo "Error executing booking cancel query: " . $stmt->error;
        //         }
        //     } else {
        //         echo "Error preparing booking cancel query: " . $mysqli->error;
        //     }
        // }
    } else {
        echo "No action parameter received.";
    }
} else {
    echo "Invalid request method.";
}
?>