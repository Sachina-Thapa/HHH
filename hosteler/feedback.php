<?php
require('inc/hsidemenu.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hosteler Panel - Feedback</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .main-content {
      flex-grow: 1; /* This will take the remaining space */
      padding: 20px; /* Add some padding */
      display: flex;
      flex-direction: column; /* Stack children vertically */
      align-items: center; /* Center items horizontally */
    }

    .feedback-card, .pfeedback-card {
      width: 100%; /* Full width for the card */
      max-width: 600px; /* Optional: Set a max width for the feedback card */
      background-color: #ffffff; /* White background for the card */
      border-radius: 8px; /* Rounded corners */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
      padding: 20px; /* Padding inside the card */
      margin-bottom: 20px; /* Space between cards */
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
  <div class="col-md-10 p-4">
    <div class="row">
      <div class="col-md-10 main-content">
        <!-- New Feedback Section -->
        <div class="feedback-card">
          <h3 class="ab-0 h-font card-title">Feedback</h3>
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <form id="feedbackForm">
                <div class="mb-3">
                  <label for="feedback" class="form-label">Your Feedback</label>
                  <textarea class="form-control" id="feedback" rows="4" placeholder="Enter your feedback here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Previous Feedback Section -->
        <div class="pfeedback-card">
          <h3 class="ab-0 h-font card-title">Previous Feedback</h3>
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <form id="pfeedbackForm">
                <div class="mb-3">
                  <label for="pfeedback" class="form-label">Your Feedback</label>
                  <textarea class="form-control" id="pfeedback" rows="4" placeholder="Enter your feedback here..."></textarea>
                </div>
                 <button type="submit" class="btn btn-primary">Delete Feedback</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>