<?php

    // Function to redirect to a given URL
    function redirect($url) {
        echo "
        <script>
        window.location.href='$url';
        </script>";
    }

    // Function to display alert messages
    function alert($type, $msg) {
        $bs_class = ($type == "success") ? "alert-success" : "alert-danger";

        echo <<<alert
                <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
                <strong class="me-3">$msg</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        alert;
    }

    // Function to sanitize input data
    function filteration($data) {
        $filtered_data = [];
        foreach ($data as $key => $value) {
            $filtered_data[$key] = htmlspecialchars(stripslashes(trim($value)));
        }
        return $filtered_data;
    }

// Function to insert data into the database
function insert($query, $values, $types) {
    global $conn;

    // Prepare the statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param($types, ...$values);

    // Execute the statement
    $result = $stmt->execute();

    // Check for execution errors
    if (!$result) {
        die("Execution failed: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();

    // Return the result of the execution
    return $result;
}

// Optional: Function to update data
function update($query, $values, $types) {
    global $conn;

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();

    if (!$result) {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();
    return $result;
}

?>
