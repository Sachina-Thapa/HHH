// Function to display the booking status message
function showBookingStatusMessage(bstatus) {
    const bookingStatusMessage = document.getElementById('bookingStatusMessage');
    const additionalFieldsContainer = document.getElementById('additionalFieldsContainer');

    // Clear any previous messages
    bookingStatusMessage.style.display = 'none'; // Hide the message initially
    bookingStatusMessage.className = ''; // Reset classes

    // Check the booking status and set the message and styles
    if (bstatus === 'pending') {
        bookingStatusMessage.textContent = 'Your booking is Pending.';
        bookingStatusMessage.className = 'alert alert-warning'; // Yellow box
        additionalFieldsContainer.style.display = 'none'; // Hide additional fields
    } else if (bstatus === 'confirmed') {
        bookingStatusMessage.textContent = 'Your booking is Confirmed.';
        bookingStatusMessage.className = 'alert alert-success'; // Green box
        additionalFieldsContainer.style.display = 'block'; // Show additional fields
    } else if (bstatus === 'declined') {
        bookingStatusMessage.textContent = 'Your booking is Declined.';
        bookingStatusMessage.className = 'alert alert-danger'; // Red box
        additionalFieldsContainer.style.display = 'none'; // Hide additional fields
    } else {
        bookingStatusMessage.textContent = ''; // Clear message if status is unknown
    }

    // Show the message if there's content
    if (bookingStatusMessage.textContent) {
        bookingStatusMessage.style.display = 'block'; // Show the message
    }
}

// Function to submit the booking form
function submitBooking(event) {
    event.preventDefault(); // Prevent the default form submission

    const roomSelect = document.getElementById('room_type');
    const selectedOption = roomSelect.options[roomSelect.selectedIndex];

    const roomNo = selectedOption.getAttribute('data-room-no');
    const roomType = selectedOption.getAttribute('data-room-type');
    const roomPrice = selectedOption.getAttribute('data-room-price');

    // Prepare form data to send
    const formData = new FormData();
    formData.append('room_no', roomNo);
    formData.append('room_type', roomType);
    formData.append('room_price', roomPrice);

    // Send the data using fetch
    fetch('ajax/booking_process.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            console.log('Response:', response); // Debugging output
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json(); // Expecting JSON response
        })
        .then(data => {
            console.log('Data:', data); // Debugging output

            if (data.status === 'success') {
                showBookingStatusMessage('pending'); // Assuming the initial status is pending
                document.getElementById('roomSelectionBox').style.display = 'none'; // Hide the room selection box
            } else {
                alert(data.message); // Show error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message); // Alert user about the error
        });
}

// Function to cancel booking
function cancelBooking() {
    document.getElementById('bookNowButton').disabled = false;
    document.getElementById('roomSelectionBox').style.display = 'block'; // Ensure this ID matches your HTML
}

// Initialize the script after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    const bookingForm = document.getElementById('bookingForm');

    // Handle form submission
    bookingForm.addEventListener('submit', submitBooking);

    // Initialize message box
    const messageBox = document.getElementById('messageBox');
    const messageContent = document.getElementById('messageContent');
    const closeMessage = document.getElementById('closeMessage');

    // Show messages
    function showMessage(type, message) {
        messageContent.textContent = message;
        messageBox.style.display = 'block';

        // Style the message
        if (type === 'success') {
            messageBox.style.backgroundColor = 'green';
        } else {
            messageBox.style.backgroundColor = 'red';
        }
    }

    // Close message box
    closeMessage.addEventListener('click', function () {
        messageBox.style.display = 'none';
    });
});
