<?php
 ob_start();
 session_start();
 
 $user_id = $_SESSION["user_id"];
 $username = $_SESSION['username'];
 $permission_level = $_SESSION["permission_level"];
 if(!$user_id){
     //redirect to login
     header("Location: https://admin.authenticmerch.com");
 }
 else{
     //add_activity($user_id,"Added User");
 }
 error_reporting( E_ALL );
 ini_set( "display_errors", 1 );

  //DB Server info
  $servername = "localhost";
  $db_username = "fe32045_dev_dustin";
  $db_password = "@TbGG3Fdau1m";
  $db = "fe32045_admin_catalog";
  // Create connection
  global $conn;
  $conn = new mysqli($servername, $db_username, $db_password,$db);
  
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  } 
?>
<!doctype html>
<html lang='en'>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Sales Home - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    </head>
    <body>
        <?php 
            $title="Sales Home";    
            include_once("includes/inc-header.php"); 
        ?>
        <div class="container-fluid skip-nav" style="clear: both;">
            <table class="table table-bordered">
                <thead class="thead thead-light">
                    <th>Name</th>
                    <th>Label</th>
                    <th>Reference Id</th>
                    <th>Links</th>
                </thead>
                <tbody>
                <?php
                    $select_query = "SELECT * FROM `admin_sales_organizations`";
                    $results = mysqli_query($conn, $select_query);

                    while($org = mysqli_fetch_assoc($results)){
                ?>
                    <tr>
                        <td><?php echo $org['company_name']; ?></td>
                        <td><?php echo ($org['label'] == 85?"Customer":""); ?></td>
                        <td><?php echo $org['reference_id']; ?></td>
                        <td><a href="sales_widget.php?client_id=<?php echo $org['reference_id']; ?>"  title="Add New Pricing Quote"><i class="fas fa-plus-circle"></i>Add New Product Quote</a><br></td>
                    </tr>
                <?php
                    }
                ?>
                </tbody>
            </table>

            </table>
        </div>
    </body>
</html>