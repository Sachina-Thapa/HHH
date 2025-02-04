// Function to confirm booking
function confirmBooking(bid, hosteler_id) {
    console.log("Confirming booking:", bid, hosteler_id);
    updateBookingStatus(bid, hosteler_id, 'confirm');
}

// Function to cancel booking
function cancelBooking(bid, hosteler_id) {
    console.log("Cancelling booking:", bid, hosteler_id);
    updateBookingStatus(bid, hosteler_id, 'cancel');
}

// Function to update booking status via AJAX
function updateBookingStatus(bid, hosteler_id, action) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/new_booking.php', true);  // Ensure correct path to new_booking.php
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);  // Parse JSON response
            console.log("Response:", response.message);  // Log the response message

            if (response.status === 'success') {
                location.reload();  // Reload the page if successful
            } else {
                alert(response.message);  // Show error message if update fails
            }
        } else if (xhr.readyState === XMLHttpRequest.DONE) {
            console.error("Error during AJAX request:", xhr.statusText);
        }
    };
    
    // Send the POST data
    xhr.send('bid=' + bid + '&hosteler_id=' + hosteler_id + '&action=' + action);
}
