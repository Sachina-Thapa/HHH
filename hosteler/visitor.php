<?php
require('inc/hsidemenu.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
            margin: 0; /* Remove default margin */
            background-color: #f8f9fa; /* Light background color */
        }

        .signup-card {
            width: 100%; /* Full width for the card */
            max-width: 400px; /* Set a max width for the signup card */
            background-color: #ffffff; /* White background for the card */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            padding: 20px; /* Padding inside the card */
        }

        .card-title {
            color: #007bff; /* Bootstrap primary color for the title */
            margin-bottom: 20px; /* Space below the title */
        }

        .form-label {
            color: #495057; /* Darker color for labels */
        }

        .form-control {
            border: 1px solid #ced4da; /* Default border color */
            border-radius: 4px; /* Rounded corners for input fields */
        }

        .form-control:focus {
            border-color: #007bff; /* Change border color on focus */
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Focus shadow */
        }

        .btn-primary {
            background-color: #007bff; /* Bootstrap primary color */
            border-color: #007bff; /* Border color for the button */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
            border-color: #0056b3; /* Darker border on hover */
        }

        .mb-3 {
            margin-bottom: 15px; /* Adjust spacing between form elements */
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <h3 class="card-title">Visitor Form</h3>
        <form action="signup-user.php" method="POST" autocomplete="">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input class="form-control" type="text" name="name" id="name" placeholder="Enter your full name" required>
            </div>
            <div class="mb-3">
                <label for="relation" class="form-label">Relationship</label>
                <input class="form-control" type="text" name="relation" id="relation" placeholder="Enter your full name" required>
            </div>
            <div class="mb-3">
            <label for="reason" class="form-label">Reason of Visit</label>
            <textarea class="form-control" id="reason" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">No of Days</label>
                <input class="form-control" type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="mb-3">
                <input class="form-control button" type="submit" name="sumbit" value="Submit">
                <input class="form-control button" type="button" name="cancel" value="Cancel">
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>