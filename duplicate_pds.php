<?php
    ob_start();
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    $permission_level = $_SESSION["permission_level"];
    if(!$user_id){
        header("Location: https://admin.authenticmerch.com");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_GET['product_id'])){
        $product_id = $_GET['product_id'];
        $product_query = "SELECT * FROM `admin_client_products` WHERE `_id`=" . $product_id;
        $result = mysqli_query($conn, $product_query);
        $product_details = mysqli_fetch_assoc($result);

        $insert_query = "INSERT INTO `admin_client_products` (client_id, product_name, site_type, product_description, product_categories, vendor"
            . ",vendor_product_number, product_sizing_chart, size_osfm, size_xs, size_s, size_m, size_l, size_xl, size_xxl, size_3xl, size_4xl, size_5xl, size_6xl, size_na,"
            . " colors, pricing_cost_net, pricing_corporate, pricing_retail, pricing_size_upcharge, product_length, product_height, product_width, product_weight, "
            . " product_decoration, product_decoration_details, product_decoration_number, product_decoration_filename, product_stock_ownership, product_ships_from,"
            . " product_minimum_order_quantity, product_inventory_threshold, product_inventory_backorder, product_inventory_details, product_inventory_notes, product_single_product_blast,"
            . " product_corporate_approval, product_sales_rep, product_notes, thread_colors, setup_fees, repeat_setups, other_vendor_charges, vendor_art_email, vendor_phone_number, product_corporate_approval_by,"
            . " minimum_stock_reorder, deco_instructions, vendor_email) " 
            . " VALUES ('" . $product_details['client_id'] . "','" 
            . "A_DUPLICATED " . $product_details['product_name'] . "','" 
            . $product_details['site_type'] . "','" 
            . $product_details['product_description'] . "','" 
            . $product_details['product_categories'] . "','"
            . $product_details['vendor']  . "','" 
            . $product_details['vendor_product_number'] . "','" 
            . $product_details['product_sizing_chart'] . "','" 
            . $product_details['size_osfm'] . "','" 
            . $product_details['size_xs'] . "','"
            . $product_details['size_s'] . "','" 
            . $product_details['size_m'] . "','" 
            . $product_details['size_l'] . "','" 
            . $product_details['size_xl'] . "','" 
            . $product_details['size_xxl'] . "','"
            . $product_details['size_3xl'] . "','" 
            . $product_details['size_4xl'] . "','" 
            . $product_details['size_5xl'] . "','" 
            . $product_details['size_6xl'] . "','" 
            . $product_details['size_na'] . "','" 
            . $product_details['colors'] . "','" 
            . $product_details['pricing_cost_net'] . "','" 
            . $product_details['pricing_corporate'] . "','" 
            . $product_details['pricing_retail'] . "','" 
            . $product_details['pricing_size_upcharge'] . "','" 
            . $product_details['product_length'] . "','" 
            . $product_details['product_height'] . "','" 
            . $product_details['product_width'] . "','" 
            . $product_details['product_weight']  . "','" 
            . $product_details['product_decoration'] . "','" 
            . $product_details['product_decoration_details'] . "','" 
            . $product_details['product_decoration_number'] . "','" 
            . $product_details['product_decoration_filename'] . "','" 
            . $product_details['product_stock_ownership'] . "','" 
            . $product_details['product_ships_from'] . "','" 
            . $product_details['product_minimum_order_quantity'] . "','" 
            . $product_details['product_inventory_threshold']  . "','" 
            . $product_details['product_inventory_backorder'] . "','" 
            . $product_details['product_inventory_details'] . "','" 
            . $product_details['product_inventory_notes'] . "','" 
            . $product_details['product_single_product_blast'] . "','" 
            . $product_details['product_corporate_approval'] . "','" 
            . $product_details['product_sales_rep'] . "','" 
            . $product_details['product_notes'] . "','"
            . $product_details['thread_colors'] . "','"
            . $product_details['setup_fees'] . "','"
            . $product_details['repeat_setups'] . "','"
            . $product_details['other_vendor_charges'] . "','"
            . $product_details['vendor_art_email'] . "','"
            . $product_details['vendor_phone_number'] . "','"
            . $product_details['product_corporate_approval_by'] . "','"
            . $product_details['minimum_stock_reorder'] . "','"
            . $product_details['deco_instructions'] . "','"
            . $product_details['vendor_email'] . "');";

            if($conn->query($insert_query) === TRUE){
                header("Location: https://admin.authenticmerch.com/edit_product.php?product_id=" . mysqli_insert_id($conn));
            }
            else{
                echo "Error: " . mysqli_error($conn);
            }
    }
    else{
        die("No product specified -> <a href='dashboard.php'>Dashboard</a>");
    }
?>