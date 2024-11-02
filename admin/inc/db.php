<?php
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "hhh";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
//$conn = new mysqli( "localhost", "root", "", "hhh");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function filteration($data){
    foreach($data as $key => $value){
        $data[$key]= trim($value);
        $data[$key]= stripslashes($value);
        $data[$key]= htmlspecialchars($value);
        $data[$key]= strip_tags($value);
    }
    return $data;
}
function select($sql,$values,$datatypes){
    $con=$GLOBALS['con'];
    if($stmt=mysqli_prepare($con,$sql)){
        mysqli_stmt_bind_param($stmt,$datatypes,...$values);
        if(mysqli_stmt_execute($stmt)){
            $res= mysqli_stmt_get_result($stmt);
            return $res;
        } 
        else{
            mysqli_stmt_close($stmt);
            die("Query cannot be executed-Select");
        }
        

    }else{
        die("Query cannot be executed-Select");
    }
}
?>
