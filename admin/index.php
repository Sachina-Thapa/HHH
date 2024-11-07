// Add this at the top of index.php after session_start()
function redirect($url) {
    echo "
    <script>
    window.location.href='$url';
    </script>";
}

// Then use it where needed like this:
redirect('index.php');
