<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HerHomeHostel</title>
    <!-- ADD BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- ADD POPPINS FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- ADD BOOTSTRAP ICONS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
<!-- NAVIGATION BAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand me-6 fq-bold fs-3" href="index.html">Her Home Hostel</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link me-2" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="#">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="#">Contact Us</a>
        </li>
        <li class="nav-item">
          <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
        </li>
        <li class="nav-item">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="border shadow p-3 rounded" 
        action="php/checklogin.php"
        method="post">
          <h1 class="text-center p-3">User Login</h1>
          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" required placeholder="Enter correct E-mail">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" id="password" required placeholder="Enter Correct Password">
          </div> 
          <div class="mb-0">
            <label class="form-label">Select User Type</label>
          </div>
          <select class="form-select mb-3" name="role"  aria-label="Default select example">
            <option selected>Admin</option>
            <option value="1">Staff</option>
            <option value="2">Hosteler</option>
          </select>
          <div class="d-flex align-items-center jsutify-content-between mb-2"> 
          <button type="submit" class="btn btn-primary">LOGIN</button>
          <a href="javascript: void(0)"class="text-secondary text-decoration-none ms-auto">Forgot Password?</a>
        </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Register Hosteler</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap th-base"></span>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 ps-0 mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control shadow-none">
              </div>
              <div class="col-md-6 p-0">
                <label class="form-label">Email</label>
                <input type="email" class="form-control shadow-none">
              </div>
              <div class="col-md-6 ps-0 mb-3">
                <label class="form-label">Phone Number</label>
                <input type="number" class="form-control shadow-none">
              </div>
              <div class="col-md-6 p-0 mb-3">
                <label class="form-label">Picture</label>
                <input type="file" class="form-control shadow-none">
              </div>
              <div class="col-md-12 ps-6 mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control shadow" rows="1"></textarea>
              </div>
              <div class="col-md-6 p-0 mb-3">
                <label class="form-label">Pincode</label>
                <input type="number" class="form-control shadow-none">
              </div>
              <div class="col-md-6 ps-0 mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control shadow-none">
              </div>
              <div class="col-md-6 p-0 mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control shadow-none">
              </div>
              <div class="col-md-6 p-0 mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control shadow-none">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Register</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
          <form action="#" class="m-auto">
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <input type="text" class="form-control" required placeholder="Your Full Name">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <input type="email" class="form-control" required placeholder="Your Email Here">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <textarea rows="3" required class="form-control" placeholder="Your Query Here"></textarea>
                </div>
              </div>
              <div class="col-md-12">
                <button class="btn btn-warning btn-lg btn-block mt-3">Send Now</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

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
<div class="container-fluid bg-white mt-5">
<div class="row">
<div class="col-lg-4 p-4">
<h3 class="h-font fw-bold fs-3 mb-2">Her Home Hostel</h3>
<p>
hehehahahhahahaha</p>
</div>
<div class="col-lg-4 p-4">
<h5 class="mb-3">Links</h5>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a> <br>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">About Us</a> <br>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">Contact Us</a> <br>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a>
</div>
<div class="col-lg-4 p-4">
<h5 class="mb-3">Follow us</h5>
<a href="#" class="d-inline-block text-dark text-decoration-none mb-2"> <i class="bi bi-twitter me-1"></i> Twitter
</a><br>
<a href="#" class="d-inline-block text-dark text-decoration-none mb-2"> <i class="bi bi-facebook me-1"></i> Facebook
</a><br>
<a href="#" class="d-inline-block text-dark text-decoration-none mb-2"> <i class="bi bi-instagram ne-1"></i> instagram
</a><br>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>