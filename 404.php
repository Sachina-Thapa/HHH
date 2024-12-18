<?php
// Advanced 404 Error Page
header("HTTP/1.1 404 Not Found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #f4f4f4;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .error-container {
            text-align: center;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 600px;
            position: relative;
            overflow: hidden;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .error-message {
            font-size: 24px;
            color: #333;
            margin-bottom: 30px;
        }

        .search-box {
            display: flex;
            margin: 20px auto;
            max-width: 400px;
        }

        .search-input {
            flex-grow: 1;
            padding: 10px;
            border: 2px solid var(--primary-color);
            border-radius: 5px 0 0 5px;
        }

        .search-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background-color: var(--secondary-color);
        }

        .quick-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .quick-link {
            text-decoration: none;
            color: var(--primary-color);
            padding: 10px 15px;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .quick-link:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(45deg, #3498db, #2ecc71);
            opacity: 0.1;
            animation: moveBackground 10s infinite alternate;
        }

        @keyframes moveBackground {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.2) rotate(10deg); }
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Oops! Page Not Found</div>
        
     

        <div class="quick-links">
    <a href="./index.php" class="quick-link">Home</a>
    <a href="javascript:history.back()" class="quick-link">Go Back</a>
</div> 
    </div>

    <script>
        // Advanced JavaScript for 404 page
        document.addEventListener('DOMContentLoaded', () => {
            // Track 404 errors
            fetch('/log-404', {
                method: 'POST',
                body: JSON.stringify({
                    url: window.location.href,
                    referrer: document.referrer
                })
            });

            // Suggest similar pages
            function suggestSimilarPages(searchTerm) {
                // Implement fuzzy search or similar page suggestion logic
                // This could be connected to a backend service
            }
        });
    </script>
</body>
</html>
