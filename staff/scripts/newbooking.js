// Function to confirm booking
function confirmBooking(bid, hosteler_id) {
  if (confirm("Are you sure you want to confirm this booking?")) {
    updateBookingStatus(bid, hosteler_id, "confirm");
  }
}

// Function to cancel booking
function cancelBooking(bid, hosteler_id) {
  if (confirm("Are you sure you want to cancel this booking?")) {
    updateBookingStatus(bid, hosteler_id, "cancel");
  }
}

// Function to update booking status via AJAX
function updateBookingStatus(bid, hosteler_id, action) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/new_booking.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText);
          if (response.status === "success") {
            alert(response.message);
            window.location.reload();
          } else {
            alert(response.message || "An error occurred");
          }
        } catch (e) {
          console.error("Error parsing JSON:", e);
          alert("An error occurred while processing the request");
        }
      } else {
        console.error("Server Error:", xhr.status, xhr.statusText);
        alert("Server error occurred. Please try again.");
      }
    }
  };

  // Add error handling for network issues
  xhr.onerror = function () {
    console.error("Network Error");
    alert("Network error occurred. Please check your connection.");
  };

  // Send the POST data
  const data = `bid=${encodeURIComponent(bid)}&hosteler_id=${encodeURIComponent(
    hosteler_id
  )}&action=${encodeURIComponent(action)}`;
  xhr.send(data);
}
