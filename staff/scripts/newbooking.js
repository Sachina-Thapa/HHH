function confirmBooking(bid, id) {
    if (confirm("Are you sure you want to confirm this booking?")) {
        let formData = new FormData();
        formData.append('action', 'confirm');
        formData.append('bid', bid);
        formData.append('id', id);

        fetch('../ajax/newbooking.php', {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.text())
        .then((data) => {
            if (data === '1') {
                alert("Booking confirmed!");
                location.reload(); // Reload to update the status in the UI
            } else {
                console.log("Error confirming booking: " + data);
                alert("Failed to confirm booking. Please try again.");
            }
        })
        .catch((error) => {
            console.error("Error confirming booking:", error);
        });
    }
}

function cancelBooking(bid, id) {
    if (confirm("Are you sure you want to cancel this booking?")) {
        let formData = new FormData();
        formData.append('action', 'cancel');
        formData.append('bid', bid);
        formData.append('id', id);

        fetch('../ajax/newbooking.php', {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.text())
        .then((data) => {
            if (data === '1') {
                alert("Booking canceled!");
                location.reload(); // Reload to update the status in the UI
            } else {
                console.log("Error canceling booking: " + data);
                alert("Failed to cancel booking. Please try again.");
            }
        })
        .catch((error) => {
            console.error("Error canceling booking:", error);
        });
    }
}
