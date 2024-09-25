<?php

if(isset($_POST['email']) && ($_POST['email']) &&
    isset( $_POST['role'])) {

        function text_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
    }
    $email = test_input($_POST['email']);
    $password = test_input($_POST['password']);
    $role = test_input($_POST['role']);

    if(empty($username)) {
        header("Location: ../index.php?error= Email is required");
}else { 
        header("Location: ../index.php");
    }
}