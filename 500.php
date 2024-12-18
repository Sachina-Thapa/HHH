<?php
// Error handling and logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');

// Prevent caching of error page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-text {
            background: linear-gradient(to right, #3B82F6, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-dark-background text-white min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="bg-dark-surface rounded-2xl shadow-2xl p-12 max-w-2xl mx-auto text-center">
            <div class="mb-8">
                <svg class="mx-auto h-24 w-24 text-yellow-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
            </div>

            <h1 class="text-6xl font-bold mb-4 gradient-text">500</h1>
            
            <h2 class="text-3xl font-semibold mb-4">
                Internal Server Error
            </h2>
            
            <p class="text-gray-400 mb-8">
                <?php 
                // Provide a user-friendly error message
                $error_message = "Oops! Something went wrong on our end. Our team has been notified and is working to resolve the issue.";
                echo htmlspecialchars($error_message); 
                ?>
            </p>
            
            <div class="flex justify-center space-x-4">
                <button onclick="window.location.reload();" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                    Refresh Page
                </button>
                
                <button onclick="window.location.href='/contact'" class="bg-gray-700 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    Contact Support
                </button>
            </div>

            <?php 
            // Optional: Log detailed error for administrators
            if (isset($_SERVER['SERVER_ADMIN'])) {
                $detailed_error = error_get_last();
                if ($detailed_error) {
                    error_log("500 Error Details: " . print_r($detailed_error, true));
                }
            }
            ?>
        </div>
    </div>

    <script>
        // Optional: Add some client-side error tracking
        window.addEventListener('error', function(event) {
            console.error('Uncaught error:', event.error);
            // You could send this to your error tracking service
        });
    </script>
</body>
</html>
