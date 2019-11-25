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
        //add_activity($user_id,"Visited Dashboard");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_GET['client_id']) and !isset($_POST['client_name'])){
        
        $client_id = $_GET['client_id'];
        $query = "SELECT * FROM `admin_client_details` WHERE `_client_id`=" . $client_id;

        $result = mysqli_query($conn, $query);

        while($row=mysqli_fetch_assoc($result)){
           $client_name             = $row["client_name"]; 
           $client_code             = $row["client_code"];
           $client_stock_held       = $row["client_stock_held"];
           $client_store_number     = $row["client_store_number"];
           $client_store_url        = $row["client_store_url"];
           $client_handles_orders   = $row["client_handles_orders"];
           $client_inventory        = $row["client_inventory"];
           $client_special_projects = $row["client_special_projects"];
           $client_who_owns_stock   = $row["client_who_owns_stock"];
           $client_contract_renewal = $row["contract_renewal_date"];
           $client_line_review      = $row["last_line_review"];

        ?>
            <html>
                <head>
                <title>Edit Client Details - RCHQ Admin Area</title>
                <meta name="robots" content="noindex,nofollow"/>
                <?php
                    include_once("includes/inc-html-header.php");
                ?>
                </head>
                <body>
                <?php include_once("includes/inc-header.php"); ?>
                <div class="container skip-nav">
                    <h1 style="padding-bottom: 20px;">Editing <?php echo $client_name; ?>!</h1>
                    <div class="dashboard-block client-block">
                        <form name="edit_client" method="POST" action="">
                            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
                            <hr/>
                            <ul class="edit_details">
                                <li>Client Name: <input type="text" class="form-control" value="<?php echo $client_name?>" name="client_name"/></li>
                                <li>Client Code: <input type="text" class="form-control" value="<?php echo $client_code?>" name="client_code"/></li>
                                <li>Where is stock held: <input type="text" class="form-control" value="<?php echo $client_stock_held?>" name="client_stock_held"/></li>
                                <li>Client Store Number: <input type="text" class="form-control" value="<?php echo $client_store_number?>" name="client_store_number"/></li>
                                <li>Client Store URL: <input type="text" class="form-control" value="<?php echo $client_store_url?>" name="client_store_url"/></li>
                                <li>Who Handles Orders: <input type="text" class="form-control" value="<?php echo $client_handles_orders?>" name="client_handles_orders"/></li>
                                <li>Who Handles Inventory: <input type="text" class="form-control" value="<?php echo $client_inventory?>" name="client_inventory"/></li>
                                <li>Who Handles Special Projects: <input type="text" class="form-control" value="<?php echo $client_special_projects?>" name="client_special_projects"/></li>
                                <li>Who Owns Stock: <input type="text" class="form-control" value="<?php echo $client_who_owns_stock?>" name="client_who_owns_stock"/></li>
                                <li>Contract Renewal Date: <input type="date" class="form-control" value="<?php echo $client_contract_renewal; ?>" name="contract_renewal_date" required="required"/></li>
                                <li>Last Line Review: <input type="date" class="form-control" value="<?php echo $client_line_review; ?>" name="last_line_review" required="required"/></li>
                            </ul>
                            <input type="submit" value="Save Changes" class="btn btn-primary"/>
                        </form>
                    </div>
                    <?php include_once("includes/inc-footer.php"); ?>
                </body>
            </html>
        <?php
        }
    }
    else{
        //check if post is set and update client, then redirect back to dashboard
        if(isset($_POST["client_name"])){
               $client_id               = $_POST['client_id'];
               $client_name             = $_POST["client_name"]; 
               $client_code             = $_POST["client_code"];
               $client_stock_held       = $_POST["client_stock_held"];
               $client_store_number     = $_POST["client_store_number"];
               $client_store_url        = $_POST["client_store_url"];
               $client_handles_orders   = $_POST["client_handles_orders"];
               $client_inventory        = $_POST["client_inventory"];
               $client_special_projects = $_POST["client_special_projects"];
               $client_who_owns_stock   = $_POST["client_who_owns_stock"];
               $client_contract_renewal = $_POST["contract_renewal_date"];
               $client_line_review      = $_POST["last_line_review"];
               
            $update_query = "UPDATE `admin_client_details` SET `client_name`='" . $client_name . 
                "',`client_code`='" . $client_code . 
                "',`client_stock_held`='" . $client_stock_held . 
                "',`client_store_number`='" . $client_store_number . 
                "',`client_store_url`='" . $client_store_url . 
                "',`client_handles_orders`='" . $client_handles_orders .
                "',`client_inventory`='" . $client_inventory . 
                "',`client_special_projects`='" . $client_special_projects .
                "',`client_who_owns_stock`='" . $client_who_owns_stock .
                "',`contract_renewal_date`='" . $client_contract_renewal .
                "',`last_line_review`='" . $client_line_review .
                "' WHERE `_client_id`=" . $client_id;
                
                

            $result = mysqli_query($conn,$update_query);
            if(!$result){
                echo $update_query;
                die("<br>broken!");
            }
            header("Location: https://admin.authenticmerch.com/dashboard.php");
        }
    }
?>