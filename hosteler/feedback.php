<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require('../admin/inc/db.php');

if (!isset($_SESSION['username'])) {
    header("Location: http://localhost/hhh/index.php");
    exit();
}

$username = $_SESSION['username'];
$success_message = '';
$error_message = '';

// Get user ID
$stmt = $conn->prepare("SELECT id FROM hostelers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hid = $row['id'];
} else {
    $_SESSION['error_message'] = "User not found.";
    header("Location: http://localhost/hhh/index.php");
    exit();
}

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $ftext = $_POST['ftext'];
    $fdate = date('Y-m-d H:i:s');
    $sid = 1; // Set a default value for sid or get it from somewhere if needed

    $stmt = $conn->prepare("INSERT INTO feedback (ftext, username, hid, fdate, sid) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $ftext, $username, $hid, $fdate, $sid);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your feedback has been sent.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Get messages from session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Fetch existing feedbacks
$feedbacks = [];
$stmt = $conn->prepare("SELECT ftext, fdate FROM feedback WHERE hid = ? ORDER BY fdate DESC");
$stmt->bind_param("i", $hid);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 210px;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .feedback-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f0f0f0;
            padding: 15px 20px;
        }
        .feedback-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .feedback-item {
            border-left: 3px solid #007bff;
            margin: 10px 0;
            padding: 10px;
            background-color: #fff;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php require('inc/hsidemenu.php'); ?>

    <div class="main-content">
        <div class="feedback-container">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Feedback Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Submit Feedback</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <textarea class="form-control" name="ftext" rows="4" 
                                    placeholder="Share your thoughts..." required></textarea>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">
                            Submit Feedback
                        </button>
                    </form>
                </div>
            </div>

            <!-- Previous Feedbacks -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Your Previous Feedbacks</h4>
                </div>
                <div class="card-body feedback-list">
                    <?php if (!empty($feedbacks)): ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <div class="feedback-item">
                                <p class="mb-1"><?php echo htmlspecialchars($feedback['ftext']); ?></p>
                                <small class="text-muted">
                                    Submitted on: <?php echo date('M d, Y H:i', strtotime($feedback['fdate'])); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">No feedback submitted yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>
</html>