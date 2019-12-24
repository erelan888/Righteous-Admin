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

   /* function generate_sku($client_name, $style_type, $category_type, $submitted_sizes){
        //Generate the sku based on the submitted values
    } */
    function get_client_id($client_name){
        global $conn;
        $client_id_query = "SELECT _client_id FROM `admin_client_details` WHERE `client_name`='" . mysqli_real_escape_string($conn,$client_name) . "';";
        $results = mysqli_query($conn,$client_id_query);
        while($row = mysqli_fetch_assoc($results)){
            return $row['_client_id'];
        }
    }
    function email_PDS_update($email, $user_cc,$email_subject,$output){
        $headers = array("From: product-development@authenticmerch.com",
            "Reply-To: no-reply@authenticmerch.com",
            "Content-type:text/html;charset=UTF-8",
            "CC: " . $user_cc,
            "X-Mailer: PHP/" . PHP_VERSION );

        $headers = implode("\r\n", $headers);

        mail($email,$email_subject,$output,$headers);
    }
    function add_activity($user_id_param, $activity_text){
        //Adds activity to the activity log
    }
    if(isset($_POST["product_name"])){
       //$generated_sku = generate_sku($chosen_client,$internal_style_type, $internal_category_type, $sizes);
        
       $chosen_client                 = $_POST['client'];
       $chosen_site_type              = $_POST['site_type'];
       //product details
       $product_name                  = mysqli_real_escape_string($conn,$_POST['product_name']);
       $product_description           = mysqli_real_escape_string($conn, $_POST['product_description']);
       $product_categories            = mysqli_real_escape_string($conn, $_POST['product_categories']);
       $product_colors                = mysqli_real_escape_string($conn, $_POST['product_colors']);
       $thread_colors                 = mysqli_real_escape_string($conn, $_POST['thread_colors']);
       //product sku information
       $sizes                         = $_POST['size'];
       $size_chart                    = $_POST['size_chart'];
       //prices
       $product_cost_net              = str_replace("$","",$_POST['product_net_cost']);
       $product_corp_price            = str_replace("$","",$_POST['product_corp']);
       $product_retail_price          = str_replace("$","",$_POST['product_retail']);
       $product_upcharges             = $_POST['upcharge'];
       $product_length                = str_replace("\"","",$_POST['product_length']);
       $product_width                 = str_replace("\"","",$_POST['product_width']);
       $product_height                = str_replace("\"","",$_POST['product_height']);
       $product_weight                = $_POST['weight'];
       $product_decoration_array      = $_POST['decoration'];
       $product_deco_instructions     = mysqli_real_escape_string($conn,$_POST['deco_instructions']);
       $product_decoration            = implode("|", $product_decoration_array);
       $product_detail                = mysqli_real_escape_string($conn,$_POST['detail']);
       $product_detail_number         = $_POST['detail_number'];
       $product_decoration_file_name  = mysqli_real_escape_string($conn,$_POST['file_name']);
       $product_stock_ownership       = $_POST['stock_ownership'];
       $product_ships_from            = $_POST['ships_from'];
       $product_minimum_order_quantity= $_POST['min_order_quantity'];
       $product_inventory_quantity    = $_POST['inventory_quantity'];
       $product_inventory_threshold   = $_POST['inventory_threshold'];
       $product_allow_backorder       = $_POST['allow_backorder'];
       $product_inventory_details     = mysqli_real_escape_string($conn,$_POST['inventory_details']);
       $product_inventory_notes       = mysqli_real_escape_string($conn,$_POST['inventory_notes']);
       $product_minimum_stock_reorder = ($_POST['minimum_stock_reorder'] == "" ? 0 : $_POST['minimum_stock_reorder'] );
       $product_marketing_blast       = $_POST['marketing_blast'];
       //approval details and notes
       $corp_approval_date            = $_POST['date_corp_approval'];
       $product_corporate_approval_by = $_POST["product_corporate_approval_by"];
       $sales_rep                     = $_POST['sales_rep'];
       $notes_comments                = mysqli_real_escape_string($conn,$_POST['notes_comments']);
       //Vendor Information
       $vendor_name                   = mysqli_real_escape_string($conn,$_POST['vendor_name']);
       $vendor_product_number         = mysqli_real_escape_string($conn,$_POST['vendor_product_number']);
       $setup_fees                    = mysqli_real_escape_string($conn,$_POST["setup_fees"]);
       $repeat_setups                 = mysqli_real_escape_string($conn,$_POST["repeat_setups"]);
       $other_vendor_charges          = mysqli_real_escape_string($conn,$_POST["other_vendor_charges"]);
       $vendor_art_email              = mysqli_real_escape_string($conn,$_POST["vendor_art_email"]);
       $vendor_phone_number           = mysqli_real_escape_string($conn,$_POST["vendor_phone_number"]);
       $vendor_email                  = mysqli_real_escape_string($conn,$_POST["vendor_email"]);
       $vendor_product_name           = mysqli_real_escape_string($conn,$_POST['vendor_product_number']);


       if(isset($_GET['client_id'])){
           $client_id = $_GET['client_id'];
       }
       else{
           $client_id = get_client_id($chosen_client);
       }
       
       $size_osfm = 0;
       $size_xs   = 0;
       $size_s    = 0;
       $size_m    = 0;
       $size_l    = 0;
       $size_xl   = 0;
       $size_xxl  = 0;
       $size_3xl  = 0;
       $size_4xl  = 0;
       $size_5xl  = 0;
       $size_6xl  = 0;
       $size_na   = 0;

       foreach($sizes as $value){
            if($value == "OSFM"){
               $size_osfm = 1;
            }
            else if($value == "XS"){
                $size_xs = 1;
            }
            else if($value == "S"){
                $size_s = 1;
            }
            else if($value == "M"){
                $size_m = 1;
            }
            else if($value == "L"){
                $size_l = 1;
            }
            else if($value == "XL"){
                $size_xl = 1;
            }
            else if($value == "2XL"){
                $size_xxl = 1;
            }
            else if($value == "3XL"){
                $size_3xl = 1;
            }
            else if($value == "4XL"){
                $size_4xl = 1;
            }
            else if($value == "5XL"){
                $size_5xl = 1;
            }
            else if($value == "6XL"){
                $size_6xl = 1;
            }
            else if($value == "na"){
                $size_na = 1;
            }
       }


       $insert_query = "INSERT INTO `admin_client_products` (client_id, product_name, site_type, product_description, product_categories, vendor"
            . ",vendor_product_number, product_sizing_chart, size_osfm, size_xs, size_s, size_m, size_l, size_xl, size_xxl, size_3xl, size_4xl, size_5xl, size_6xl, size_na,"
            . " colors, pricing_cost_net, pricing_corporate, pricing_retail, pricing_size_upcharge, product_length, product_height, product_width, product_weight, "
            . " product_decoration, product_decoration_details, product_decoration_number, product_decoration_filename, product_stock_ownership, product_ships_from,"
            . " product_minimum_order_quantity, product_inventory_threshold, product_inventory_backorder, product_inventory_details, product_inventory_notes, product_single_product_blast,"
            . " product_corporate_approval, product_sales_rep, product_notes, thread_colors, setup_fees, repeat_setups, other_vendor_charges, vendor_art_email, vendor_phone_number, product_corporate_approval_by,"
            . " minimum_stock_reorder, deco_instructions, vendor_email, vendor_product_name) " 
            . " VALUES ('" . $client_id . "','" 
            . $product_name . "','" 
            . $chosen_site_type . "','" 
            . $product_description . "','" 
            . $product_categories . "','"
            . $vendor_name  . "','" 
            . $vendor_product_number . "','" 
            . ($size_chart == 'Yes' ? 1 : 0) . "','" 
            . $size_osfm . "','" 
            . $size_xs . "','"
            . $size_s . "','" 
            . $size_m . "','" 
            . $size_l . "','" 
            . $size_xl . "','" 
            . $size_xxl . "','"
            . $size_3xl . "','" 
            . $size_4xl . "','" 
            . $size_5xl . "','" 
            . $size_6xl . "','" 
            . $size_na . "','" 
            . $product_colors . "','" 
            . $product_cost_net . "','" 
            . (!empty($product_corp_price) ? $product_corp_price : 0.00) . "','" 
            . (!empty($product_retail_price) ? $product_retail_price : 0.00) . "','" 
            . $product_upcharges . "','" 
            . $product_length . "','" 
            . $product_height . "','" 
            . $product_width . "','" 
            . $product_weight  . "','" 
            . $product_decoration . "','" 
            . $product_detail . "','" 
            . $product_detail_number . "','" 
            . $product_decoration_file_name . "','" 
            . $product_stock_ownership . "','" 
            . $product_ships_from . "','" 
            . (!empty($product_minimum_order_quantity) ? $product_minimum_order_quantity : 0) . "','" 
            . (!empty($product_inventory_threshold) ? $product_inventory_threshold : 0)  . "','" 
            . ($product_allow_backorder == "Yes" ? 1 : 0) . "','" 
            . $product_inventory_details . "','" 
            . $product_inventory_notes . "','" 
            . ($product_marketing_blast == "Yes" ? 1 : 0) . "','" 
            . $corp_approval_date . "','" 
            . $sales_rep . "','" 
            . $notes_comments . "','"
            . $thread_colors . "','"
            . $setup_fees . "','"
            . $repeat_setups . "','"
            . $other_vendor_charges . "','"
            . $vendor_art_email . "','"
            . $vendor_phone_number . "','"
            . $product_corporate_approval_by . "','"
            . $product_minimum_stock_reorder . "','"
            . $product_deco_instructions . "','"
            . $vendor_email . "','"
            . $vendor_product_name . "');";
    
    
    
        if($conn->query($insert_query) === TRUE){
            $message = "PDS Created. View the catalog <a href='https://admin.authenticmerch.com/catalog.php?client_id=" . get_client_id($chosen_client) . "'>here</a>, or create another below";
            $message_type = "success";

            $output = "<h5 style='text-align:center;'>New Product: " . $product_name . "</h5>"
                . "<p style='text-align:center;'><a href='https://admin.authenticmerch.com/catalog.php?client_id=" . $client_id . "'>View Client Catalog here</a>" 
                . "<p style='text-align:center;'><a href='https://admin.authenticmerch.com/edit_product.php?product_id=" . mysqli_insert_id($conn) . "'>View New PDS here</a>" ;
                email_PDS_update("dustin@rchq.com",null,"New PDS Created - " . $chosen_client,$output);
        }
        else{
            ?>
            <div style="margin-left: 300px;">
            <?php
                $output = $insert_query . " ERROR: " . mysqli_error($conn);
                email_PDS_update("dustin@rchq.com",null,"Error Creating PDS - " . $chosen_client,$output);
                echo $insert_query;
                echo mysqli_error($conn);
            ?>
            </div>
            <?php
        }
    }
        ?>
    <html>
        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Add Product Development Sheet - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        </head>
        <body>
        <?php $title="Create New Product (PDS)";
        include_once("includes/inc-header.php"); ?>
        <div class="container-fluid skip-nav">
            <h1 style="padding-bottom: 20px;">Create New Product (PDS)</h1>
            <div class="dashboard-block client-block">
                <form name="edit_user" method="POST" action="">
                    <hr/>
                    <?php
                        //Get client list from database so you don't have to manually update this field, which would be torture. 
                        if(isset($_GET['client_id'])){
                            $client_query = "SELECT * FROM `admin_client_details` WHERE _client_id=" . $_GET['client_id'];
                            $clients = mysqli_query($conn,$client_query);
    
                            if(!$clients){
                                $message .= " Please try again, could not collect client list";
                                $message_type = "fail";
                            }
                        }
                        else{
                            $client_query = "SELECT * FROM `admin_client_details`";
                            $clients = mysqli_query($conn,$client_query);
    
                            if(!$clients){
                                $message .= " Please try again, could not collect client list";
                                $message_type = "fail";
                            }
                        }
                    ?>
                    <?php
                        if(!empty($message) and $message_type == "fail"){
                    ?>
                            <p class="alert alert-danger"><?php echo $message; ?></p>
                    <?php
                        } 
                        else if (!empty($message) and $message_type == "success"){
                    ?>
                            <p class="alert alert-success"><?php echo $message; ?></p>
                    <?php
                        }
                    ?>
                    <div class="row">
                    <div class="col">
                    <div class="card">
                    <h6 class="card-header">Details</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Choose Client*: <select required="required" class="form-control" name="client">
                            <?php
                                    if(!isset($_GET['client_id'])){
                            ?>
                                        <option name="Choose Client" name="none_chosen">Choose Client</option>
                            <?php
                                    }
                            ?>
                                    
                                <?php
                                    while($row = mysqli_fetch_assoc($clients)){
                                        echo "<option name=\"" . $row['client_name'] . "\">" . $row['client_name'] ."</option>";
                                    }
                                ?>
                                </select>
                        </li>
                        <li>Site Type: <select required="required" class="form-control" name="site_type">
                            <option name="retail">Retail</option>
                            <option name="corp">Corp</option>
                            <option name="franchise">Franchise</option></select></li>
                        <li>Product Name*: <input type="text" class="form-control" value="" name="product_name" required="required"/></li>
                        <li>Product Description*: <input type="text" class="form-control" value="" name="product_description" required="required"/></li>
                        <li>Product Categories*: <select required="required" class="form-control" name="product_categories">
                                <option name="Choose Category" value="Choose Category">Choose Category</option>
                        <?php
                         if(isset($client_id)){
                            $category_query = "SELECT * FROM `admin_client_woocommerce_categories` WHERE `client_id`=" . $client_id;
                            $cat_results = mysqli_query($conn, $category_query);
                        
                            if(!empty($cat_results)){
                                $product_category_list = "SELECT * FROM `admin_client_woocommerce_categories` WHERE `client_id`=" . $client_id;
                                $category_results = mysqli_query($conn, $product_category_list);
                                while($category = mysqli_fetch_assoc($category_results)){
                                    $option =  "<option name='" . $category['category_id'] . "|" . $category['category_title'] 
                                        . "' value='" . $category['category_id'] . "|" . $category['category_title'] . "'> " . $category['category_id'] . "|" . $category['category_title'] . "</option>";
                                    echo $option;
                                }
                                echo "</select><li>";
                            }
                            else{
                                echo "<input type='text' class=\"form-control\" name=\"product_categories\" required=\"required\"></li>";
                            }
                        }
                        else{
                            echo "<input type='text' class=\"form-control\" name=\"product_categories\" required=\"required\"></li>";
                        }
                        ?>
                        </select></li>
                        <li>Colors: <input type="text" class="form-control" value="" name="product_colors"/></li>
                        </ul>
                        </div>
                        </div>
                        </div>
                        <div class="col">
                        <div class="card" style="height: 100%;">
                        <h6 class="card-header">Sizes</h6>
                        <div class="card-body">
                        <ul class="edit_details">
                        <li>Sizes<fieldset required="required">
                                    <input type="checkbox" name="size[]" value="OSFM"/>OSFM <br>
                                    <input type="checkbox" name="size[]" value="XS"/>XS <br>
                                    <input type="checkbox" name="size[]" value="S"/>S <br>
                                    <input type="checkbox" name="size[]" value="M"/>M <br>
                                    <input type="checkbox" name="size[]" value="L"/>L <br>
                                    <input type="checkbox" name="size[]" value="XL"/>XL <br>
                                    <input type="checkbox" name="size[]" value="2XL"/>2XL <br>
                                    <input type="checkbox" name="size[]" value="3XL"/>3XL <br>
                                    <input type="checkbox" name="size[]" value="4XL"/>4XL <br>
                                    <input type="checkbox" name="size[]" value="5XL"/>5XL <br>
                                    <input type="checkbox" name="size[]" value="6XL"/>6XL <br>
                                    <input type="checkbox" name="size[]" value="na"/>N/A <br>
                                </fieldset></li>
                        <li>Include Size Chart: <select class="form-control" name="size_chart">
                            <option name="No" value="No">No</option>
                            <option name="Yes" value="Yes">Yes</option>
                                </select></li>
                        
                    </ul>
                    </div>
                    </div>
                    </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col">
                        <div class="card">
                    <h6 class="card-header">Pricing Information</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Do not include "$" in the pricing fields and please put any deco/personalization upcharge in the decoration detail below</li>
                        <li>Pricing (Net Cost)*:<input type="text" class="form-control" value="" name="product_net_cost" required="required"/></li>
                        <li>Pricing (Corp):<input type="text" class="form-control" value="" name="product_corp"/></li>
                        <li>Pricing (Retail):<input type="text" class="form-control" value="" name="product_retail"/></li>
                        <li>Upcharge & Teired Pricing (Size - Upcharge):<textarea rows='3' class="form-control" value="" name="upcharge"></textarea></li>
                        </ul>
                    </div>
                    </div>
                    </div>
                    <div class="col">
                    <div class="card" style="height: 100%;">
                    <h6 class="card-header">Dimension Information</h6>
                    <div class="card-body">
                        <script type="text/javascript">
                            function autoFillDimensions(height, width, length, weight){
                                $(".product_height").val(height);
                                $(".product_width").val(width);
                                $(".product_length").val(length);
                                $(".weight").val(weight);
                            }
                        </script>
                        <p>Autofill Options:<a onclick="autoFillDimensions(9,11,.75,.33)" style="cursor: pointer; color: #fff;" class="btn btn-primary">Shirts</a> <a onclick="autoFillDimensions(9,11,3,.65)" style="cursor: pointer; color: #fff;" class="btn btn-primary">Outerwear</a> <a onclick="autoFillDimensions(4,7,7,.15)" style="cursor: pointer;  color: #fff;"  class="btn btn-primary">Hats</a></p>
                        <ul class="edit_details">
                            <li>Height (in Inches)*:<input type="text" class="form-control product_height" value="" name="product_height" required="required"/></li>
                            <li>Width (in Inches)*:<input type="text" class="form-control product_width" value="" name="product_width" required="required"/></li>
                            <li>Length (in Inches)*:<input type="text" class="form-control product_length" value="" name="product_length" required="required"/></li>
                            <li>Weight (in lbs)*:<input type="text" class="form-control weight" value="" name="weight" required="required"/></li>
                        </ul>
                        </div>
                        </div>
                        </div>
                        </div>
                    <div class="row" style="margin-top: 20px;">
                    <div class="col">
                    <div class="card">
                    <h6 class="card-header">Decoration Information</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Decoration:<br/><fieldset>
                                    <input type="checkbox" name="decoration[]" value="print"/>Print<br>
                                    <input type="checkbox" name="decoration[]" value="heatpress"/>Heatpress<br>
                                    <input type="checkbox" name="decoration[]" value="embroidery"/>Embroidery<br>
                                    <input type="checkbox" name="decoration[]" value="sew"/>Sew<br>
                                </fieldset></li>
                        <li><b>Deco Instructions:</b><br><p>In the field below, please describe the decoration of this product. Use one line per location following this format:<br>
                         <i>Deco location | deco identifier [file name, patch number, screen number] | Color/Thread color</i>
                            <textarea class="form-control" name="deco_instructions" rows="5" placeholder="Left Chest | #1375 | Silver Thread"></textarea> </li>
                        <li>Detail:<input type="text" class="form-control" value="" name="detail"/></li>
                        <li>Number:<input type="text" class="form-control" value="" name="detail_number"/></li>
                        <li>File Name:<input type="text" class="form-control" value="" name="file_name"/></li>
                        <li>Thread Colors: <input type="text" class="form-control" value="" name="thread_colors"/></li>
                    </ul>   
                    </div>
                        </div>
                        </div>
                        </div>
                    <div class="row" style="margin-top: 20px;">
                    <div class="col">
                    <div class="card">
                    <h6 class="card-header">Inventory Information</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Stock Ownership*:<br/><select class="form-control" name="stock_ownership" required="required">
                                    <option value="righteous">Righteous Clothing</option>
                                    <option value="client">Client</option>
                                    <option value="nonstock">Nonstock</option>
                                </select></li>
                        <li>Ships From*:<br/><select class="form-control" name="ships_from" required="required">
                                    <option value="dropship">Dropship</option>
                                    <option value="missouri">Missouri</option>
                                    <option value="clackamas">Clackamas</option>
                        </select></li>
                        <li>Customer Minimum Order Quantity:<input type="text" class="form-control" value="" name="min_order_quantity"/></li>
                        <li>Starting Inventory Quantity:<input type="text" class="form-control" value="" name="inventory_quantity"/></li>
                        <li>Inventory Threshold:<input type="text" class="form-control" value="" name="inventory_threshold"/></li>
                        <li>Allow Backorder: <select class="form-control" name="allow_backorder">
                            <option name="No">No</option>
                            <option name="Yes">Yes</option>
                                </select></li>
                        <li>Minimum Stock Reorder:<input type="text" class="form-control" value="" name="minimum_stock_reorder"/></li>
                        <li>Inventory Details:<br/><select class="form-control" name="inventory_details">
                            <option value="autoreplenish"/>Autoreplenish</option>
                            <option value="corporate_approval"/>Corporate Approval</option>
                            <option value="sales_rep_approval"/>Sales Rep Approval</option>
                        </select></li>
                        <li>Inventory Notes:<input type="text" class="form-control" value="" name="inventory_notes"/></li>
                    </ul>   
                    </div>
                        </div>
                        </div>
                        </div>
                    <div class="row" style="margin-top: 20px;">
                    <div class="col">
                    <div class="card">                                
                    <h6 class="card-header">Approval Information</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Corporate Approval Date: <input type="date" name="date_corp_approval" /></li>
                        <li>Corporate Approval By: <input type="text" class="form-control" value="" name="product_corporate_approval_by"/></li>
                        <li>Marketing: Single item blast once live? <select class="form-control" name="marketing_blast">
                            <option name="No">No</option>
                            <option name="Yes">Yes</option>
                        </select></li>
                        <li>Sales Rep: <input type="text" class="form-control" value="" name="sales_rep"/></li>
                        <li>Notes/Additional Comments: <input type="text" class="form-control" value="" name="notes_comments"/></li>
                    </ul>
                    </div>
                    </div>
                    </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                    <div class="col">
                    <div class="card">
                    <h6 class="card-header">Vendor Information</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Vendor Name*:<input type="text" class="form-control" value="" name="vendor_name" required="required"/></li>
                        <li>Vendor Product Number*:<input type="text" class="form-control" value="" name="vendor_product_number" required="required"/></li>
                        <li>Vendor Product Name*:<input type="text" class="form-control" value="" name="vendor_product_name" required="required"/></li>
                        <li>Setup Fees:<input type="text" class="form-control" value="" name="setup_fees"/></li>
                        <li>Repeat Setups:<input type="text" class="form-control" value="" name="repeat_setups"/></li>
                        <li>Other Vendor Charges:<input type="text" class="form-control" value="" name="other_vendor_charges"/></li>
                        <li>Art Email:<input type="text" class="form-control" value="" name="vendor_art_email"/></li>
                        <li>Phone Number:<input type="text" class="form-control" value="" name="vendor_phone_number"/></li>
                        <li>Vendor Email:<input type="text" class="form-control" value="" name="vendor_email"/></li>
                    </ul>
                    <br>
                    </div>
                    </div>
                    </div>
                    </div>
                    <input type="submit" value="Save Changes" class="btn btn-primary"/>
                </form>
            </div>
            <?php include_once("includes/inc-footer.php"); ?>
        </body>
    </html>