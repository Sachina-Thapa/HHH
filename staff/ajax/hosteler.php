<?php
require('../inc/db.php');
// adminLogin();
if(isset($_POST['get_users']))
$res = selectAll('user_cred');
$i=1;
$data = "";
while($row = mysqli_fetch_assoc($res))
{
    $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
    if(!$row['status'])
    {
     $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-danger btn-sm shadow-none'>inactive</button>";
    }
    $date=date("d-m-Y",strtotime($row['datentime']));
     $data.="
    <tr>
        <td>$i</td>
        <td>
            <img src=$path$row[profile]' width='55px'>
            <br>
            $row[name]
        </td>
        <td>$row[email]</td>
        <td>$row[phonenue]</td>
        <td>$row[address] | $row[pincode]</td>
        <td>$row[dob]</td>
        <td>$status</td>
        <td>$date</td>
    </tr>
    ";
    $i++;
    echo $data;
    }
    if(isset($_POST['toggle_status']))
    {
        $frm_data = filteration($_POST);
        $q="UPDATE 'hosteler_cred' SET 'status' =? WHERE id=?";
        $v= [$frm_data['value'], $frm_data['toggle_status']];

        if(update($q, $v, 'ii')){
            echo 1;
        }
        else{
            echo 0;
        }
    }

    if(isset($_POST['remove_hosteler']))
    {
        $frm_data = filteration($_POST);

        $res = delete("DELETE FROM 'hosteler_cred' WHERE 'id'=?", [$frm_data['hid']],'ii');
        if($res){
        echo 1;
        }
        else{
        echo 0;
        }
    }
    ?>
  