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

// Add Query facilities
$facilities_query = "SELECT * FROM facilities ORDER BY id";
$facilities_result = mysqli_query($conn, $facilities_query);


// Add query room
$rooms_query = "SELECT * FROM rooms ORDER BY id";
$rooms_result = mysqli_query($conn, $rooms_query);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['form_type'])) {
        if($_POST['form_type'] === 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $userType = $_POST['user_type'];

            switch ($userType) {
                case 'Admin':
                    $table = 'adminlogin';
                    $dashboard = 'admin/addash.php';
                    break;
                case 'Staff':
                    $table = 'staff_data';
                    $dashboard = 'staff/staffdash.php';
                    break;
                case 'Hosteler':
                    $table = 'hostelers';
                    $dashboard = 'hosteler/index/overview.php';
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
                    $row = $result->fetch_assoc();
                    if ($row['status'] == '1') {
                        $_SESSION['username'] = $username;
                        header("Location: $dashboard");
                        exit();
                    } else {
                        $errorMessage = "Account is not active.";
              
                    }
                } else {
                    $errorMessage = "Invalid username or password.";
              
                }
                $stmt->close();
            }
        }
        else if($_POST['form_type'] === 'register') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $dob = $_POST['dob'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $picture = '';
            if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
                $target_dir = "uploads/";
                $picture = $target_dir . time() . '_' . basename($_FILES["picture"]["name"]);
                move_uploaded_file($_FILES["picture"]["tmp_name"], $picture);
            }
            
            $sql = "INSERT INTO hostelers (name, email, phone_number, picture_path, address, date_of_birth, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $name, $email, $phone, $picture, $address, $dob, $username, $password);
            
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!');</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
            
            $stmt->close();
        }
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
    .navbar {
    transition: all 0.3s ease;
    background-color:#36454F!important;

   
}

.navbar.scrolled {

  background-color: transparent !important;
    backdrop-filter: blur(2px);

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
      text-shadow: 2px 2px 4px rgba(0, 0, 0.5, 1);
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

<?php require('inc/header.php'); ?>

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


  <section id="rooms" class="py-5 bg-white">
      <div class="container">
          <h2 class="text-center fs-2 fw-bold mb-4">Choose Your Perfect Hostel Room</h2>
          <div class="d-flex flex-wrap justify-content-center gap-4">
              <?php while($room = mysqli_fetch_assoc($rooms_result)): ?>
                  <div class="bg-primary text-white rounded-circle d-flex flex-column justify-content-center align-items-center" 
                   style="width: 150px; height: 150px;">
                      <h3 class="fs-6 mb-1"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                      <p class="fs-7">From Rs <?php echo htmlspecialchars($room['price']); ?></p>
                  </div>
              <?php endwhile; ?>
          </div>
      </div>
  </section>
  <!-- Contact Us -->
  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center fs-2 fw-bold mb-4">Contact Us</h2>
      <div class="row">
        <div class="col-md-3">
      </div>
        <div class="col-md-3">
          <div class="mb-4 mt-5  ">
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
        </div>
        <div class="col-md-6">
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

    document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});

function handleSubmit(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('handle_query.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Message sent successfully!');
            event.target.reset();
        } else {
            alert('Error sending message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    });
    
    return false;
}
</script>

</body>
</html>

 