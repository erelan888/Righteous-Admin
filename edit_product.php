<?php
    ob_start();
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    $permission_level = $_SESSION["permission_level"];
    if(!$user_id){
        header("Location: https://admin.authenticmerch.com");
    }
    else{
        //add_activity($user_id,"Visited Dashboard");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    global $consumer_key;
    global $consumer_secret;
    global $conn;
    include_once("includes/inc-dbc-conn.php");

    function add_activity($user_id_param, $activity_text){
        //Adds activity to the activity log
    }

    if(isset($_GET["product_id"])){
         if(isset($_GET['message'])){
             $message = $_GET['message'];
             if(!isset($_GET['message_type'])){
                $message_type = "fail";
             }
             else{
                 $message_type = $_GET['message_type'];
             }
             
         }
        $product_id = $_GET['product_id'];
        $query = "SELECT * FROM `admin_client_products` WHERE `_id`=" . $product_id;

        $result = mysqli_query($conn, $query);
        $product_details = mysqli_fetch_assoc($result);

        $client_id                      = $product_details['client_id'];
        $site_type                      = $product_details['site_type'];
        //product details
        $product_name                   = $product_details['product_name'];
        $product_description            = $product_details['product_description'];
        $product_categories             = $product_details['product_categories'];
        $product_colors                 = $product_details['colors'];
        $product_thread_colors          = $product_details['thread_colors'];
        
        $size_chart                     = $product_details['product_sizing_chart'];
        
        $product_cost_net               = $product_details['pricing_cost_net'];
        $product_corp_price             = $product_details['pricing_corporate'];
        $product_retail_price           = $product_details['pricing_retail'];
        $product_upcharges              = $product_details['pricing_size_upcharge'];

        $product_length                 = $product_details['product_length'];
        $product_width                  = $product_details['product_width'];
        $product_height                 = $product_details['product_height'];
        $product_weight                 = $product_details['product_weight'];

        $product_decoration             = $product_details['product_decoration'];
        $product_detail                 = $product_details['product_decoration_details'];
        $product_deco_instructions      = $product_details['deco_instructions'];
        $product_detail_number          = $product_details['product_decoration_number'];
        $product_decoration_file_name   = $product_details['product_decoration_filename'];
        $product_stock_ownership        = $product_details['product_stock_ownership'];
        $product_ships_from             = $product_details['product_ships_from'];
        $product_minimum_order_quantity = $product_details['product_minimum_order_quantity'];
        $product_inventory_quantity     = $product_details['inventory_quantity'];
        $product_inventory_threshold    = $product_details['product_inventory_threshold'];
        $product_allow_backorder        = $product_details['product_inventory_backorder'];
        $product_inventory_details      = $product_details['product_inventory_details'];
        $product_inventory_notes        = $product_details['product_inventory_notes'];
        $product_minimum_stock_reorder  = $product_details['minimum_stock_reorder'];
        $product_marketing_blast        = $product_details['product_single_product_blast'];

        $product_vendor                 = $product_details['vendor'];
        $product_vendor_product_number  = $product_details['vendor_product_number'];
        $product_sales_rep              = $product_details['product_sales_rep'];
        $product_notes                  = $product_details['product_notes'];
        $product_discontinued           = $product_details['is_discontinued'];

        $product_corporate_approval_by  = $product_details['product_corporate_approval_by'];
        $product_corp_approval_date     = $product_details['product_corporate_approval'];
        $setup_fees                     = $product_details['setup_fees'];
        $repeat_setups                  = $product_details['repeat_setups'];
        $other_vendor_charges           = $product_details['other_vendor_charges'];
        $vendor_art_email               = $product_details['vendor_art_email'];
        $vendor_phone_number            = $product_details['vendor_phone_number'];
        $vendor_email                   = $product_details['vendor_email'];
        $vendor_product_name            = $product_details['vendor_product_name'];

        //sizes
        $product_sizes_osfm             = $product_details['size_osfm'];
        $product_sizes_xs               = $product_details['size_xs'];
        $product_sizes_s                = $product_details['size_s'];
        $product_sizes_m                = $product_details['size_m'];
        $product_sizes_l                = $product_details['size_l'];
        $product_sizes_xl               = $product_details['size_xl'];
        $product_sizes_xxl              = $product_details['size_xxl'];
        $product_sizes_3xl              = $product_details['size_3xl'];
        $product_sizes_4xl              = $product_details['size_4xl'];
        $product_sizes_5xl              = $product_details['size_5xl'];
        $product_sizes_6xl              = $product_details['size_6xl'];
        $product_sizes_na               = $product_details['size_na'];
        $product_uploaded               = $product_details['product_uploaded'];
           

        ?>
            <html>
                <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Edit Products Details - RCHQ Admin Area</title>
                <meta name="robots" content="noindex,nofollow"/>
                <?php
                    include_once("includes/inc-html-header.php");
                ?>
                </head>
                <body>
                <?php
                    $title="Edit PDS"; 
                    include_once("includes/inc-header.php"); 
                ?>
                <div class="container-fluid skip-nav">
                    <?php
                        $client_query = "SELECT * FROM `admin_client_details` WHERE _client_id=" . $client_id;
                        $clients = mysqli_query($conn,$client_query);
                        
                        if(!$clients){
                            $message .= " Please try again, could not collect client list";
                            $message_type = "fail";
                        }
                        
                    ?>
                    <?php
                        if(!empty($message) and $message_type == "fail"){
                    ?>
                            <p class="alert alert-danger"><?php echo $message; ?></p>
                    <?php
                        } 
                        else if (!empty($message) and $message_type == "fail"){
                    ?>
                            <p class="alert alert-success"><?php echo $message; ?></p>
                    <?php
                        }
                        $clients_data = mysqli_fetch_assoc($clients);
                        $credentials_query = "SELECT * FROM `admin_client_details_api_woocommerce` WHERE `_client_id`=" . $client_id;
                
                        $cred_results    = mysqli_query($conn, $credentials_query);
                        $credentials     = mysqli_fetch_assoc($cred_results);
                        $consumer_secret = $credentials['consumer_secret'];
                        $consumer_key    = $credentials['consumer_key'];
                        $client_url = $clients_data['client_store_url'];

                        $notes_count_query = "SELECT * FROM `admin_product_notes` WHERE _product_id=" . $product_id . ";";
                        $notes_count_results = mysqli_query($conn, $notes_count_query);
                        $notes_count = mysqli_num_rows($notes_count_results);
                    ?>
                    <h1>Editing: <?php echo $clients_data['client_name']; ?></h1>
                    <h5 style="color: #D02030; font-style:italic;"><?php echo $product_name ?></h5>
                    <p><span class="badge badge-info"><?php echo $product_vendor ?></span> <span class="badge badge-info"><?php echo $product_vendor_product_number ?></span> <span class="badge badge-info"> <?php echo $product_stock_ownership; ?></span></p>
                    <div class="dashboard-block client-block">
                    <p>
                        <a href="https://admin.authenticmerch.com/catalog.php?client_id=<?php echo $client_id; ?>" class="btn"> <i class="fas fa-undo"></i> Back To Catalog</a> 
                        <a href="https://admin.authenticmerch.com/delete_product.php?product_id=<?php echo $product_id; ?>&client_id=<?php echo $client_id; ?>" class="btn"> <i class="fas fa-trash-alt"></i> Delete This Product</a>
                        <a href="https://admin.authenticmerch.com/add_skus.php?product_id=<?php echo $product_id; ?>&client_id=<?php echo $client_id; ?>" class="btn"><i class="fas fa-cogs"></i> Generate Skus</a>
                        <a class="btn" onclick="$('.notes-side-block').toggle(300);" style="cursor: pointer"><i class="far fa-clipboard"></i> Product Notes <span style="color: #D02030;">(<b><?php echo $notes_count; ?></b>)
                        <a class="btn" onclick="$('#attachment_form').toggle(300);" style="cursor:pointer;"><i class="far fa-image"></i> Add Image</a>
                        <a class="btn" href="https://admin.authenticmerch.com/duplicate_pds.php?product_id=<?php echo $product_id; ?>"><i class="fas fa-copy"></i> Duplicate PDS</a>
                        <?php if($product_uploaded == 0 && ($product_sizes_osfm ==1 || $product_sizes_na==1)){ ?>
                        <a class="btn btn-info" id="sentToWoo" style="cursor: pointer; color: #fff;"><i class="fas fa-paper-plane"></i> Send to Woo</a>
                        <?php }?>
                    </span></a></p>
                    <form name="edit_product" method="POST" action="">
                    <hr/>
                    <!-- Display uploaded images block -->
                    <?php
                        $image_query = "SELECT * FROM `admin_product_attachments` WHERE `product_id`=" . $product_id;
                        $image_results = mysqli_query($conn, $image_query);

                        if(mysqli_num_rows($image_results) > 0){
                            ?>
                        <ul id="image-block">
                            <?php
                            while($image = mysqli_fetch_assoc($image_results)){
                                echo "<li><img src='" . $image['file_name'] . "'/><a class='btn delete_image' href='delete_image.php?product_id=" 
                                . $product_id . "&file_name=" . $image['file_name'] . "'>Delete Image</a></li>";
                            }
                            ?>
                        </ul>
                            <?php
                        }
                    ?>
                    <!-- END Image display -->
                    <hr style="clear: both;"/>
                    <div class="row">
                    <div class="col">
                        <div class="card">
                        <h6 class="card-header">Details</h6>
                        <div class="card-body">
                            <ul class="edit_details">
                                <li>Is Discontinued?<input <?php echo ($product_discontinued == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="is_discontinued" value="discontinued" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/><br>
                                            </li>
                                <li>Site Type: <select required="required" class="form-control" name="site_type">
                                    <option <?php echo (strpos($site_type,"Retail") > -1 ? "selected" : ""); ?> name="retail" value="Retail">Retail</option>
                                    <option <?php echo (strpos($site_type,"Corp") > -1 ? "selected" : ""); ?> name="corp" value="Corp">Corp</option>
                                    <option <?php echo (strpos($site_type,"Franchise") > -1 ? "selected" : ""); ?> name="franchise" value="Franchise">Franchise</option></select></li>
                                <li>Product Name: <input type="text" class="form-control" value="<?php echo $product_name; ?>" name="product_name" required="required"/></li>
                                
                                <li>Product Description: <input type="text" class="form-control" value="<?php echo str_replace('"', "", $product_description); ?>" name="product_description" required="required"/></li>
                                <!-- TODO: get categories from database, then add selected code -->
                                <li>Product Categories:<select required="required" class="form-control" name="product_categories">
                                    <option name="Choose Category" value="Choose Category">
                                <?php
                                    $product_category_list = "SELECT * FROM `admin_client_woocommerce_categories` WHERE `client_id`=" . $client_id;
                                    $category_results = mysqli_query($conn, $product_category_list);
                                    while($category = mysqli_fetch_assoc($category_results)){
                                        $option =  "<option name='" . $category['category_id'] . "|" . $category['category_title'] 
                                            . "' value='" . $category['category_id'] . "|" . $category['category_title'] . "'";
                                        if((strpos($category['category_title'],$product_categories) > -1) || (strpos($category['category_id'] . "|" . $category['category_title'], $product_categories)> -1)){
                                            $option .= " selected='selected' > " . $category['category_id'] . "|" . $category['category_title'] . "</option>";
                                        }
                                        else{
                                            $option .= "> " . $category['category_id'] . "|" . $category['category_title'] . "</option>";
                                        }
                                        echo $option;
                                    }
                                ?>
                                </select></li>
                                <li>Colors: <input type="text" class="form-control" value="<?php echo $product_colors; ?>" name="colors"/></li>
                                <li>Thread Colors: <input type="text" class="form-control" value="<?php echo $product_thread_colors; ?>" name="thread_colors"/></li>
                            </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                    <div class="card" style="height: 100%;">
                    <h6 class="card-header">Sizes</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li><fieldset required="required">
                                    <input <?php echo ($product_sizes_osfm == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_osfm" value="OSFM" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>OSFM <br>
                                    <input <?php echo ($product_sizes_xs   == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_xs" value="XS" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>XS <br>
                                    <input <?php echo ($product_sizes_s    == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_s" value="S" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>S <br>
                                    <input <?php echo ($product_sizes_m    == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_m" value="M" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>M <br>
                                    <input <?php echo ($product_sizes_l    == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_l" value="L" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>L <br>
                                    <input <?php echo ($product_sizes_xl   == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_xl" value="XL" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>XL <br>
                                    <input <?php echo ($product_sizes_xxl  == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_xxl" value="2XL" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>2XL <br>
                                    <input <?php echo ($product_sizes_3xl  == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_3xl" value="3XL" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>3XL <br>
                                    <input <?php echo ($product_sizes_4xl  == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_4xl" value="4XL" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>4XL <br>
                                    <input <?php echo ($product_sizes_5xl  == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_5xl" value="5XL" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>5XL <br>
                                    <input <?php echo ($product_sizes_6xl  == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_6xl" value="6XL" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>6XL <br>
                                    <input <?php echo ($product_sizes_na   == 1 ? "checked" : ""); ?> class="form-control" type="checkbox" name="size_na" value="na" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>N/A <br>
                                </fieldset></li>
                        <li>Include Size Chart: <select class="form-control" name="product_sizing_chart">
                            <option <?php echo ($size_chart == 0 ? "selected" : ""); ?> name="No" value="No">No</option>
                            <option <?php echo ($size_chart == 1 ? "selected" : ""); ?> name="Yes" value="Yes">Yes</option>
                                </select></li>
                        </ul>
                        </div>
                        </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                    <div class="col">
                    <div class="card">
                        <h6 class="card-header">Pricing</h6>
                        <div class="card-body">
                        <ul class="edit_details">
                            <li>Pricing (Net Cost):<input type="number" class="form-control" value="<?php echo $product_cost_net; ?>" name="pricing_cost_net" required="required"/></li>
                            <li>Pricing (Corp):<input type="number" class="form-control" value="<?php echo $product_corp_price; ?>" name="pricing_corporate"/></li>
                            <li>Pricing (Retail):<input type="number" class="form-control" value="<?php echo $product_retail_price; ?>" name="pricing_retail"/></li>
                            <li>Upcharge (Size - Upcharge):<input type="text" class="form-control" value="<?php echo $product_upcharges; ?>" name="pricing_size_upcharge"/></li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    <div class="col">
                    <div class="card">
                    <h6 class="card-header">Dimension Information</h6>
                    <div class="card-body">
                    <ul class="edit_details">
                        <li>Height (in Inches):<input type="text" class="form-control" value="<?php echo $product_height; ?>" name="product_height" required="required"/></li>
                        <li>Width (in Inches):<input type="text" class="form-control" value="<?php echo $product_width; ?>" name="product_width" required="required"/></li>
                        <li>Length (in Inches):<input type="text" class="form-control" value="<?php echo $product_length; ?>" name="product_length" required="required"/></li>
                        <li>Weight (in lbs):<input type="text" class="form-control" value="<?php echo $product_weight; ?>" name="product_weight" required="required"/></li>
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
                        <li><b>Decoration:</b><br/><fieldset>
                                    <input <?php echo (strpos($product_decoration,"print") > -1 ? "checked" : ""); ?> class="decoration" type="checkbox" name="product_decoration" value="print" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>Print<br>
                                    <input <?php echo (strpos($product_decoration,"heatpress") > -1 ? "checked" : ""); ?> class="decoration" type="checkbox" name="product_decoration" value="heatpress" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>Heatpress<br>
                                    <input <?php echo (strpos($product_decoration,"embroidery") > -1 ? "checked" : ""); ?> class="decoration" type="checkbox" name="product_decoration" value="embroidery" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>Embroidery<br>
                                    <input <?php echo (strpos($product_decoration,"sew") > -1 ? "checked" : ""); ?> class="decoration" type="checkbox" name="product_decoration" value="sew" style="width: auto !important; display: inline !important; line-height: 1 !important; height: auto !important;"/>Sew<br>
                                </fieldset></li>
                        <li>Deco Instructions:<textarea class="form-control"  name="deco_instructions"><?php echo $product_deco_instructions; ?></textarea></li>
                        <li>Detail:<input type="text" class="form-control" value="<?php echo $product_detail; ?>" name="product_decoration_details"/></li>
                        <li>Number:<input type="text" class="form-control" value="<?php echo $product_detail_number; ?>" name="product_decoration_number"/></li>
                        <li>File Name:<input type="text" class="form-control" value="<?php echo $product_decoration_file_name; ?>" name="product_decoration_filename"/></li>
                    </ul>
                        <ul class="image_block" style="list-style-type: none;">
                        <?php
                            if(strpos($product_decoration_file_name,",") == false){
                                $select_query = "SELECT image_url FROM `admin_embroidery_design_list` WHERE embroidery_file_name=" . $product_decoration_file_name;
                                $results = mysqli_query($conn, $select_query);
                                if($results){
                                    while($image = mysqli_fetch_assoc($results)){
                            ?>
                                        <li><img src="<?php echo $image['image_url']; ?>"/></li>
                            <?php
                                    }
                                }
                            }
                        ?>
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
                        <li>Stock Ownership:<br/><select class="form-control" name="product_stock_ownership" required="required">
                                    <option <?php echo (strpos($product_stock_ownership,"righteous") > -1 ? "selected" : ""); ?> value="righteous"/>Righteous Clothing</option>
                                    <option <?php echo (strpos($product_stock_ownership,"client") > -1 ? "selected" : ""); ?> value="client"/>Client</option>
                                    <option <?php echo (strpos($product_stock_ownership,"nonstock") > -1 ? "selected" : ""); ?> value="nonstock"/>Nonstock</option>
                                </select></li></li>
                        <li>Ships From:<br/><select class="form-control" name="product_ships_from" required="required">
                                    <option <?php echo (strpos($product_ships_from,"dropship") > -1 ? "selected" : ""); ?> value="dropship"/>Dropship</option>
                                    <option <?php echo (strpos($product_ships_from,"missouri") > -1 ? "selected" : ""); ?> value="missouri"/>Missouri</option>
                                    <option <?php echo (strpos($product_ships_from,"clackamas") > -1 ? "selected" : ""); ?> value="clackamas"/>Clackamas</option>
                        </select></li>
                        <li>Customer Minimum Order Quantity:<input type="text" class="form-control" value="<?php echo $product_minimum_order_quantity; ?>" name="product_minimum_order_quantity"/></li>
                        <li>Starting Inventory Quantity:<input type="text" class="form-control" value="<?php echo $product_inventory_quantity; ?>" name="inventory_quantity"/></li>
                        <li>Minimum Stock Reorder Quantity:<input type="text" class="form-control" value="<?php echo $product_minimum_stock_reorder; ?>" name="minimum_stock_reorder"/></li>
                        <li>Inventory Threshold:<input type="text" class="form-control" value="<?php echo $product_inventory_threshold; ?>" name="product_inventory_threshold"/></li>
                        <li>Allow Backorder: <select class="form-control" name="product_inventory_backorder">
                            <option <?php echo (strpos($product_allow_backorder,"No") > -1 ? "selected" : ""); ?> name="No">No</option>
                            <option <?php echo (strpos($product_allow_backorder,"Yes") > -1 ? "selected" : ""); ?> name="Yes">Yes</option>
                                </select></li>
                        <li>Inventory Details:<br/><select class="form-control" name="product_inventory_details">
                            <option <?php echo (strpos($product_inventory_details,"autoreplenish") > -1 ? "selected" : ""); ?> value="autoreplenish"/>Autoreplenish</option>
                            <option <?php echo (strpos($product_inventory_details,"corporate_approval") > -1 ? "selected" : ""); ?> value="corporate_approval"/>Corporate Approval</option>
                            <option <?php echo (strpos($product_inventory_details,"sales_rep_approval") > -1 ? "selected" : ""); ?> value="sales_rep_approval"/>Sales Rep Approval</option>
                        </select></li>
                        <li>Inventory Notes:<input type="text" class="form-control" value="<?php echo $product_inventory_notes; ?>" name="product_inventory_notes"/></li>
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
                    <li>Corporate Approval Date: <?php echo $product_corp_approval_date; ?></li>
                        <li>Corporate Approval By: <input type="text" class="form-control" value="<?php echo $product_corporate_approval_by; ?>" name="product_corporate_approval_by"/></li>
                        <li>Marketing: Single item blast once live? <select class="form-control" name="product_single_product_blast">
                            <option <?php echo (strpos($product_marketing_blast,"No") > -1 ? "selected" : ""); ?> name="No" value="No">No</option>
                            <option <?php echo (strpos($product_marketing_blast,"Yes") > -1 ? "selected" : ""); ?> name="Yes" value="Yes">Yes</option>
                                </select></li>
                        <li>Sales Rep: <input type="text" class="form-control" value="<?php echo $product_sales_rep; ?>" name="product_sales_rep"/></li>
                        <li>Notes/Additional Comments: <input type="text" class="form-control" value="<?php echo $product_notes; ?>" name="product_notes"/></li>
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
                            <li>Vendor Name:<input type="text" class="form-control" value="<?php echo $product_vendor; ?>" name="vendor" required="required"/></li>
                            <li>Vendor Product Number:<input type="text" class="form-control" value=" <?php echo $product_vendor_product_number; ?>" name="vendor_product_number" required="required"/></li>
                            <li>Vendor Product Name:<input type="text" class="form-control" value="<?php echo $vendor_product_name; ?>" name="vendor_product_name"/></li>
                            <li>Setup Fees: <input type="text" class="form-control" value="<?php echo $setup_fees; ?>" name="setup_fees"/></li>
                            <li>Repeat Setups: <input type="text" class="form-control" value="<?php echo $repeat_setups; ?>" name="repeat_setups"/></li>
                            <li>Other Vendor Charges: <input type="text" class="form-control" value="<?php echo $other_vendor_charges; ?>" name="other_vendor_charges"/></li>
                            <li>Vendor Art Email: <input type="text" class="form-control" value="<?php echo $vendor_art_email; ?>" name="vendor_art_email"/></li>
                            <li>Vendor Phone Number: <input type="text" class="form-control" value="<?php echo $vendor_phone_number; ?>" name="vendor_phone_number"/></li>
                            <li>Vendor Email: <input type="text" class="form-control" value="<?php echo $vendor_email; ?>" name="vendor_email"/></li>
                        </ul>
                        </div>
                    </div>
                    </div>
                    </div>
                    
                </form>
                <hr>
                <h3>SKU Information</h3>
                <table class="table table-bordered">
                        <tr>
                            <th>Size</th>
                            <th>Color</th>
                            <th>SKU</th>
                        </tr>
                <?php
                    $sku_query = "SELECT * FROM `admin_product_skus` WHERE `product_id`=" . $product_id;
                    $sku_results = mysqli_query($conn, $sku_query);

                    if(mysqli_num_rows($sku_results) == 0){
                        echo "<tr colspan='3'><td>Skus not generated yet</td></tr>";
                    }
                    else{
                        while($data = mysqli_fetch_assoc($sku_results)){
                            echo "<tr><td>" . $data["size"] . "</td><td>" . $data["color"] . "</td><td>" . $data["sku"] . "</td></tr>";
                        }
                    }
                ?>
                </table>
                    </div>
                    <?php include_once("includes/inc-footer.php"); ?>
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $(".form-control").change(function(){
                                var username = "<?php echo $username; ?>";
                                var product_id = <?php echo $product_id; ?>;
                                var column_to_update = $(this).attr('name');
                                var data = encodeURIComponent($(this).val());
                                //exceptions
                                if(column_to_update.includes("size") || column_to_update.includes("discontinued")){
                                    if(!column_to_update.includes("upcharge"))
                                    {
                                        if($(this).prop("checked")){
                                        data = 1;
                                        }
                                        else{
                                            data = 0;
                                        }
                                    }
                                }
                                if(column_to_update.includes("blast") || column_to_update.includes("sizing")){
                                    if(data == "Yes"){
                                        data = 1;
                                    }
                                    else{
                                        data = 0;
                                    }
                                }
                                if(column_to_update.includes("inventory_backorder")){
                                    if(data == "Yes"){
                                        data = 1;
                                    }
                                    else{
                                        data = 0;
                                    }
                                }


                                var urlData = "update_product_details.php?"
                                    + "username=" + username
                                    + "&product_id=" + product_id
                                    + "&column_to_update=" + column_to_update
                                    + "&data=" + data;

                                console.log(urlData);

                                $.ajax({
                                    url: urlData,
                                    success: function (data) {
                                        // Notify User
                                        // Refresh Tracked Changes
                                        $('.toast-body').text(data);
                                        $('.toast').toast({ 
                                            animation: false, 
                                            delay: 3000 
                                        }); 
                                        $('.toast').toast('show');
                                    },
                                    error: function (xhr, status, error) {
                                        if (xhr.status > 0){
                                            alert(status); // status 0 - when load is interrupted
                                        } 
                                    }
                                });
                            }); /* END form-control*/
                            $(".decoration").change(function(){
                                var username = "<?php echo $username; ?>";
                                var product_id = <?php echo $product_id; ?>;
                                var column_to_update = $(this).attr('name');
                                var data = "";
                                
                                $(".decoration").each(function(){
                                    if($(this).prop("checked")){
                                        data += $(this).val() + " | ";
                                    }
                                })

                                var urlData = "update_product_details.php?"
                                    + "username=" + username
                                    + "&product_id=" + product_id
                                    + "&column_to_update=" + column_to_update
                                    + "&data=" + data;

                                console.log(urlData);

                                $.ajax({
                                    url: urlData,
                                    success: function (data) {
                                        // Notify User
                                        // Refresh Tracked Changes
                                        $('.toast-body').text(data);
                                        $('.toast').toast({ 
                                            animation: false, 
                                            delay: 3000 
                                        }); 
                                        $('.toast').toast('show');
                                    },
                                    error: function (xhr, status, error) {
                                        if (xhr.status > 0){
                                            alert(status); // status 0 - when load is interrupted
                                        } 
                                    }
                                });
                            });
                        });
                    </script>
                    <!-- CHANGE HISTORY FOR PRODUCT -->
                    <div class="dashboard-block client-block">
                    <hr>
                        <h3>Change History</h3>
                        <ul class="edit_details">
                            <?php
                                $change_query = "SELECT * FROM `admin_product_changes` WHERE product_id=" . $product_id;
                                $results = mysqli_query($conn, $change_query);
                                while($change = mysqli_fetch_assoc($results)){
                                    echo "<li><b>" . $change["username"] . "</b><br><i>" . $change["product_changes"] . "</i> - " . $change["date"] . "</li>";
                                }
                            ?>
                        </ul>
                    </div>
                    <!-- NOTES HISTORY FOR PRODUCT -->
                    <div class="notes-side-block">
                        <p class="x" onclick="$(this).parent().toggle(300)">X</p>
                            <h3>Product Notes</h3>
                            <ul class="edit-details product_notes_blocks">
                                <?php
                                    $notes_query = "SELECT * FROM `admin_product_notes` WHERE `_product_id`=" . $product_id;

                                    $notes = mysqli_query($conn, $notes_query);
                                    while($note = mysqli_fetch_assoc($notes)){
                                        echo "<li>" . $note['notes'] . "</li>";
                                    }
                                ?>
                                <li class="note_form"><textarea class="new_note" rows="3" placeholder="Enter your new note here" style="padding: 10px; width: 100%;"></textarea>
                                    <br><a id="submit_note" class="btn" style="cursor: pointer;">Submit Note</a></li>
                                    
                            </ul>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    $("#submit_note").click(function(){
                                        var note_text = $(".new_note").val();
                                        var product_id = <?php echo $product_id; ?>;
                                        var username = "<?php echo $username; ?>";
                                        $("<li>" + note_text + "</li>").insertBefore(".note_form");
                                        $(".new_note").val("");
                                        
                                        var urlData = "insert_note.php?product_id=" + product_id 
                                                    + "&notes=" + note_text.replace("#")
                                                    + "&username=" + username;
                                                    
                                        $.ajax({
                                            url: urlData,
                                            success: function (data) {
                                                // Notify User
                                                // Refresh Tracked Changes
                                                $('.toast-body').text(data);
                                                $('.toast').toast({ 
                                                    animation: false, 
                                                    delay: 3000 
                                                }); 
                                                $('.toast').toast('show');
                                            },
                                            error: function (xhr, status, error) {
                                                if (xhr.status > 0){
                                                    alert(status); // status 0 - when load is interrupted
                                                } 
                                            }
                                        });
                                    });
                                    $("#sentToWoo").click(function(){
                                        var consumerKey = "<?php echo $consumer_key; ?>";
                                        var consumerSecret = "<?php echo $consumer_secret; ?>";
                                        var urlData      = "includes/push_to_woo_simple.php?consumer_key=" + consumerKey + "&consumer_secret=" + consumerSecret + "&product_id=<?php echo $_GET['product_id']; ?>&client_id=<?php echo $client_id; ?>";
                                       
                                        window.location.href=urlData;
                                    });
                                });
                            </script>
                    </div> <!-- END side note block -->
                    <!-- Form for adding attachments -->
                    <div id="attachment_form">
                    <div class="X" onclick="$(this).parent().toggle(300);">X</div>
                        <h3>Upload an Image</h3>
                        <form action="add_attachments.php" method="post" enctype="multipart/form-data" id="image_uploading_form">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" id="product_id"/>
                            Select image to upload:
                            <input type="file" name="fileToUpload" id="fileToUpload">
                            <input type="submit" value="Upload Image" name="submit" class="btn btn-primary">
                        </form>
                    </div>
                    <!-- END for for adding attachments -->
        <!-- TOAST Section -->
        <div aria-live="polite" aria-atomic="true" class="toast-holder">
            <div class="toast" style="position: relative; top: 0; right: 0;">
                <div class="toast-header">
                <i class="fas fa-flag"></i>
                <strong class="mr-auto">Edit Product Update</strong>
                <small>Just Now</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="toast-body">
                
                </div>
            </div>
        </div>
        <!-- END Toast Section -->
                </body>
            </html>
            <?php
    }
            ?>