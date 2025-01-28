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

// Prevent multiple submissions
let isSubmitting = false;

function submitBooking(event) {
    event.preventDefault();

    if (isSubmitting) {
        return; // Exit if the form is already submitting
    }
    isSubmitting = true;

    const roomSelect = document.getElementById('room_type');
    const selectedOption = roomSelect.options[roomSelect.selectedIndex];

    const roomNo = selectedOption.getAttribute('data-room-no');
    const roomType = selectedOption.getAttribute('data-room-type');
    const roomPrice = selectedOption.getAttribute('data-room-price');

    const formData = new FormData();
    formData.append('room_no', roomNo);
    formData.append('room_type', roomType);
    formData.append('room_price', roomPrice);

    fetch('ajax/booking_process.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showBookingStatusMessage('pending');
                document.getElementById('roomSelection').style.display = 'none';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message);
        })
        .finally(() => {
            isSubmitting = false; // Reset the flag after completion
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
    if (bookingForm) {
        bookingForm.addEventListener('submit', submitBooking);
    } else {
        console.error('Booking form not found.');
    }

    // Initialize message box
    const messageBox = document.getElementById('messageBox');
    const messageContent = document.getElementById('messageContent');
    const closeMessage = document.getElementById('closeMessage');

    // Show messages
    function showMessage(type, message) {
        if (messageContent) {
            messageContent.textContent = message;
        }
        if (messageBox) {
            messageBox.style.display = 'block';

            // Style the message
            if (type === 'success') {
                messageBox.style.backgroundColor = 'green';
            } else {
                messageBox.style.backgroundColor = 'red';
            }
        }
    }

    // // Close message box
    // if (closeMessage) {
    //     closeMessage.addEventListener('click', function () {
    //         if (messageBox) {
    //             messageBox.style.display = 'none';
    //         }
    //     });
    // } else {
    //     console.error('Close message button not found.');
    // }
});
