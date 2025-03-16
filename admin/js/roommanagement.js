
function checkRoomNumber() {
    var roomNumber = document.getElementById('room_number').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/roommanagement.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('room_check_message').innerText = this.responseText;
            if (this.responseText === "This room already exists.") {
                document.getElementById('room_check_message').style.color = 'red';
            } else {
                document.getElementById('room_check_message').style.color = 'green';
            }
        }
    };
    xhr.send('room_number=' + encodeURIComponent(roomNumber));
}


function enableEdit(rid) {
    document.getElementById('rno_' + rid).contentEditable = true;
    document.getElementById('rtype_' + rid).contentEditable = true;
    document.getElementById('rprice_' + rid).contentEditable = true;
    document.getElementById('edit_' + rid).style.display = 'none';
    document.getElementById('save_' + rid).style.display = 'inline';
}

function saveEdit(rid) {
    var rno = document.getElementById('rno_' + rid).innerText;
    var rtype = document.getElementById('rtype_' + rid).innerText;
    var rprice = document.getElementById('rprice_' + rid).innerText.replace('₹ ', '').replace(',', '');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'roommanagement.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('rno_' + rid).contentEditable = false;
            document.getElementById('rtype_' + rid).contentEditable = false;
            document.getElementById('rprice_' + rid).contentEditable = false;
            document.getElementById('edit_' + rid).style.display = 'inline';
            document.getElementById('save_' + rid).style.display = 'none';
        }
    };
    xhr.send('update=1&rid=' + rid + '&rno=' + encodeURIComponent(rno) + '&rtype=' + encodeURIComponent(rtype) + '&rprice=' + encodeURIComponent(rprice));
}