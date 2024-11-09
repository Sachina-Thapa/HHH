
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HerHomeHostel-About US</title>
    <?php require('inc/links.php');
          require('inc/db.php');
          require('inc/essentials.php');
          
    ?>
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

    <!-- FOR IMAGE SLIDE  -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" class="active" aria-current="true" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" class="active" aria-current="true" aria-label="Slide 3"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" class="active" aria-current="true" aria-label="Slide 4"></button>
</div>

  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="images/hostelroom.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Hostel Picture</h5>
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/singleroom.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Single Bed Room</h5>
        <p>Simple single room</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/doublebed.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Double Bed Room</h5>
        <p>Some representative placeholder content for the second slide.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/triplebed.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5>Triple Bed Room</h5>
        <p>Some representative placeholder content for the second slide.</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>  


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
          <form method ="POST" action="queries.php" id ="contactForm">
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label" style="font-weight:500;"> Your Full Name</label>
                  <input name="name" required type="text" class="form-control shadow-none">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                <label class="form-label" style="font-weight:500;"> Your Email </label>
                <input name="email" required type="email" class="form-control shadow-none">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                <label class="form-label" style="font-weight:500;"> Messsage</label>
                <textarea name="message" required  class="form-control shadow-none" rows="4" style="resize:none;"></textarea>
                </div>
              </div>
              <div class="col-md-12">
              <button type="submit" id="sendMessage" class="btn btn-warning btn-lg btn-block mt-3">Send Now</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sendMessage').click(function() {
            // Get form data
            var formData = $('#contactForm').serialize();

            // Send AJAX request
            $.ajax({
                type: 'POST',
                url: 'queries.php', 
                data: formData,
                success: function(response) {
                    // Handle success response
                    alert('Mail sent!');
                    $('#contactForm')[0].reset(); // Reset the form
                },
                error: function() {
                    // Handle error response
                    alert('Server Down! Try again later');
                }
            });
        });
    });
</script>

<?php
    require('inc/db.php'); 
    require('inc/essentials.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
        $frm_data = filteration($_POST);

        // Prepare the SQL query
        $q = "INSERT INTO `queries` (`name`, `email`, `message`, `date`, `seen`) VALUES (?, ?, ?, NOW(), 0)";
        $values = [$frm_data['name'], $frm_data['email'], $frm_data['message']];
        
        // Execute the insert function
        $res = insert($q, $values, 'sss');
        
        // Check the result
        if ($res > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Mail sent!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Server Down! Try again later']);
        }
        exit(); // Make sure to exit after handling the AJAX request
    }
?>




<!-- REACH US -->
<h2 class=" mt-5 pt-4 mb-4 text-center">Reach Us</h2>
<div class="container">
</div>
<?php require('inc/footer.php'); ?>
</body>
</html>