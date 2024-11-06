<!-- NAVIGATION BAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand me-6 fq-bold fs-3" href="index.html">Her Home Hostel</a>
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

            <form method="POST" action="index.php" enctype="multipart/form-data" id="registerForm">
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
                        <input type="text" name="name" class="form-control shadow-none">
                      </div>

                      <div class="col-md-6 ps-0 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" pattern="(\+977?)?[9][6-9]\d{8}"
                        maxLength="10" name="phone" class="form-control shadow-none">
                      </div>
                      <div class="col-md-6 p-0 mb-3">
                        <label class="form-label">Picture</label>
                        <input type="file" name="picture" class="form-control shadow-none">
                      </div>
                      <div class="col-md-12 ps-6 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control shadow" rows="1"></textarea>
                      </div>
                      <div class="col-md-6 ps-0 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control shadow-none">
                      </div>
                      <div class="col-md-6 p-0 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control shadow-none">
                      </div>
                      <div class="col-md-6 p-0 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control shadow-none">
                      </div>
                      <div class="col-md-6 p-0 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control shadow-none">
                      </div>
                      <div class="col-md-6 p-0">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <input type="email" name="email" id="register-email" class="form-control shadow-none">
                            <button type="button" class="btn btn-primary" id="sendOtpBtn" onclick="sendOTP()">Send OTP</button>
                        </div>
                      <div class="col-md-6 p-0 mb-3 mt-3" id="otpSection" style="display:none;">
                        <label class="form-label">Enter OTP</label>
                        <div class="input-group">
                            <input type="text" maxLength="6" id="otp-input" class="form-control shadow-none" maxlength="6">
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
        </script>