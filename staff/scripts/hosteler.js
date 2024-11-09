function get_hosteler() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        document.getElementById('hosteler-data').innerHTML = this.responseText;
    }
    xhr.send('get_hosteler');
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
