<?php
session_start();
$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'hhh'; 
$errorMessage = '';
$successMessage = '';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Login handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
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

// Contact form handling
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $msg = $_POST['msg'] ?? '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($msg)) {
        $errorMessage = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address";
    } else {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aakritipandit777@gmail.com'; // Replace with your Gmail
            $mail->Password = 'zeqx oiqu ukcu zoap'; // Replace with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('aakritipandit777@gmail.com', 'Her Home Hostel');
            $mail->addAddress('aakritipandit777@gmail.com', 'Admin');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            
            // Create HTML message body
            $htmlMessage = "
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Message:</strong>" . htmlspecialchars($msg) ."</p>
            ";
            
            $mail->Body = $htmlMessage;
            $mail->AltBody = "Name: $name\nEmail: $email\nMessage: $msg";

            $mail->send();
            $successMessage = "Thank you! Your message has been sent successfully.";
            
            // Clear form data after successful submission
            $name = $email = $msg = '';
            
        } catch (Exception $e) {
            $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
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
        
        .navbar {
            background-color: #333333!important;
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
    </style>
</head>
<body class="min-vh-100 bg-light">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logoo.png" alt="Logo"> 
                <span>Her Home Hostel</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link me-2" href="#home">Home</a>
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
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="border shadow p-3 rounded" method="POST" action="">
                        <h1 class="text-center p-3">User Login</h1>
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select User Type</label>
                            <select class="form-select" name="user_type" required>
                                <option selected disabled>Select User Type</option>
                                <option value="Admin">Admin</option>
                                <option value="Staff">Staff</option>
                                <option value="Hosteler">Hosteler</option>
                            </select>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">LOGIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register Hosteler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="reg_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="reg_email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="reg_phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Picture</label>
                                <input type="file" class="form-control" name="reg_picture" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="reg_address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="reg_pincode" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" name="reg_dob" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="reg_password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="reg_confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="home" class="position-relative vh-100">
        <div class="position-absolute w-100 h-100">
            <img id="slide1" src="images/Hostel.jpg" class="carousel-img active-slide w-100 h-100 object-fit-cover" alt="Slide 1">
            <img id="slide2" src="images/single.jpg" class="carousel-img inactive-slide w-100 h-100 object-fit-cover" alt="Slide 2">
            <img id="slide3" src="images/double.jpg" class="carousel-img inactive-slide w-100 h-100 object-fit-cover" alt="Slide 3">
            <img id="slide4" src="images/triple.jpg" class="carousel-img inactive-slide w-100 h-100 object-fit-cover" alt="Slide 4">
        </div>
        <div class="position-absolute top-0 bottom-0 start-0 end-0 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
            <div class="text-center text-white">
                <h1 class="display-3 fw-bold mb-4">Welcome to Her Home Hostel</h1>
                <p class="fs-4 mb-4">Experience comfort and community in our modern hostels</p>
                <button class="btn btn-primary btn-lg">Book Now</button>
            </div>
        </div>
        <button id="prevSlide" class="position-absolute start-0 top-50 translate-middle-y btn btn-outline-light ms-3">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button id="nextSlide" class="position-absolute end-0 top-50 translate-middle-y btn btn-outline-light me-3">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center fs-2 fw-bold mb-4">About Us</h2>
            <div class="row align-items-center">
                <div class="col-md-6 mb-4">
                    <img src="images/aboutus.png" alt="About Us" class="rounded shadow-lg img-fluid">
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-4">
                        Her Home Hostel is more than just a place to stay; it's a community where travelers from all over the world come
                        together. Our modern facilities and welcoming atmosphere ensure that your stay is comfortable, memorable, and full of new experiences.
                    </p>
                    <p class="text-muted mb-4">
                        We are committed to providing a convenient, secure, and efficient way for students, travelers, and working professionals to find and book hostel rooms according to their choice. 
                        Our mission is to simplify the process of finding and booking hostels, ensuring a seamless experience for users seeking affordable and comfortable accommodation.
                    </p>
                    <button class="btn btn-primary">Learn More</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="py-5 bg-light">
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
                            <h3 class="card-title fs-5">Wi-Fi</h3>
                            <p class="card-text text-muted">Stay connected with fast, free Wi-Fi at the hostel.</p>
                            <a href="#" class="btn btn-outline-primary w-100">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Room Types Section -->
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

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fs-2 fw-bold mb-4">Contact Us</h2>
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $successMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email"
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" name="msg" placeholder="Your Message" required><?php echo isset($msg) ? htmlspecialchars($msg) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="send">Send Message</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="mb-0">123 Hostel Street, City, Country</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <p class="mb-0">+977 9805625634</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <p class="mb-0">info@herhomehostel.com</p>
                        </div>
                    </div>
                    <img src="images/map.png" alt="Map" class="rounded img-fluid shadow-sm">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fs-5 fw-bold">Her Home Hostel</h3>
                    <p class="mb-0">Your home away from home</p>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-white">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-white">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">&copy; 2024 Her Home Hostel. All rights reserved.</p>
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