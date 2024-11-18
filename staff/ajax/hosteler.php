<?php
require('../inc/db.php'); // Ensure db.php initializes a $mysqli variable

function filteration($data) {
    // Sanitize each element in the POST data array
    $filtered_data = [];
    foreach ($data as $key => $value) {
        $filtered_data[$key] = htmlspecialchars(trim($value), ENT_QUOTES);
    }
    return $filtered_data;
}

// Fetch all hostelers
if (isset($_POST['get_hosteler'])) {
    $res = $mysqli->query("SELECT * FROM hostelers");

    if (!$res) {
        die("Query failed: " . $mysqli->error);
    }

    $i = 1;
    $data = "";

    while ($row = $res->fetch_assoc()) {
        $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
        }
        $date = date("d-m-Y", strtotime($row['created_at']));
        $data .= "
        <tr>
            <td>$i</td>
            <td>
                <img src='{$row['picture_path']}' width='55px'>
                <br>
                {$row['name']}
            </td>
            <td>{$row['email']}</td>
            <td>{$row['phone_number']}</td>
            <td>{$row['address']}</td>
            <td>{$row['date_of_birth']}</td>
            <td>$status</td>
            <td>$date</td>
            <td><button onclick='remove_hosteler({$row['id']})' class='btn btn-danger btn-sm shadow-none'>Delete</button></td>
        </tr>
        ";
        $i++;
    }
    echo $data;
}

// Toggle status logic
if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);
    $stmt = $mysqli->prepare("UPDATE hostelers SET status = ? WHERE id = ?");
    $stmt->bind_param('ii', $frm_data['value'], $frm_data['toggle_status']);

    if ($stmt->execute()) {
        echo 1;
    } else {
        echo 0;
    }
}

// Remove hosteler logic
if (isset($_POST['remove_hosteler'])) {
    $frm_data = filteration($_POST);
    $stmt = $mysqli->prepare("DELETE FROM hostelers WHERE id = ?");
    $stmt->bind_param('i', $frm_data['id']);

    if ($stmt->execute()) {
        echo 1;
    } else {
        echo 0;
    }
}

// Search hosteler logic
if (isset($_POST['search_hosteler'])) {
    $frm_data = filteration($_POST);
    $query = "SELECT * FROM hostelers WHERE name LIKE ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    $searchTerm = "%{$frm_data['name']}%";
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $res = $stmt->get_result();

    $i = 1;
    $data = "";

    while ($row = $res->fetch_assoc()) {
        $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
        }
        $date = date("d-m-Y", strtotime($row['created_at']));
        $data .= "
        <tr>
            <td>$i</td>
            <td>
                <img src='{$row['picture_path']}' width='55px'>
                <br>
                {$row['name']}
            </td>
            <td>{$row['email']}</td>
            <td>{$row['phone_number']}</td>
            <td>{$row['address']}</td>
            <td>{$row['date_of_birth']}</td>
            <td>$status</td>
            <td>$date</td>
            <td><button onclick='remove_hosteler({$row['id']})' class='btn btn-danger btn-sm shadow-none'>Delete</button></td>
        </tr>
        ";
        $i++;
    }
    echo $data;
}
?>
