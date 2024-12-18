<?php
require('inc/hsidemenu.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style></style>
</head>
<body>


<!-- Total Price Display -->
<div class="card-footer gradient-bg text-dark d-flex justify-content-between align-items-center p-3">
                    <div class="price-label">
                        Total Price: $<span id="totalPrice">0</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Book Now</button>
                </div>
            </form>
        </div>


<!-- Voucher Upload Section -->
<div id="voucherUploadSection" class="card shadow mt-4">
            <div class="card-header text-center bg-secondary text-white">
                <h3 class="card-title">Upload Payment Voucher</h3>
            </div>
            <div class="card-body">
                <form id="voucherForm">
                    <div class="mb-3">
                        <label for="voucherFile" class="form-label">Choose Voucher File</label>
                        <input type="file" class="form-control" id="voucherFile" accept="image/*" required>
                        <div class="error" id="voucherError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Voucher</button>
                </form>
            </div>
        </div>
    </div>
<script>
     document.getElementById('voucherForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const voucherFile = document.getElementById('voucherFile').files[0];
            if (voucherFile) {
                document.getElementById('voucherError').textContent = '';

    // Display uploaded voucher as an image
    const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.alt = "Voucher Image";
                    img.classList.add("img-thumbnail", "mt-3");
                    img.style.maxWidth = "200px";
                    document.getElementById('voucherImageContainer').innerHTML = '<strong>Voucher Image:</strong><br>';
                    document.getElementById('voucherImageContainer').appendChild(img);
                };
                reader.readAsDataURL(voucherFile);

                // Update voucher status to "Pending" after submission
                document.getElementById('voucherStatus').innerText = 'Pending';
                document.getElementById('voucherStatus').className = 'badge bg-warning text-dark';

                // Hide voucher upload section
                document.getElementById('voucherUploadSection').style.display = 'none';
            } else {
                document.getElementById('voucherError').textContent = 'Please upload a voucher file.';
            }
        });

        document.getElementById('checkIn').addEventListener('change', calculateTotalPrice);
        document.getElementById('checkOut').addEventListener('change', calculateTotalPrice);
        document.querySelectorAll('input[name="roomType"], #breakfast, #lunch, #dinner').forEach(element => {
            element.addEventListener('change', calculateTotalPrice);
        });
</script>
</body>
</html>