function get_hosteler()
{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload=function(){
    document.getElementById('hosteler-data').innerHTML = this.responseText;
    }
    xhr.send('get_hosteler');
}

function toggle_status(id, val)
{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/hosteler.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
        if(this.responseText==1) {
            alert('success', 'Status toggled!');
            get_users();
    }
    else{
        alert('success', 'Server Down!');
    }
    }
    xhr.send('toggle_status='+id+'&value='+val);
}

function remove_hosteler(hid)
    {
        if(confirm("Are you sure you want to remove this hosteler?"))
        {
            let data = new FormData();
            data.append('hid', user_id);
            data.append('remove_hosteler','');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/hosteler.php", true);
            
            xhr.onload=function()
            {
                if(this.responseText == 1){
                alert('success', 'User Removed!');
                get_hosteler();
                }
                else{
                alert('error', 'User removal failed!');
                }
            }
                xhr.send(data);
        }
    }

    window.onload = function(){
    get_hosteler();
}