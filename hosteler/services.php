<?php
require('inc/hsidemenu.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hosteler Panel - Feedback</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style></style>
</head>
<body>

 <!-- Food Options -->
 <div class="col-md-10 p-4">
        <div class="row">
        <div class="col-md-10 main-content">
                        <label class="form-label text-dark">Food Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="breakfast" value="10">
                            <label class="form-check-label" for="breakfast">Breakfast ($10/day)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lunch" value="15">
                            <label class="form-check-label" for="lunch">Lunch ($15/day)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dinner" value="20">
                            <label class="form-check-label" for="dinner">Dinner ($20/day)</label>
                        </div>
                    </div>
                    <hr>

                    <!-- Laundry Service -->
                    <div class="col-md-10 p-4">
                        <label class="form-label text-dark">Laundry Service</label>
                        <div id="laundryOptions"></div>
                    </div>

                    <!-- Other Services -->
                    <div class="col-md-10 p-4">
                        <label for="otherServices" class="form-label text-dark">Other Services (Optional)</label>
                        <input type="text" class="form-control" id="otherServices" placeholder="Specify any additional services">
                    </div>
                </div>
<script>

                function updateLaundryOptions(days) {
            const laundryOptionsContainer = document.getElementById('laundryOptions');
            laundryOptionsContainer.innerHTML = '';
            let laundryOptionsHtml = '';
            if (days < 7) {
                laundryOptionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="laundryService" id="dailyLaundry" value="10">
                        <label class="form-check-label" for="dailyLaundry">Daily ($10/day)</label>
                    </div>
                `;
            } else if (days === 7) {
                laundryOptionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="laundryService" id="weeklyLaundry" value="50">
                        <label class="form-check-label" for="weeklyLaundry">Weekly ($50)</label>
                    </div>
                `;
            } else {
                laundryOptionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="laundryService" id="monthlyLaundry" value="150">
                        <label class="form-check-label" for="monthlyLaundry">Monthly ($150)</label>
                    </div>
                `;
            }
            laundryOptionsContainer.innerHTML = laundryOptionsHtml;
        }

        function calculateTotalPrice() {
            const checkIn = new Date(document.getElementById('checkIn').value);
            const checkOut = new Date(document.getElementById('checkOut').value);
            const days = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)) + 1;
            if (days <= 0 || isNaN(days)) {
                document.getElementById('totalPrice').innerText = 0;
                return;
            }
            updateLaundryOptions(days);
            const roomType = document.querySelector('input[name="roomType"]:checked').value;
            const roomTotal = roomType * days;
            const foodOptions = ['breakfast', 'lunch', 'dinner'];
            const foodTotal = foodOptions.reduce((total, optionId) => {
                const option = document.getElementById(optionId);
                return option.checked ? total + (Number(option.value) * days) : total;
            }, 0);
            const laundryOption = document.querySelector('input[name="laundryService"]:checked');
            const laundryTotal = laundryOption ? (laundryOption.id === 'dailyLaundry' ? laundryOption.value * days : laundryOption.value) : 0;
            const totalPrice = roomTotal + foodTotal + Number(laundryTotal);
            document.getElementById('totalPrice').innerText = totalPrice;
        }   
        function getSelectedFoodOptions() {
            const options = [];
            document.querySelectorAll('#breakfast, #lunch, #dinner').forEach(option => {
                if (option.checked) options.push(option.nextElementSibling.textContent.trim());
            });
            return options.length > 0 ? options.join(', ') : 'None';
        }
                

        </script>
        </body>
        </html>