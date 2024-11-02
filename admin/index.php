<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Panel</title>
    <?php require('inc/links.php'); ?>
</head>
<body>
    <div>
        <form>
            <h4>Admin Login Panel</h4>
            <div class="mb-3">
            <input type="TEXT" class="form-control text-center" id="email" required placeholder="Enter correct E-mail"  >
            </div>
          <div class="mb-3">
            <input type="password" class="form-control" id="password" required placeholder="Enter Correct Password">
          </div>
            <button type="submit" class="btn text-white"> Login </button>
        </div>

        </form>
    </div>
    <?php require('inc/script.php')?>
</body>
</html>