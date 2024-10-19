
<?php
    require('../inc/db_config.php');
if(isset($_GET['seen']))
{
    $frm_data=filteration($_GET);

    if($frm_data['seen']=='all'){

    }
    else{
        $q="UPDATE `queries` SET `seen`=? WHERE `sr_no`=?";
        $values=[1,$frm_data['seen']];
        if(update($q,$values,'ii')){
            alert('sucess','Marked as read');
        }
        else{
            alert('error','Operation Failed');
        }
    }
}
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Room Management</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 100%;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            
            /*sidebar css*/
            .sidebar {
                margin:0px;
                height: 100vh;
                background-color: #343a40;
                padding-top: 10px;
            }
            .sidebar a {
                color: #fff;
                padding: 15px;
                display: block;
                text-decoration: none;
                
            }
            .sidebar a:hover {
                background-color: #495057;
            }
            .logout-btn {
                margin-top: 20px;
                background-color: #f8f9fa;
                border: none;
                color: #000;
                padding: 10px;
            }
        </style>
    </head>
    <body> 
    <div class="container-fluid m-0">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
        <h3 class="text-white text-center">Her Home Hostel</h3>
                    <a href="addash.php">Dashboard</a>
                    <a href="roomManagement.php">Room Management</a>
                    <a href="staffmanagement.php">Staff management</a>
                    <a href="hostelerManagement.php">Hosteller</a>
                    <a href="usersquery.php">Queries</a>
                    <a href="setting.php">Settings</a>
                    <button class="btn w-100" ><a href="../index.php">LOG OUT</a></button>
                </div>
        <div class="col-md-10">
            <h2 class="mt-4 mb-4">User Queries</h2>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">

                <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
                    <table class="table table-hover border">
                        <thead class="sticky-top">
                            <tr class= "bg-dark text-light">
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col" width="30%">Message</th>
                            <th scope="col">Date</th>
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php
                                $q = "SELECT * FROM `queries` ORDER BY `sr_no` DESC"; 
                                
                                $data = mysqli_query($conn, $q);

                                if ($data === false) {
                                    die("Error in query: " . mysqli_error($conn));
                                }
                                $i = 1;

                                while ($row = mysqli_fetch_assoc($data)) {
                                    $seen='';
                                    if($row['seen']!=1){
                                        $seen="<a href='?seen=$row[sr_no]' class='btn btn-sn rounded-pill btn-primary'>Mark as read</a>";
                                    }
                                    $seen.="<a href='?del=$row[sr_no]' class='btn btn-sn rounded-pill btn-danger'>Delete</a>";
                                    echo <<<query
                                    <tr>
                                        <td>$i</td>
                                        <td>$row[name]</td>
                                        <td>$row[email]</td>
                                        <td>$row[message]</td>
                                        <td>$row[date]</td>
                                        <td>$seen</td>
                                    </tr>
                                    query;
                                    $i++;
                                }
                            ?>

                            </tbody>
                    </table>
                </div-table>
                </div>
            </div>
            
        </div>
    </div>
    </div>
    </body>
    </html>

<?php
$conn->close();
?>
