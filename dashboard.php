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
    //error_reporting( E_ALL );
    //ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");
    
    ?>
    
<!doctype html>
<html lang='en'>
    <head>
        <title>Dashboard - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
        <?php include_once("includes/inc-header.php"); ?>
        <div class="container skip-nav">
            <h1 style="padding-bottom: 20px;">Welcome, <?php echo $username; ?>!</h1>
            <div class="dashboard-block client-block">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <th class="fancy-name">Client Name</th>
                        <th class="fancy-name">Store Number</th>
                        <th class="fancy-name">Client Code</th>
                    </thead>
                    <tbody>
                        <?php
                            $client_query= "SELECT * FROM `admin_client_details` ORDER BY `client_name` ASC";
                            $client_results = mysqli_query($conn,$client_query);
                            
                            if(!empty($client_results)){
                                while($row=mysqli_fetch_assoc($client_results)){
                                    $client_id               = $row["_client_id"];
                                    $client_store_number     = $row["client_store_number"];
                                    $client_name             = $row["client_name"];
                                    $client_code             = $row["client_code"];
                                    $client_store_url        = $row["client_store_url"];
                                    $client_stock_held       = $row["client_stock_held"];
                                    $client_handles_orders   = $row["client_handles_orders"];
                                    $client_inventory        = $row["client_inventory"];
                                    $client_special_projects = $row["client_special_projects"];
                                    $client_who_owns_stock   = $row["client_who_owns_stock"];
                                    $client_contract_renewal = $row["contract_renewal_date"];
                                    $client_line_review      = $row["last_line_review"];
                                    ?>
                                    <tr>
                                        <td><b class="fancy-name"><?php echo $client_name; ?></b></td>
                                        <td><?php echo $client_store_number; ?></td>
                                        <td><?php echo $client_code; ?></td>
                                    </tr>
                                    <tr class="data_row">
                                        <td colspan="2">
                                            <ul class="data_list">
                                                <li>Store URL: <a href="<?php echo $client_store_url; ?>"><?php echo $client_store_url;?></a></li>
                                                <li>Stock Owned By: <b><?php echo $client_who_owns_stock; ?></b></li>
                                                <li>Stock Held: <b><?php echo $client_stock_held; ?></b></li>
                                                <li>Handles Orders: <b><?php echo $client_handles_orders; ?></b></li>
                                                <li>Inventory: <b><?php echo $client_inventory; ?></b></li>
                                                <li>Special Projects:<b> <?php echo $client_special_projects; ?></b></li>
                                                <li>Contract Renewal Date:<b> <?php echo $client_contract_renewal; ?></b></li>
                                                <li>Last Line Review:<b> <?php echo $client_line_review; ?></b></li>
                                            </ul>
                                        </td>
                                        <td class="links-cell">
                                        <a href="sales_dashboard.php?client_id=<?php echo $client_id; ?>" title="Sales Dashboard"><i class="fas fa-chart-bar"></i>Sales Dashboard</a><br>
                                        <a href="catalog.php?client_id=<?php echo $client_id; ?>" title="View Client Catalog"><i class="fas fa-book-open"></i>Product Catalog</a><br>
                                        <a href="add_product.php?client_id=<?php echo $client_id; ?>"  title="Add New PDS"><i class="fas fa-plus-circle"></i>Add New PDS</a><br>
                                        <a href="inventory.php?client_id=<?php echo $client_id; ?>" title="Inventory Admin"><i class="fas fa-box-open"></i>Inventory Admin</a><br>
                                        <a href="edit_client.php?client_id=<?php echo $client_id; ?>" title="Edit Client Info"><i class="fas fa-edit"></i>Edit Client</a><br>
                                        <?php
                                            if($username=="dustingunter"){
                                        ?>
                                            <a href="edit_client_credentials.php?client_id=<?php echo $client_id; ?>" title="Edit Client Info"><i class="fas fa-edit"></i>Edit API Credentials</a><br>
                                            <a href=".php?client_id=<?php echo $client_id; ?>" title="Testing" style="cursor: not-allowed;"><i class="fas fa-sync"></i>Testing</a>
                                        </td>
                                        <?php 
                                            }
                                        ?>
                                    </tr>
                                    <?php
                                }
                            }
                            else{
                                ?>
                                <tr>
                                    <td colspan="4">
                                        <p class="alert alert-danger">No clients found... This is not supposed to happen</p>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include_once("includes/inc-footer.php"); ?>
    </body>
</html>