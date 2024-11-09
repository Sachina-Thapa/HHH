<?php
require_once 'db.php'; // Ensure db.php is included only once

// Function to redirect to a given URL
        function redirect($url) {
            echo "
            <script>
            window.location.href='$url';
            </script>";
            exit;
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

?>
