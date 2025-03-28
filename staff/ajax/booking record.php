<?php
require('../inc/db.php');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

    } else {
        echo "No action parameter received.";
    }
} else {
    echo "Invalid request method.";
}
?>