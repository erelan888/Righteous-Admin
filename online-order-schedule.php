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
    //error_reporting( E_ALL );
    //ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");
    
    ?>
    
<!doctype html>
<html lang='en'>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Online Order Schedule - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
        <?php 
            $title="Online Order Schedule";
            include_once("includes/inc-header.php"); 
        ?>
        <div class="container-fluid skip-nav">
            <table class="table table-striped">
                <thead class="thead thead-dark">
                    <th>Store</th>
                    <th>Order Date</th>
                    <th>Order Number</th>
                    <th>Paperwork Date</th>
                    <th>Due Date</th>
                    <th>Date Finished</th>
                    <th>Quantity</th>
                    <th>App</th>
                    <th>Update</th>
                </thead>
<?php
    $select_query = "SELECT * FROM `admin_production_online_store_schedule`";
    $results = mysqli_query($conn, $select_query);

    if($results && (mysqli_num_rows($results) > 0)){
        while($order = mysqli_fetch_assoc($results)){
            echo "<tr>";
            echo "<td>" . $order['store'] . "</td>";
            echo "<td>" . $order['order_date'] . "</td>";
            echo "<td>" . $order['order_number'] . "</td>";
            echo "<td>" . $order['paperwork_date'] . "</td>";
            echo "<td>" . $order['due_date'] . "</td>";
            echo "<td>" . $order['date_finished'] . "</td>";
            echo "<td>" . $order['quantity'] . "</td>";
            echo "<td>" . $order['application'] . "</td>";
            echo "<td>Things and stuff with buttons</td>";
            echo "</tr>";
        }
    }
?>
            </table>
        </div>
        <?php include_once("includes/inc-footer.php"); ?>
    </body>
</html>