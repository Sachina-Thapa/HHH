<?php
require('../inc/db.php');
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
            $status = "<button onclick='toggle_status($row[id], 1)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
        }
        $data .= "
        <tr>
            <td>$i</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phone_number']}</td>
            <td>{$row['address']}</td>
            <td>{$row['date_of_birth']}</td>
            <td>$status</td>
            <td>{$row['created_at']}</td>
            <td>
                <button onclick='viewhosteler({$row['id']})' class='btn btn-info btn-sm'>View</button>
                <button onclick='remove_hosteler({$row['id']})' class='btn btn-danger btn-sm'>Remove</button>
            </td>
        </tr>
        ";
        $i++;
    }
    echo $data;
    exit;
}

// Fetch hosteler details by ID
if (isset($_POST['viewhosteler'])) {
    $frm_data = filteration($_POST);
    $stmt = $mysqli->prepare("SELECT * FROM hostelers WHERE id = ?");
    $stmt->bind_param('i', $frm_data['viewhosteler']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $hosteler = $res->fetch_assoc();
        echo json_encode($hosteler);
    } else {
        echo json_encode(null);
    }
    exit;
}

// Toggle hosteler status
if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);
    $stmt = $mysqli->prepare("UPDATE hostelers SET status = ? WHERE id = ?");
    $stmt->bind_param('ii', $frm_data['value'], $frm_data['toggle_status']);
    $stmt->execute();
    echo 1; // Success
    exit;
}

// Remove hosteler
if (isset($_POST['remove_hosteler'])) {
    $frm_data = filteration($_POST);
    $stmt = $mysqli->prepare("DELETE FROM hostelers WHERE id = ?");
    $stmt->bind_param('i', $frm_data['id']);
    $stmt->execute();
    echo 1; // Success
    exit;
}

// Search hosteler
if (isset($_POST['search_hosteler'])) {
    $frm_data = filteration($_POST);
    $stmt = $mysqli->prepare("SELECT * FROM hostelers WHERE name LIKE ?");
    $search_term = "%" . $frm_data['name'] . "%";
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $res = $stmt->get_result();

    $i = 1;
    $data = "";

    while ($row = $res->fetch_assoc()) {
        $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggle_status($row[id], 1)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
        }
        $data .= "
        <tr>
            <td>$i</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phone_number']}</td>
            <td>{$row['address']}</td>
            <td>{$row['date_of_birth']}</td>
            <td>$status</td>
            <td>{$row['created_at']}</td>
            <td>
              <button onclick='viewhosteler({$row['id']})' class='btn btn-info btn-sm'>View</button>
              <button onclick='remove_hosteler({$row['id']})' class='btn btn-danger btn-sm'>Remove</button>
            </td>
        </tr>
        ";
        $i++;
    }
    echo $data;
    exit;
}
?>