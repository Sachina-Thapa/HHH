


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HerHomeHostel-Contact US</title>
    <?php require('inc/links.php'); ?>
    <style>
  body {
    background-color: #909EC1; 
  }

  .contact-img img {
    border-radius: 10px; /* Optional: for rounded corners */
    width: 100%; /* Ensure it scales with the container */
    height: auto; /* Maintain aspect ratio */
}

  /* Contact Form Background and Text Color */
  .contact-form {
    background-color: beige; /* Light grayish blue background */
    color: #333; /* Text color */
    border-radius: 10px; /* Rounded corners */
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
  }

  /* Customize Input Fields */
  .contact-form input, 
  .contact-form textarea {
    background-color: #ffffff; /* White background for inputs */
    border: 1px solid #ccc; /* Light gray border */
    border-radius: 5px;
    padding: 10px;
    color: #333; /* Text color */
    width: 100%;
    margin-bottom: 15px;
  }

  /* Change Placeholder Color */
  .contact-form input::placeholder,
  .contact-form textarea::placeholder {
    color: #888; /* Light gray placeholder text */
  }

  /* Customize Submit Button */
  .contact-form button {
    background-color: #007bff; /* Blue background for button */
    color: white; /* White text */
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }

  /* Hover Effect for Submit Button */
  .contact-form button:hover {
    background-color: #0056b3; /* Darker blue on hover */
  }
</style>

</head>
<body>
<?php require('inc/header.php'); ?>
<?php require('inc/essentials.php'); ?>

 <!-- CONTACT SECTION -->
  <section id="contact" class="contact-section-padding mt-5">
  <h2 class=" mt-5 pt-4 mb-4 text-center">Contact Us</h2>
  <div class="container">
    <div class="row align-items-center">
      <!-- Contact Image on the Left -->
      <div class="col-lg-4 col-md-12 col-12">
        <div class="contact-img">
          <img src="images/contactus.jpg" alt=" " class="img-fluid">
        </div>
      </div>
      <!-- Contact Form on the Right -->
      <div class="col-lg-8 col-md-12 col-12">
        <div class="contact-form p-4">
          <form method="POST">
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <input name="name" required type="text" class="form-control" required placeholder="Your Full Name">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <input name="email" required type="email" class="form-control" required placeholder="Your Email Here">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <textarea name="message" rows="3" required class="form-control" placeholder="Your Query Here"></textarea>
                </div>
              </div>
              <div class="col-md-12">
                <button class="btn btn-warning btn-lg btn-block mt-3" name="send">Send Now</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
// Define filteration function
function filteration($data) {
    foreach ($data as $key => $value) {
        $value = trim($value); // Trim spaces
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // Prevent XSS
        if ($key == 'email') {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL); // Sanitize email
        }
        $data[$key] = $value;
    }
    return $data;
}

// Define insert function
function insert($query, $values, $types) {
    $conn = new mysqli('localhost', 'root', '', 'hhh'); // Replace with actual DB credentials
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Prepare statement
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Error in prepare: ' . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param($types, ...$values);
    
    // Execute and return result
    $result = $stmt->execute() ? 1 : 0;
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Define alert function
function alert($type, $message) {
  $color = ($type == 'success') ? 'green' : 'red';
  
  echo "
  <div style='
      position: relative;
      padding: 10px;
      border: 1px solid $color;
      background-color: light$color;
      color: $color;
      margin: 10px 0;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
  ' id='alert-box'>
      <span>$message</span>
      <span style='cursor: pointer; font-weight: bold;' onclick='document.getElementById(\"alert-box\").style.display=\"none\";'>&times;</span>
  </div>
  ";
}

?>

  <?php
    if(isset($_POST['send']))
    {
      $frm_data = filteration($_POST);
      $q="INSERT INTO `queries`(`name`, `email`, `message`) VALUES (?,?,?)";
      $values=[$frm_data['name'], $frm_data['email'], $frm_data['message']];

      $res=insert($q, $values, 'sss');
      if($res==1){
        alert('success','Mail sent!');
      }
      else{
        alert('error','Server Down! Try again later.');
      }
    }

  ?>



<!-- REACH US -->
<h2 class=" mt-5 pt-4 mb-4 text-center">Reach Us</h2>
<div class="container">
</div>
<?php require('inc/footer.php'); ?>
</body>
</html>