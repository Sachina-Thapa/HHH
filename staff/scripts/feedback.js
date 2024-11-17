function get_feedback() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feedback.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        document.getElementById('feedback-data').innerHTML = this.responseText;
    }
    xhr.send('get_feedback');
}

function remove_feedback(fid) {
    if (confirm("Are you sure you want to remove this feedback?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/feedback.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (this.responseText == "1") {
                alert('Feedback removed successfully!');
                get_feedback(); // Reload the list of feedback
            } else {
                alert('Failed to remove feedback.');
            }
        }

        // Send the request with fid and remove_feedback flag
        xhr.send(`fid=${fid}&remove_feedback=1`);
    }
}

window.onload = function() {
    get_feedback();
}
