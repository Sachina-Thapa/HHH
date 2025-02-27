<?php

$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'hhh'; 
$errorMessage = '';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION)) {
    session_start();
}

$result = null;
if (isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? '';
    
    // Set dashboard based on user type
    $dashboard = '';
    if ($userType === 'Admin') {
        $dashboard = 'admin/dashboard.php';
    } else if ($userType === 'Staff') {
        $dashboard = 'staff/dashboard.php';
    } else if ($userType === 'Hosteler') {
        $dashboard = 'hosteler/dashboard.php';
    }
    
    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND user_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $userType);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['status'] == '1') {
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $userType;
        
        // If there was a pending booking attempt
        if (isset($_SESSION['booking_pending']) && $_SESSION['booking_pending']) {
            unset($_SESSION['booking_pending']);
            echo "<script>window.location.href = 'hosteler/bookNow.php';</script>";
            exit();
        }
        
        header("Location: $dashboard");
        exit();
    } else {
        $errorMessage = "Account is not active.";
    }
}
// Fetch current site settings
$settings_query = "SELECT * FROM site_settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$site_settings = mysqli_fetch_assoc($settings_result);
// Fetch logo from site_settings
$logo_query = "SELECT logo_path FROM site_settings LIMIT 1";
$logo_result = mysqli_query($conn, $logo_query);
$logo_path = $logo_result && mysqli_num_rows($logo_result) > 0 
    ? mysqli_fetch_assoc($logo_result)['logo_path'] 
    : '../images/logoo.png'; // Fallback to default logo if no logo in database
?>

 <!-- NAVIGATION BAR -->
 <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container-fluid">
    <!-- Logo in the Navbar -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">

    <!-- Map logo -->
    <img src="admin/<?php echo htmlspecialchars($logo_path); ?>" 
           alt="Logo" 
           style="filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));">

           <span style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 1.5);">
    <?php echo isset($site_settings['site_title']) ? htmlspecialchars($site_settings['site_title']) : 'Her Home Hostel'; ?>
</span>

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
                    <form class="border shadow p-3 rounded" method="POST" action="index.php">
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
                        <input type="hidden" name="form_type" value="login">
                           <input type="hidden" name="booking_redirect" value="<?php echo isset($_SESSION['booking_pending']) ? '1' : '0'; ?>">

                        <div class="d-flex align-items-center justify-content-between mb-2"> 
                            <button type="submit" class="btn btn-primary">LOGIN</button>
                            <a href="#" class="text-secondary text-decoration-none ms-auto" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Forgot Password?</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

 <!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <!-- Email Form -->
<div id="emailForm" class="form-container">
    <form class="form" id="forgotPasswordForm">
        <div class="form-group mb-3">
            <label for="forgot-email">Email</label>
            <input type="email" id="forgot-email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <button type="button" class="btn btn-primary w-100" id="sendForgotPasswordBtn" onclick="sendForgotPasswordOTP()">Send Email</button>
    </form>
</div>


                <!-- OTP Verification Form -->
<div id="otpVerificationForm" class="form-container" style="display:none;">
    <div class="title text-center">OTP Verification Code</div>
    <p class="message text-center">We have sent a verification code to your email</p>
    <div class="inputs d-flex justify-content-center gap-2 mb-3">
        <input id="input1" type="text" maxlength="1" class="form-control">
        <input id="input2" type="text" maxlength="1" class="form-control">
        <input id="input3" type="text" maxlength="1" class="form-control">
        <input id="input4" type="text" maxlength="1" class="form-control">
        <input id="input5" type="text" maxlength="1" class="form-control">
        <input id="input6" type="text" maxlength="1" class="form-control">
    </div>
    <button class="btn btn-primary w-100" onclick="verifyForgotPasswordOTP()">Verify</button>
</div>


                <!-- New Password Form -->
                <div id="newPasswordForm" class="form-container" style="display:none;">
                    <form class="form" id="changePasswordForm">
                        <div class="form-group mb-3">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm-password">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="changePassword()">Change Password</button>
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
              <form method="POST" action="index.php" enctype="multipart/form-data" id="registerForm">
                <div class="modal-header">
                  <h5 class="modal-title" id="registerModalLabel">Register Hosteler</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control shadow-none" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" pattern="(\+977?)?[9][6-9]\d{8}" maxLength="10" name="phone" class="form-control shadow-none" required>
                      </div>
                      <!-- <div class="col-md-6 mb-3">
                        <label class="form-label">Picture</label>
                        <input type="file" name="picture" class="form-control shadow-none" required>
                      </div> -->
                      <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control shadow-none" rows="2" required></textarea>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control shadow-none" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control shadow-none" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control shadow-none" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control shadow-none" required>
                      </div>
                      <div class="col-md-12 mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                          <input type="email" name="email" id="register-email" class="form-control shadow-none" required>
                          <button type="button" class="btn btn-primary" id="sendOtpBtn" onclick="sendOTP()">Send OTP</button>
                        </div>
                      </div>
                      <div class="col-md-12 mb-3" id="otpSection" style="display:none;">
                        <label class="form-label">Enter OTP</label>
                        <div class="input-group">
                          <input type="text" maxLength="6" id="otp-input" class="form-control shadow-none">
                          <button type="button" class="btn btn-success" onclick="verifyOTP()">Verify OTP</button>
                        </div>
                      </div>
                      <input type="hidden" name="form_type" value="register">
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary" id="registerBtn" disabled>Register</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <script>
        let emailVerified = false;

        function sendOTP() {
            const email = document.getElementById('register-email').value;
            if(email) {
                const formData = new FormData();
                formData.append('email', email);
                
                fetch('send_otp.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Server response:', data); // For debugging
                    try {
                        const jsonData = JSON.parse(data);
                        if(jsonData.success) {
                            document.getElementById('otpSection').style.display = 'block';
                            document.getElementById('sendOtpBtn').disabled = true;
                            alert('OTP sent successfully! Please check your email.');
                        } else {
                            alert(jsonData.message || 'Failed to send OTP');
                        }
                    } catch(e) {
                        console.error('Parse error:', e);
                        alert('Server communication error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Network error occurred');
                });
            } else {
                alert('Please enter a valid email address');
            }
        }
        function verifyOTP() {
            const otp = document.getElementById('otp-input').value;
            const email = document.getElementById('register-email').value;

            fetch('verify_otp.php', {
                method: 'POST',
                body: JSON.stringify({ 
                    email: email,
                    otp: otp 
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                   // document.getElementById('pincode-input').disabled = false;
                    document.getElementById('registerBtn').disabled = false;
                    emailVerified = true;
                    alert('Email verified successfully!');
                } else {
                    alert('Invalid OTP');
                }
            });
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if(!emailVerified) {
                e.preventDefault();
                alert('Please verify your email first');
            }
        });

        function sendForgotPasswordOTP() {
    const email = document.getElementById('forgot-email').value;
    const sendButton = document.getElementById('sendForgotPasswordBtn');
    
    if(email) {
        // Disable button immediately when sending
        sendButton.disabled = true;
        sendButton.textContent = 'Sending...';
        
        fetch('check_and_send_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                document.getElementById('emailForm').style.display = 'none';
                document.getElementById('otpVerificationForm').style.display = 'block';
                alert('OTP sent successfully! Please check your email.');
            } else {
                // Re-enable button if email not found
                sendButton.disabled = false;
                sendButton.textContent = 'Send Email';
                alert(data.message || 'Email not found in our system');
            }
        })
        .catch(error => {
            // Re-enable button on error
            sendButton.disabled = false;
            sendButton.textContent = 'Send Email';
            console.error('Error:', error);
            alert('Server error occurred. Please try again.');
        });
    }
}



function verifyForgotPasswordOTP() {
    const otp = Array.from({length: 6}, (_, i) => document.getElementById(`input${i+1}`).value).join('');
    const email = document.getElementById('forgot-email').value;

    fetch('verify_otp.php', {
        method: 'POST',
        body: JSON.stringify({ email, otp }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('otpVerificationForm').style.display = 'none';
            document.getElementById('newPasswordForm').style.display = 'block';
        } else {
            alert('Invalid OTP');
        }
    });
}


function changePassword() {
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const email = document.getElementById('forgot-email').value;

    if(newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }

    fetch('change_password.php', {
        method: 'POST',
        body: JSON.stringify({
            email,
            password: newPassword
        }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Password changed successfully');
            document.getElementById('forgotPasswordModal').style.display = 'none';
            // Remove modal backdrop
            document.querySelector('.modal-backdrop').remove();        } else {
            alert(data.message || 'Failed to change password');
        }
    });
}

// Auto-focus next input in OTP verification
document.querySelectorAll('#otpVerificationForm input').forEach((input, index) => {
    input.addEventListener('input', function() {
        if(this.value && index < 5) {
            document.getElementById(`input${index + 2}`).focus();
        }
    });
});
        </script>