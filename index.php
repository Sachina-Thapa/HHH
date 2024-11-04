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
            $dashboard = 'staff/staffdash.php';
            break;
        case 'Hosteler':
            $table = 'hostelerlogin';
            $dashboard = 'hosteler/overview.php';
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
  <title>HerHomeHostel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .carousel-img {
      transition: opacity 1s ease-in-out;
    }

    .hidden-menu {
      display: none;
    }

    .active-slide {
      opacity: 1;
    }

    .inactive-slide {
      opacity: 0;
      position: absolute;
      top: 0;
      left: 0;
    }
     /* Navbar Customizations */
    .navbar {
      background-color:#36454F!important;
    }
    .navbar-brand img {
      height: 40px;
      width: auto;
      margin-right: 10px;
    }
    .navbar-nav .nav-link {
      color: #fff;
      font-weight: 500;
      padding: 0.5rem 1rem;
      transition: color 0.3s ease-in-out;
    }
    .navbar-nav .nav-link:hover {
      color: #0d6efd;
    }

    /* Carousel and content styling */
    .carousel-img {
      transition: opacity 1s ease-in-out;
    }

    .hidden-menu {
      display: none;
    }

    .active-slide {
      opacity: 1;
    }

    .inactive-slide {
      opacity: 0;
      position: absolute;
      top: 0;
      left: 0;
    }
  </style>
</head>
<body class="min-vh-100 bg-light">

  <!-- NAVIGATION BAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container-fluid">
    <!-- Logo in the Navbar -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="images/logoo.png" alt="Logo"> 
      <span>Her Home Hostel</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link me-2" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="#about">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="#contact">Contact Us</a>
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
                    <form class="border shadow p-3 rounded" method="POST" action="">
                        <h1 class="text-center p-3">User Login</h1>
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username" required placeholder="Enter Username">
                        </div>
                        <div class="mb-3">

                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password" required placeholder="Enter Password">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Select User Type</label>
                        </div>
                        <select class="form-select mb-3" name="user_type" required>
                            <option selected disabled>Select User Type</option>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                            <option value="Hosteler">Hosteler</option>
                        </select>
                        <div class="d-flex align-items-center justify-content-between mb-2"> 
                            <button type="submit" class="btn btn-primary">LOGIN</button>
                            <a href="javascript: void(0)" class="text-secondary text-decoration-none ms-auto">Forgot Password?</a>
                        </div>
                    </form>
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
        <div class="d-flex">
          <button class="btn btn-primary me-2">Login</button>
          <button class="btn btn-primary">Register</button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section with Gallery -->
  <section class="position-relative vh-100">
  <div class="position-absolute w-100 h-100">
      <img id="slide1" src="images/Hostel.jpg?height=600&width=800" class="carousel-img active-slide w-100 h-100 object-cover" alt="Slide 1">
      <img id="slide2" src="images/single.jpg?height=600&width=800" class="carousel-img active-slide w-100 h-100 object-cover" alt="Slide 2">
      <img id="slide3" src="images/double.jpg?height=600&width=800" class="carousel-img inactive-slide w-100 h-100 object-cover" alt="Slide 3">
      <img id="slide4" src="images/triple.jpg?height=600&width=800" class="carousel-img inactive-slide w-100 h-100 object-cover" alt="Slide 4">
    </div>
    <div class="position-absolute top-0 bottom-0 start-0 end-0 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
      <div class="text-center text-white">
        <h1 class="display-3 fw-bold mb-4">Welcome to Her Home Hostel</h1>
        <p class="fs-4 mb-4">Experience comfort and community in our modern hostels</p>
        <button class="btn btn-primary btn-lg">Book Now</button>
      </div>
    </div>
    <button id="prevSlide" class="position-absolute start-0 top-50 translate-middle-y btn btn-outline-light">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
      </svg>
    </button>
    <button id="nextSlide" class="position-absolute end-0 top-50 translate-middle-y btn btn-outline-light">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
    </button>
  </section>

  <!-- About Us -->
  <section id="about" class="py-5 bg-white">
    <div class="container">
      <h2 class="text-center fs-2 fw-bold mb-4">About Us</h2>
      <div class="row align-items-center">
        <div class="col-md-6 mb-4">
          <img src="images/aboutus.png?height=200&width=200" alt="About Us" class="rounded shadow-lg img-fluid">
        </div>
        <div class="col-md-6">
          <p class="text-muted mb-4">
            Her Home Hostel is more than just a place to stay; it's a community where travelers from all over the world come
            together. Our modern facilities and welcoming atmosphere ensure that your stay is comfortable, memorable, and full of new experiences.
          </p>
          <p class="text-muted mb-4">
           We are committed to providing a convenient, secure, and efficient way for students, travelers, and working professionals to find and book hostel room according to their choice. 
          Our mission is to simplify the process of finding and booking hostels, ensuring a seamless experience for users seeking affordable and comfortable accommodation.
          <button class="btn btn-primary">Learn More</button>
        </div>
      </div>
    </div>
  </section>

  <!-- Facilities Section -->
  <section id="Facilities" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center fs-2 fw-bold mb-4">Our Facilities</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h3 class="card-title fs-5">Budget-Friendly Stays</h3>
              <p class="card-text text-muted">Enjoy a cozy stay without breaking the bank.</p>
              <a href="#" class="btn btn-outline-primary w-100">Learn More</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h3 class="card-title fs-5">Healthy Food</h3>
              <p class="card-text text-muted">Healthy meals to keep you energized at the hostel.</p>
              <a href="#" class="btn btn-outline-primary w-100">Learn More</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h3 class="card-title fs-5">Peaceful Environment</h3>
              <p class="card-text text-muted">Experience a peaceful stay with comfort and calm all around.</p>
              <a href="#" class="btn btn-outline-primary w-100">Learn More</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h3 class="card-title fs-5">Laundry</h3>
              <p class="card-text text-muted">Quick and easy laundry right at the hostel.</p>
              <a href="#" class="btn btn-outline-primary w-100">Learn More</a>
      </div>
    </div>      
   </div>
   <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h3 class="card-title fs-5">Wi-fi</h3>
              <p class="card-text text-muted">Stay connected with fast, free Wi-Fi at the hostel.</p>
              <a href="#" class="btn btn-outline-primary w-100">Learn More</a>
   </div>
   </div>  
  </section>

  <!-- Choose Your Room -->
  <section id="rooms" class="py-5 bg-white">
    <div class="container">
      <h2 class="text-center fs-2 fw-bold mb-4">Choose Your Perfect Hostel Room</h2>
      <div class="d-flex flex-wrap justify-content-center gap-4">
        <div class="bg-primary text-white rounded-circle d-flex flex-column justify-content-center align-items-center" style="width: 150px; height: 150px;">
          <h3 class="fs-6 mb-1">Single Bed</h3>
          <p class="fs-7">From Rs 1000</p>
        </div>
        <div class="bg-primary text-white rounded-circle d-flex flex-column justify-content-center align-items-center" style="width: 150px; height: 150px;">
          <h3 class="fs-6 mb-1">Double Bed</h3>
          <p class="fs-7">From Rs 1200</p>
        </div>
        <div class="bg-primary text-white rounded-circle d-flex flex-column justify-content-center align-items-center" style="width: 150px; height: 150px;">
          <h3 class="fs-6 mb-1">Triple Bed</h3>
          <p class="fs-7">From Rs 1500</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Us -->
  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center fs-2 fw-bold mb-4">Contact Us</h2>
      <div class="row">
        <div class="col-md-6 mb-4">
          <form>
            <div class="mb-3">
              <input type="text" class="form-control" placeholder="Your Name">
            </div>
            <div class="mb-3">
              <input type="email" class="form-control" placeholder="Your Email">
            </div>
            <div class="mb-3">
              <textarea class="form-control" rows="4" placeholder="Your Message"></textarea>
            </div>
            <button class="btn btn-primary w-100">Send Message</button>
          </form>
        </div>
        <div class="col-md-6">
          <div class="mb-4">
            <div class="d-flex align-items-center">
              <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l9 4-9 4-9-4 9-4zm0 0v16"></path>
              </svg>
              <p class="mb-0">123 Hostel Street, City, Country</p>
            </div>
            <div class="d-flex align-items-center">
              <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7 7 7-7"></path>
              </svg>
              <p class="mb-0">+977 9805625634</p>
            </div>
            <div class="d-flex align-items-center">
              <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m4-4v8"></path>
              </svg>
              <p class="mb-0">info@herhomehostel.com</p>
            </div>
          </div>
          <img src="images/map.png?height=300&width=400" alt="Map" class="rounded img-fluid shadow-sm">
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="fs-5 fw-bold"> Her Home Hostel</h3>
          <p class="mb-0">Your home away from home</p>
        </div>
        <div class="d-flex gap-2">
          <a href="#" class="text-white">
            <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h4v4M2 22h20m-2-10H8l2-5 7 5H2v2h18v-2z"></path>
            </svg>
          </a>
          <a href="#" class="text-white">
            <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2M16 3a4 4 0 10-8 0v5h8V3z"></path>
            </svg>
          </a>
          <a href="#" class="text-white">
            <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 9l10 10L22 9"></path>
            </svg>
          </a> 
        </div>
      </div>
      <div class="container">
      <div class="text-center mt-3">
        <p>&copy; 2024 Her Home Hostel.</p>
      </div>
      
    </div>
  </footer>
  

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Carousel Logic
    const slides = document.querySelectorAll('.carousel-img');
    let currentSlide = 0;

    function showSlide(index) {
      slides.forEach((slide, i) => {
        slide.classList.add('inactive-slide');
        slide.classList.remove('active-slide');
        if (i === index) {
          slide.classList.remove('inactive-slide');
          slide.classList.add('active-slide');
        }
      });
    }

    document.getElementById('prevSlide').addEventListener('click', () => {
      currentSlide = (currentSlide - 1 + slides.length) % slides.length;
      showSlide(currentSlide);
    });

    document.getElementById('nextSlide').addEventListener('click', () => {
      currentSlide = (currentSlide + 1) % slides.length;
      showSlide(currentSlide);
    });

    // Auto slide
    setInterval(() => {
      currentSlide = (currentSlide + 1) % slides.length;
      showSlide(currentSlide);
    }, 5000);
  </script>
</body>
</html>