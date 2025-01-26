function confirmBooking(bid, hosteler_id) {
    updateBookingStatus(bid, hosteler_id, 'confirm');
}

function cancelBooking(bid, hosteler_id) {
    updateBookingStatus(bid, hosteler_id, 'cancel');
}

function updateBookingStatus(bid, hosteler_id, action) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'newbooking_process.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Refresh the page or update the table dynamically
            location.reload();
        }
    };
    xhr.send('bid=' + bid + '&hosteler_id=' + hosteler_id + '&action=' + action);
}
