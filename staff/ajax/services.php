<?php
require('../inc/db.php'); // Ensure this path is correct and db.php exists

if (isset($_POST['add_service'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    $query = "INSERT INTO `services` (`name`, `price`) VALUES (?, ?)";
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

if (isset($_POST['load_services'])) {
    $query = "SELECT * FROM `services`";
    $result = $mysqli->query($query);

    if ($result) {
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            echo <<<HTML
            <tr>
                <td>{$i}</td>
                <td>{$row['name']}</td>
                <td>{$row['price']}</td>
                <td>
                    <button type="button" onclick="deleteService({$row['id']})" class="btn btn-danger btn-sm">
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

if (isset($_POST['delete_service'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM `services` WHERE `id` = ?";
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
