function get_bookings()
{
let xhr = new XMLHttpRequest();
xhr.open("POST", "ajax/new_bookings.php", true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.onload = function(){
document.getElementById('table-data').innerHTML = this.responseText;
}
xhr.send('get_bookings');
}
function remove_user(user_id)
{
if(confirm("Are you sure, you want to remove this user?"))
let data new FormData();
data.append('user_id', user_id);
data.append('remove_user','');