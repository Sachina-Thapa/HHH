// function get_bookings()
// {
// let xhr = new XMLHttpRequest();
// xhr.open("POST", "ajax/new_bookings.php", true);
// xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
// xhr.onload = function(){
// document.getElementById('table-data').innerHTML = this.responseText;
// }
// xhr.send('get_bookings');
// }
// function remove_user(user_id)
// {
// if(confirm("Are you sure, you want to remove this user?"))
// let data new FormData();
// data.append('user_id', user_id);
// data.append('remove_user','');
// }

function confirmBooking(bid, hid) {
    if (confirm("Are you sure you want to confirm this booking?")) {
        let formData = new FormData();
        formData.append('confirm_booking', true);
        formData.append('bid', bid);
        formData.append('hid', hid);

        fetch('../ajax/newbooking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data == 1) {
                alert("Booking confirmed!");
                location.reload();
            } else {
                alert("Failed to confirm booking.");
            }
        });
    }
}

function cancelBooking(bid, hid) {
    if (confirm("Are you sure you want to cancel this booking?")) {
        let formData = new FormData();
        formData.append('cancel_booking', true);
        formData.append('bid', bid);
        formData.append('hid', hid);

        fetch('../ajax/newbooking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data == 1) {
                alert("Booking canceled!");
                location.reload();
            } else {
                alert("Failed to cancel booking.");
            }
        });
    }
}
