<?php
require('../inc/db.php'); // Ensure db.php initializes a $mysqli variable

// Fetch all feedback
if (isset($_POST['get_feedback'])) {
    $res = $mysqli->query("SELECT * FROM feedback");

    if (!$res) {
        die("Query failed: " . $mysqli->error);
    }

    $i = 1;
    $data = "";

    while ($row = $res->fetch_assoc()) {
        $date = date("d-m-Y", strtotime($row['fdate']));
        $data .= "
        <tr>
            <td>$i</td>
            <td>{$row['username']}</td>
            <td>{$row['ftext']}</td>
            <td>$date</td>
            <td><button onclick='remove_feedback({$row['fid']})' class='btn btn-danger btn-sm shadow-none'>Delete</button></td>
        </tr>
        ";
        $i++;
    }
    echo $data;
}

// Remove feedback logic
if (isset($_POST['remove_feedback'])) {
    $fid = $_POST['fid']; // Get the feedback ID from POST data
    $stmt = $mysqli->prepare("DELETE FROM feedback WHERE fid = ?");
    $stmt->bind_param('i', $fid);

    if ($stmt->execute()) {
        echo 1; // Successfully deleted
    } else {
        echo 0; // Failed to delete
    }
}
?>
