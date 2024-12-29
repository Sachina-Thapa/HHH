function get_hosteler() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        document.getElementById('hosteler-data').innerHTML = this.responseText;
    }
    xhr.send('get_hosteler');
}
function fetch_hosteler_details(id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        const data = JSON.parse(this.responseText);
        if (data) {
            document.getElementById('hosteler-id').innerText = data.id;
            document.getElementById('hosteler-name').innerText = data.name;
            document.getElementById('hosteler-email').innerText = data.email;
            document.getElementById('hosteler-phone').innerText = data.phone_number;
            document.getElementById('hosteler-location').innerText = data.address;
            document.getElementById('hosteler-status').innerText = data.status ? 'Active' : 'Inactive';
            document.getElementById('hosteler-dob').innerText = data.date_of_birth;
            document.getElementById('hosteler-created-at').innerText = data.created_at;
        }
    }
    xhr.send('get_hosteler_details');
}

function addRowClickListeners() {
    const rows = document.querySelectorAll('#hosteler-data tr');
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON') {
                fetch_hosteler_details(this.dataset.id);
            }
        });
    });
}


function toggle_status(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText == 1) {
            alert('Status toggled successfully!');
            get_hosteler();
        } else {
            alert('Server error, please try again later.');
        }
    }
    xhr.send('toggle_status=' + id + '&value=' + val);
}

function remove_hosteler(id) {
    if (confirm("Are you sure you want to remove this hosteler?")) {
        let data = new FormData();
        data.append('id', id);
        data.append('remove_hosteler', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/hosteler.php", true);

        xhr.onload = function() {
            if (this.responseText == 1) {
                alert('Hosteler removed successfully!');
                get_hosteler(); // Reload the list of hostelers
            } else {
                alert('Failed to remove hosteler.');
            }
        }
        xhr.send(data); // Send the request with the form data
    }
}


function toggle_status(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText == 1) {
            alert('Status toggled successfully!');
            get_hosteler();
        } else {
            alert('Server error, please try again later.');
        }
    }
    xhr.send('toggle_status=' + id + '&value=' + val);
}

function remove_hosteler(id) {
    if (confirm("Are you sure you want to remove this hosteler?")) {
        let data = new FormData();
        data.append('id', id);
        data.append('remove_hosteler', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/hosteler.php", true);

        xhr.onload = function() {
            if (this.responseText == 1) {
                alert('Hosteler removed successfully!');
                get_hosteler(); // Reload the list of hostelers
            } else {
                alert('Failed to remove hosteler.');
            }
        }
        xhr.send(data); // Send the request with the form data
    }
}

function search_hosteler(name) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        document.getElementById('hosteler-data').innerHTML = this.responseText;
    }
    xhr.send('search_hosteler&name=' + name);
}

window.onload = function() {
    get_hosteler();
}
