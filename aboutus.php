<?php
session_start();
$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'hhh'; 
$errorMessage = '';
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userType = $_POST['user_type'];

    switch ($userType) {
        case 'Admin':
            $table = 'adminlogin';
            $dashboard = 'admin/addash.php';
            break;
        case 'Staff':
            $table = 'stafflogin';
            $dashboard = 'staffdash.php';
            break;
        case 'Hosteler':
            $table = 'hostelerlogin';
            $dashboard = 'hostelerdash.php';
            break;
        default:
            $errorMessage = "Invalid user type selected";
            break;
    }

    if (empty($errorMessage)) {
        $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            $_SESSION['username'] = $username; 
            header("Location: $dashboard");
            exit();
        } else {
            $errorMessage = "Invalid username or password.";
               }

        $stmt->close();
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HerHomeHostel-About US</title>
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

<!-- ABOUT US SECTION -->
 <section id="about" class="about-section-padding mt-5">
  <div class="container">
    <div class="row align-items-top">
      <div class="col-lg-3 col-md-12 col-12">
        <div class="about-img">
        <img src="images/logo.png" alt="" class="img-fluid">
        </div>
      </div>
      <div class="col-lg-9 col-md-12 col-12 ps-lg-0 mt-md-5">
        <div class="about-text">
        <h1>About Us</h1>
        <p class="about-paragraph" >
          Welcome to Her Home Hostel, your trusted online platform for hassle-free hostel bookings. We are committed to providing a convenient, secure, and efficient way for students, travelers, and working professionals to find and book hostel room according to their choice. 
          Our mission is to simplify the process of finding and booking hostels, ensuring a seamless experience for users seeking affordable and comfortable accommodation. Whether you're a student looking for a long-term stay or a traveler needing short-term accommodation, we have a solution for you.</p>
        </div>
      </div>
    </div>
  </div>
 </section>
 
 <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">Our Facilities</h2>
<div class="container">
  <div class="row justify-content-center"> <!-- Ensures alignment -->
    <div class="col-lg-2 col-md-4 col-sm-6 text-center bg-white rounded shadow py-4 my-3 mx-3"> 
      <img src="images/features/wifi.svg" width="100px">
      <h5 class="mt-3">Wifi</h5>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 text-center bg-white rounded shadow py-4 my-3 mx-3"> 
      <img src="images/food.jpg" width="100px">
      <h5 class="mt-3">Food</h5>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 text-center bg-white rounded shadow py-4 my-3 mx-3"> 
      <img src="images/laundry.png" width="80px">
      <h5 class="mt-3">Laundry</h5>
    </div>
  </div>
</div>
 
<!-- REACH US -->
<h2 class=" mt-5 pt-4 mb-4 text-center">Reach Us</h2>
<div class="container">
</div>
<?php require('inc/footer.php'); ?>
</body>
</html>