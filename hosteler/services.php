<?php
require('inc/hsidemenu.php');
require('inc/db.php');

// Fetch services from the database
$services = [];
$sql = "SELECT id, name, price FROM services";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hosteler Panel - Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="col-md-10 p-4">
  <div class="row">
    <!-- Food Options -->
    <div class="col-md-10 main-content">
      <label class="form-label text-dark">Services</label>
      <?php foreach ($services as $service): ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="service_<?php echo $service['id']; ?>" value="<?php echo $service['price']; ?>">
          <label class="form-check-label" for="service_<?php echo $service['id']; ?>"><?php echo $service['name']; ?> ($<?php echo $service['price']; ?>/day)</label>
        </div>
      <?php endforeach; ?>
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
</div>

<script>
function updateLaundryOptions(days) {
  const laundryOptionsContainer = document.getElementById('laundryOptions');
  laundryOptionsContainer.innerHTML = '';

  let optionsHtml = '';
  if (days < 7) {
    optionsHtml += `
      <div class="form-check">
        <input class="form-check-input" type="radio" name="laundryService" id="dailyLaundry" value="10">
        <label class="form-check-label" for="dailyLaundry">Daily ($10/day)</label>
      </div>`;
  } else if (days === 7) {
    optionsHtml += `
      <div class="form-check">
        <input class="form-check-input" type="radio" name="laundryService" id="weeklyLaundry" value="50">
        <label class="form-check-label" for="weeklyLaundry">Weekly ($50)</label>
      </div>`;
  } else {
    optionsHtml += `
      <div class="form-check">
        <input class="form-check-input" type="radio" name="laundryService" id="monthlyLaundry" value="150">
        <label class="form-check-label" for="monthlyLaundry">Monthly ($150)</label>
      </div>`;
  }
  laundryOptionsContainer.innerHTML = optionsHtml;
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

  const foodTotal = Array.from(document.querySelectorAll('.form-check-input:checked'))
    .reduce((total, checkbox) => total + Number(checkbox.value) * days, 0);

  const laundryOption = document.querySelector('input[name="laundryService"]:checked');
  const laundryTotal = laundryOption ? (laundryOption.id === 'dailyLaundry' ? laundryOption.value * days : Number(laundryOption.value)) : 0;

  const totalPrice = foodTotal + Number(laundryTotal);
  document.getElementById('totalPrice').innerText = totalPrice;
}
</script>
</body>
</html>