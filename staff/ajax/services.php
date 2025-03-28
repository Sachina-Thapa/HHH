<?php
require('../inc/db.php'); // Ensure this path is correct and db.php exists

// Add a new service
if (isset($_POST['add_service'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    $query = "INSERT INTO services (name, price) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ss", $name, $price);
        echo $stmt->execute() ? 1 : 0;
        $stmt->close();
    } else {
        echo 0;
    }
    exit;
}

// Load all services
if (isset($_POST['load_services'])) {
    $query = "SELECT * FROM services";
    $result = $mysqli->query($query);

    if ($result) {
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            echo <<<HTML
            <tr id="service-row-{$row['seid']}">
                <td>{$i}</td>
                <td><span id="name-{$row['seid']}">{$row['name']}</span><input type="text" id="name-input-{$row['seid']}" class="form-control d-none" value="{$row['name']}"></td>
                <td><span id="price-{$row['seid']}">{$row['price']}</span><input type="text" id="price-input-{$row['seid']}" class="form-control d-none" value="{$row['price']}"></td>
                <td>
                    <button type="button" onclick="editService({$row['seid']})" class="btn btn-warning btn-sm" id="edit-btn-{$row['seid']}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button type="button" onclick="saveService({$row['seid']})" class="btn btn-success btn-sm d-none" id="save-btn-{$row['seid']}">
                        <i class="bi bi-save"></i> Save
                    </button>
                    <button type="button" onclick="deleteService({$row['seid']})" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            </tr>
            HTML;
            $i++;
        }
    }
    exit;
}

// Update service
if (isset($_POST['update_service'])) {
    $id = $_POST['seid'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $query = "UPDATE services SET name = ?, price = ? WHERE seid = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ssi", $name, $price, $id);
        echo $stmt->execute() ? 1 : 0;
        $stmt->close();
    } else {
        echo 0;
    }
    exit;
}

// Delete service
if (isset($_POST['delete_service'])) {
    $id = $_POST['seid'];

    $query = "DELETE FROM services WHERE seid = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? 1 : 0;
        $stmt->close();
    } else {
        echo 0;
    }
    exit;
}
?>