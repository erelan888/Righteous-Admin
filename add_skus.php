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
 global $product_id;
 $conn = new mysqli($servername, $db_username, $db_password,$db);
 
 // Check connection
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 } 


function get_product_details($product_id){
    global $conn;
    $product_query = "SELECT * FROM `admin_client_products` WHERE `_id`=" . $product_id;
    $product_query_results = mysqli_query($conn, $product_query);

    return mysqli_fetch_assoc($product_query_results);
}
function get_client_details($client_id){
    global $conn;
    $client_query  = "SELECT * FROM `admin_client_details` WHERE `_client_id`=" . $client_id;
    $client_query_results = mysqli_query($conn, $client_query);

    return mysqli_fetch_assoc($client_query_results);
}

if(isset($_GET["client_id"]) and isset($_GET["product_id"])){
    global $product_id;
    $product_id     = $_GET["product_id"];
    $client_id      = $_GET["client_id"];

    $client_details = get_client_details($client_id);
    $product        = get_product_details($product_id);

    $colors         = explode(",",$product["colors"]);
    /* client_code - product->style_number - product->category_number - <<product_number>> - product-size */
}
$message = null;
if(isset($_GET["message"])){
    global $product_id;
    $message = $_GET["message"];
    $product_id = $_GET["product_id"];
}
?>
<html>
<head>
<title>Add Product SKU's - RCHQ Admin Area</title>
<meta name="robots" content="noindex,nofollow"/>
<?php
    include_once("includes/inc-html-header.php");
?>
</head>
<body>
<?php include_once("includes/inc-header.php"); ?>
<div class="container skip-nav">
    <h1 style="padding-bottom: 20px;">Create Product SKU's</h1>
    <?php
        if($message != null){
            echo "<p class='alert alert-success'>Skus Genereated Successfully!</p>";
        }
    ?>
    <a href="https://admin.authenticmerch.com/edit_product.php?product_id=<?php echo $product_id; ?>" class="btn"> <i class="fas fa-undo"></i> Back To Product</a>
    <div class="dashboard-block client-block">
        <form name="edit_skus" method="POST" action="">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <input type="hidden" name="client_code" value="<?php echo $client_details["client_code"]; ?>" />
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
        <!-- hidden sizes so it goes through post -->
        <input type="hidden" name="size_osfm" value="<?php echo $product["size_osfm"]; ?>" />
        <input type="hidden" name="size_xs"   value="<?php echo $product["size_xs"]; ?>" />
        <input type="hidden" name="size_s"    value="<?php echo $product["size_s"]; ?>" />
        <input type="hidden" name="size_m"    value="<?php echo $product["size_m"]; ?>" />
        <input type="hidden" name="size_l"    value="<?php echo $product["size_l"]; ?>" />
        <input type="hidden" name="size_xl"   value="<?php echo $product["size_xl"]; ?>" />
        <input type="hidden" name="size_xxl"  value="<?php echo $product["size_xxl"]; ?>" />
        <input type="hidden" name="size_3xl"  value="<?php echo $product["size_3xl"]; ?>" />
        <input type="hidden" name="size_4xl"  value="<?php echo $product["size_4xl"]; ?>" />
        <input type="hidden" name="size_5xl"  value="<?php echo $product["size_5xl"]; ?>" />
        <input type="hidden" name="size_6xl"  value="<?php echo $product["size_6xl"]; ?>" />
        <input type="hidden" name="size_na"   value="<?php echo $product["size_na"]; ?>" />
        <input type="hidden" name="colors"     value="<?php echo $product["colors"]; ?>" />
        <hr/>
        <ul class="edit_details">
            <li>Internal Style Type (For the SKU): <select class="form-control" name="style_number">
                <option name="na">N/A</option>
                <option name="mensUnisex">Mens/Unisex</option>
                <option name="ladies">Ladies</option>
                <option name="youth">Youth</option></select></li>
            <li>Internal Category Type (For the SKU): <select class="form-control" name="category_number">
                <option name="misc">Misc</option>
                <option name="tees">Tees</option>
                <option name="sweats">Sweats</option>
                <option name="headwear">Headwear</option>
                <option name="outerwear">Outerwear</option>
                <option name="wovens">Wovens</option>
                <option name="knits">Knits</option>
                <option name="promoDrinkware">Promo Items/Drinkware</option>
                <option name="saucesRubs">Sauces/Rubs</option>
                <option name="giftCards">Gift Cards</option>
                </select></li>
        
        </ul>
        <h3 style="padding-bottom: 20px;">Assign product numbers: </h3>
        <ul class="edit_details">
            <?php
                if(!empty($colors)){
                    foreach($colors as $color){
                        echo "<li>" . $color . ": <input type='text' class='form-control'  required='required' maxlength='3' value='' name='product_number_" . $color . "' /></li>";
                    }
                }
                else{
            ?>
                    <li>Single Product:<input type="text" class="form-control" maxlength='3' value="" name="sku_single" required="required"/></li>
            <?php
                } 
            ?>
        </ul>
            <input type="submit" value="Generate" class="btn btn-primary"/>
        <hr/>
        </form>

        <?php
        function generate_sku($product_id, $color, $client_code,$style_number, $category_number, $product_number, $product_size){
            global $conn;

            if($product_size == "OSFM"){
                $sku =  $client_code . "-" . $style_number . $category_number . "-" . $product_number;
            }
            else{
                $sku =  $client_code . "-" . $style_number . $category_number . "-" . $product_number . "-" . $product_size;
            }
            
            $sku_insert_query = "INSERT INTO `admin_product_skus` (product_id,size,color,sku) VALUES ('" . $product_id . "','"
            . $product_size . "','" . $color . "','" . $sku . "');";
            //TODO: add check for existing sku
            $check_query = "SELECT * FROM `admin_product_skus` WHERE sku=" . $sku;
            $check_results = mysqli_query($conn,$check_query);
            if(mysqli_num_rows($check_results) < 1){
                if($conn->query($sku_insert_query) === TRUE){
                    return;
                }
                else{
                    die("SKU Insert failed " . mysqli_error($conn));
                }
            }
            else{
                return;
            }
            
        }
            if(isset($_POST['product_id'])){
                /* client_code - product->style_number - product->category_number - <<product_number>> - product-size */
                $client_id = $_POST['client_id'];
                $client_code = $_POST['client_code'];
                $style_number = $_POST['style_number'];
                $category_number = $_POST['category_number'];
                //product sizes
                $size_osfm  = $_POST['size_osfm'];
                $size_xs    = $_POST['size_xs'];
                $size_s     = $_POST['size_s'];
                $size_m     = $_POST['size_m'];
                $size_l     = $_POST['size_l'];
                $size_xl    = $_POST['size_xl'];
                $size_xxl   = $_POST['size_xxl'];
                $size_3xl   = $_POST['size_3xl'];
                $size_4xl   = $_POST['size_4xl'];
                $size_5xl   = $_POST['size_5xl'];
                $size_6xl   = $_POST['size_6xl'];
                $size_na    = $_POST['size_na'];
                if(!empty($_POST["sku_single"])){
                    $sku_single = $_POST["sku_single"];
                }
                

                $sizes_bool = array($size_osfm,$size_xs,$size_s,$size_m,$size_l,$size_xl,$size_xxl, $size_3xl, $size_4xl, $size_5xl,$size_6xl,$size_na);
                $sizes = array("OSFM","XS","S","M","L","XL","2XL","3XL","4XL","5XL","6XL");

                $style = array("N/A","Mens/Unisex","Ladies", "Youth");
                $category = array("Misc","Tees","Sweats","Headwear","Outerwear","Wovens","Knits","Promo Items/Drinkware","Sauces/Rubs","Gift Cards");

                $colors    = explode(",",$_POST["colors"]);

                foreach($colors as $color){
                    for($i = 0; $i < count($sizes_bool); $i++){
                        if($sizes_bool[$i] == 1){
                            $color_product_number = $_POST["product_number_" . str_replace(" ","_",$color)];
                            generate_sku($product_id, $color, $client_code,array_search($_POST['style_number'],$style), array_search($_POST["category_number"],$category),$color_product_number, $sizes[$i]);
                        }
                    }
                }
                
                header("Location: https://admin.authenticmerch.com/edit_product.php?product_id=" . $product_id);
            }
            ?>
    </div>
    <?php include_once("includes/inc-footer.php"); ?>
</body>
</html>