<?php
    ob_start();
    session_start();

    define("API_TOKEN","057b7128760efe57302835217de648ddc62923d1");
    define("COMPANY_DOMAIN","righteous");
    define("COMPANIES_PER_PAGE",100);
    define("PHP_EOL_FIX", "\n<br>");
    
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
    function create_file($data){
        //TODO: create file and store it on server, so it can then be pushed to pipedrive. 

    }
    function push_to_pipedrive($filename, $pipedrive_client_id){
        $data = array(
            'file' => curl_file_create($filename),
            'org_id' => $pipedrive_client_id
        );
         
        $url = 'https://' . $company_domain . '.pipedrive.com/v1/files?api_token=' . $api_token;
         
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
         
        $output = curl_exec($ch);
        curl_close($ch);
         
        $result = json_decode($output, true);
         
        if (!empty($result['data']['id'])) {
            return TRUE;
        }
    }
    if(isset($_POST['estimated-volume'])){

        $client_id                    = $_POST['client_id'];
        $estimated_volume             = $_POST['estimated-volume'];
        $average_shipping_quantity    = $_POST['average-shipping-quantity'];
        $how_customer_pay             = $_POST['how-customer-pay'];
        $design_options               = $_POST['design-options'];
        $item_size                    = $_POST['item-size'];
        $number_of_skus               = $_POST['number-of-skus'];
        $purchase_quantity            = $_POST['purchase-quantity'];
        $sourced_item                 = $_POST['sourced-item'];
        $purchase_price               = $_POST['purchase-price'];
        $inbound_freight              = $_POST['inbound-freight'];
        $how_we_manufacture           = $_POST['how-we-manufacture'];
        $what_happens                 = $_POST['what-happens'];
        $print_screens_front          = $_POST['print-screens-front'];
        $print_screens_back           = $_POST['print-screens-back'];
        $print_screens_other          = (empty($_POST['print-screens-other'])?0:$_POST['print-screens-other']);
        $print_screen_washes          = (empty($_POST['print-screen-washes'])?0:$_POST['print-screen-washes']);
        $print_screen_tape_off        = (empty($_POST['print-screen-tape-off'])?0:$_POST['print-screen-tape-off']);
        $embroidery_size              = $_POST['embroidery-size'];
        $embroidery_stitch_count      = (empty($_POST['embroidery-stitch-count'])?0:$_POST['embroidery-stitch-count']);
        $heatpress                    = $_POST['heatpress'];
        $project_shipping             = (empty($_POST['project-shipping'])?0:$_POST['project-shipping']);
        $markup_level                 = $_POST['markup'];

        $insert_query = "INSERT INTO `admin_sales_widget` (client_id,user_id,estimated_volume,average_shipping_quantity,how_customer_pay,
        design_options,item_size,number_of_skus,purchase_quantity,sourced_item,purchase_price,inbound_freight,how_we_manufacture,what_happens,
        print_screens_front,print_screens_back,print_screens_other,print_screen_washes,print_screen_tape_off,embroidery_size,embroidery_stitch_count,
        heatpress,project_shipping,markup_level) VALUES ('"
        . $client_id                    . "','"
        . $user_id                      . "','"
        . $estimated_volume             . "','"
        . $average_shipping_quantity    . "','"
        . $how_customer_pay             . "','"
        . $design_options               . "','"
        . $item_size                    . "','"
        . $number_of_skus               . "','"
        . $purchase_quantity            . "','"
        . $sourced_item                 . "','"
        . $purchase_price               . "','"
        . $inbound_freight              . "','"
        . $how_we_manufacture           . "','"
        . $what_happens                 . "','"
        . $print_screens_front          . "','"
        . $print_screens_back           . "','"
        . $print_screens_other          . "','"
        . $print_screen_washes          . "','"
        . $print_screen_tape_off        . "','"
        . $embroidery_size              . "','"
        . $embroidery_stitch_count      . "','"
        . $heatpress                    . "','"
        . $project_shipping             . "','"
        . $markup_level                 . "');";

        $message = "";
        if($conn->query($insert_query) === TRUE){
            $message = "Pricing Calculation Saved!";
        }
        else{
            echo $insert_query . "<br>\n\r";
            echo mysqli_error($conn);
        }
    }
    ?>
    
<!doctype html>
<html lang='en'>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Sales Widget - RCHQ Admin Area</title>
        <meta name="robots" content="noindex, nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
        <?php 
            $title="Sales Widget";
            include_once("includes/inc-header.php"); 
        ?>
        <div class="container-fluid skip-nav">
        <h3>Sales Widget</h3>
        <?php
            if(!empty($message)){
                ?>
                <p class="alert alert-success"><?php echo $message; ?></p>
                <?php
            }
        ?>
            <div class="row">
            <div class="col">
            <form method="POST" action="">
                <input type="hidden" name="client_id" value="<?php echo $_GET['client_id']; ?>" />
                <table class="table">
                <tr>
                    <td>Estimated Annual Volume:<a href="#" data-toggle="tooltip" title="Enter the estimated annual volume or the size of the one time order" class="widget-tooltip"><i class="fas fa-question-circle"></i></a></td>
                    <td><input type="text" class="form-control-sm estimated-volume" name="estimated-volume"/></td>
                </tr>
                <tr>
                    <td>Average Shipping Quantity:<a href="#" data-toggle="tooltip" title="Required if the item is in inventory" class="widget-tooltip"><i class="fas fa-question-circle"></i></a></td>
                    <td><input type="text" class="form-control-sm average-shipping-quantity" name="average-shipping-quantity"/></td>
                </tr>
                <tr>
                    <td>How will the Customer Pay:<a href="#" data-toggle="tooltip" title="Charges vary based on chosen method" class="widget-tooltip"><i class="fas fa-question-circle"></i></a></td>
                    <td><select class="form-control-sm how-customer-pay" name="how-customer-pay">
                        <option value="credit-card">Credit Card</option>
                        <option value="purchase-order">Purchase Order</option>
                        <option value="will-call">Will Call Pre-pay</option></select></td>
                </tr>
                <tr>
                    <td>Design Options:</td>
                    <td><select class="form-control-sm design-options" name="design-options">
                        <option value="print-ready-from-client">Art is Print Ready from Client</option>
                        <option value="client-design-with-work">Client Design We Cleaned Up</option>
                        <option value="righteous-design">Righteous Design</option>
                        <option value="previous-art">Using Previous Art</option></select></td>
                </tr>
                <tr>
                    <td>What is the size of the item: <a href="#" data-toggle="tooltip" title="For calculating warehousing fees" class="widget-tooltip"><i class="fas fa-question-circle"></i></a></td>
                    <td><select class="form-control-sm item-size" name="item-size">
                        <option value="like-a-shirt">Like a Shirt</option>
                        <option value="much-smaller">Much Smaller</option>
                        <option value="much-larger">Much Larger</option></select></td>
                </tr>
                <tr>
                    <td><a href="#" data-toggle="tooltip" title="Required if the item is in inventory" class="widget-tooltip"><i class="fas fa-question-circle"></i></a>How many Unique SKU's (Size/Color):</td>
                    <td><input type="text" class="form-control-sm number-of-skus" name="number-of-skus"/></td>
                </tr>
                </table>
                    <h5>We Decide</h5>
                    <table class="table">
                    <tr>
                        <td>Purchase/Mfg Quantity:</td>
                        <td><input type="text" class="form-control-sm purchase-quantity" name="purchase-quantity"/></td>
                    </tr>
                    <tr>
                    <td>Sourced Item Number/Who:</td>
                    <td><input type="text" class="form-control-sm sourced-item" name="sourced-item"/></td>
                    </tr>
                    <tr>
                    <td>Purchase Price (Each item):</td>
                    <td><input type="text" class="form-control-sm purchase-price" name="purchase-price"/></td>
                    </tr>
                    <tr>
                    <td>Inbound Freight (per purchase quantity):</td>
                    <td><input type="text" class="form-control-sm inbound-freight" name="inbound-freight"/></td>
                    </tr>
                    <tr>
                    <td>How will we manufacture the item:</td>
                    <td><select class="form-control-sm how-we-manufacture" name="how-we-manufacture">
                        <option value="rca-portland">RCA Portland</option>
                        <option value="rca-missouri">RCA Missouri</option>
                        <option value="rca-contract">RCA Contract</option>
                        <option value="vendor-supplied-domestic">Vendor Supplied Domestic</option>
                        <option value="vendor-supplied-import">Vendor Supplied Import</option></select></td>
                    </tr>
                    <tr>
                    <td>What Happens to the Order:</td>
                    <td><select class="form-control-sm what-happens" name="what-happens">
                        <option value="INVENTORY_IN_MISSOURI">Inventory In Missouri</option>
                        <option value="INVENTORY_IN_PORTLAND">Inventory In Portland</option>
                        <option value="LTO_SPECIAL">LTO or One Time Special</option>
                        <option value="NON_STOCK_ONLINE">Non-stock Online</option>
                        <option value="PERIODIC_SCHEDULED_ONLINE">Periodic Scheduled Online</option>
                        <option value="RECEIVED_INTO_INVENTORY">Received into Inventory</option>
                        <option value="DIRECT_FROM_RCA">(Project)Direct to Consumer from RCA</option>
                        <option value="DIRECT_FROM_VENDOR">(Project)Direct Ship From Vendor</option>
                    </select></td>
                    </tr>
                    </table>
                    <h5>Deco Options</h5>
                    <table class="table">
                    <tr>
                    <td>Number of Print Screens (Front):</td>
                    <td><input type="number" class="form-control-sm print-screens-front" name="print-screens-front"/></td>
                    </tr>
                    <tr>
                    <td>Number of Print Screens (Back):</td>
                    <td><input type="number" class="form-control-sm print-screens-back" name="print-screens-back"/></td>
                    </tr>
                    <tr>
                    <td>Number of Print Screens (Other):</td>
                    <td><input type="number" class="form-control-sm print-screens-other" name="print-screens-other"/></td>
                    </tr>
                    <tr>
                    <td>Screen Washes:</td>
                    <td><input type="number" class="form-control-sm print-screen-washes" name="print-screen-washes"/></td>
                    </tr>
                    <tr>
                    <td>Screen Tape Off:</td>
                    <td><input type="number" class="form-control-sm print-screen-tape-off" name="print-screen-tape-off"/></td>
                    </tr>
                    <tr>
                    <td>Embroidery Size:</td>
                    <td><select class="form-control-sm embroidery-size" name="embroidery-size">
                        <option value="NO_EMBROIDERY">No Embroidery</option>
                        <option value="EMBROIDERY_5x5">5 x 5</option>
                        <option value="EMBROIDERY_6x8">6 x 8</option>
                        <option value="EMBROIDERY_OVER_6x8">Over 6 x 8</option>
                        <option value="EMBROIDERY_STRAIGHT_STOCK">Straight Stock</option>
                        </select></td>
                        </tr>
                    <tr>
                    <td>Embroidery Stitch Count:</td>
                    <td><input type="number" class="form-control-sm embroidery-stitch-count" name="embroidery-stitch-count" placeholder="Enter 0, or a number greater than 5000"/></td>
                    </tr>
                    <tr>
                    <td>Heatpress:</td>
                    <td><select class="form-control-sm heatpress" name="heatpress">
                        <option value="NO_HEATPRESS">None</option>
                        <option value="HEATPRESS_REGULAR">Regular</option>
                        <option value="HEATPRESS_TRICKY">Tricky</option>
                        <option value="HEATPRESS_EXTRA">Extra</option>
                    </select></td>
                    </tr>
                    <tr>
                    <td>Project Shipping:</td>
                    <td><input type="number" class="form-control-sm project-shipping" name="project-shipping"/></td>
                    </tr>
                    <tr>
                        <td>Mark Up Level:</td>
                        <td><select class="form-control-sm markup" name="markup">
                        <option value="AorP">A or P</option>
                        <option value="BorQ">B or Q</option>
                        <option value="CorR">C or R</option>
                        <option value="DorS">D or S</option>
                        <option value="EotT">E ot T</option>
                        <option value="ForU">F or U</option>
                        <option value="GorV">G or V</option>
                        <option value="HorW">H or W</option>
                        </select></td>
                    <tr>
                </table>
                <input class="btn btn-primary" type="submit" name="submit" value="Save Pricing Calculations"/>
                <br/>
            
            </div>
            <div class="col" style="border: 1px solid #e7e7e6;">
                <ul class="nav nav-tabs" style="position: relative; top: -42px;">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#home">Online Item</a></li>
                <li class="nav-item"><a class="nav-link"  data-toggle="tab" href="#menu1">Project</a></li>
                </ul>

                <div class="tab-content">
                <div id="home" class="tab-pane fade-in active">
                    <h5>All in Sales Price (Each):<span id="all-in-sales-price-online"></span></h5> 
                    <table class="table">
                        <tr>
                            <td>Sell Price for Item:</td>
                            <td><span id="sell-price-online"></span></td>
                        </tr>
                        <tr>
                            <td>Decoration Charges:</td>
                            <td><span id="decoration-charges-online"></span></td>
                        </tr>
                        <tr>
                            <td>Setup/Manufacturing Run Charges:</td>
                            <td><span id="setup-run-charges-online"></span></td>
                        </tr>
                        <tr>
                            <td>Design Services:</td>
                            <td><span id="design-services-online"></span></td>
                        </tr>
                        <tr>
                            <td>Annual Store Management:</td>
                            <td><span id="annual-store-charges-online"></span></td>
                        </tr>
                        <tr>
                            <td>Warehousing, Inventory & Order Handling:</td>
                            <td><span id="warehousing-charges-online"></span></td>
                        </tr>
                        <tr>
                            <td>Merchant Services & Transaction:</td>
                            <td><span id="merchant-services-online"></span></td>
                        </tr>
                        <tr>
                            <td>Internal Shipping:</td>
                            <td><span id="internal-shipping-online"></span></td>
                        </tr>
                        <tr>
                            <td>Sales Total:</td>
                            <td><span id="sell-price-total-online"></span></td>
                        </tr>
                    </table>
                    
                </div>
                <div id="menu1" class="tab-pane fade">
                    <h5>All in Sales Price (Each):<span class="all-in-sales-price-project"></span></h5> 
                    <table class="table">
                        <tr>
                            <td>Sell Price for Item:</td>
                            <td><span id="sell-price-project"></span></td>
                        </tr>
                        <tr>
                            <td>Decoration Charges:</td>
                            <td><span id="decoration-charges-project"></span></td>
                        </tr>
                        <tr>
                            <td>Setup/Manufacturing Run Charges:</td>
                            <td><span id="setup-run-charges-project"></span></td>
                        </tr>
                        <tr>
                            <td>Design Services:</td>
                            <td><span id="design-services-project"></span></td>
                        </tr>
                        <tr>
                            <td>Annual Store Management:</td>
                            <td><span id="annual-store-charges-project"></span></td>
                        </tr>
                        <tr>
                            <td>Warehousing, Inventory & Order Handling:</td>
                            <td><span id="warehousing-charges-project"></span></td>
                        </tr>
                        <tr>
                            <td>Merchant Services & Transaction:</td>
                            <td><span id="merchant-services-project"></span></td>
                        </tr>
                        <tr>
                            <td>Internal Shipping:</td>
                            <td><span id="internal-shipping-project"></span></td>
                        </tr>
                        <tr>
                            <td>Sales Total:</td>
                            <td><span id="sale-price-total-project"></span></td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <tr>
                            <td>Total Per Item Charge:</td>
                            <td><span id="total-per-item-charge-project"></span></td>
                        </tr>
                        <tr>
                            <td>Total Project Charges</td>
                            <td><span id="total-project-charges"></span></td>
                        </tr>
                        <tr>
                            <td>Plus Shipping:</td>
                            <td><span id="shipping-project"></span></td>
                        </tr>
                    </table>
                </div>
                </div>

            </div>
        </div>
        <script>
$(document).ready(function(){
    
  $('[data-toggle="tooltip"]').tooltip();

  const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
        });

  $(".form-control-sm").on("change",function(){
    
    var markUpLevels = {
        "AorP":2,
        "BorQ":1.818,
        "CorR":1.667,
        "DorS":1.538,
        "EorT":1.429,
        "ForU":1.333,
        "GorV":1.25,
        "HorW":1.176
    }
    var manufacturerMarkupLevels = {
        "rca-portland":1.01,
        "rca-missouri":1.01,
        "rca-contract":1.02,
        "vendor-supplied-domestic":1.00,
        "vendor-supplied-import":1.04
    }
    var designCharges = {
        "print-ready-from-client":4.00,
        "client-design-with-work":15.00,
        "righteous-design":45.00,
        "previous-art":0.00
    }
    var heatpressLevels = {
        "NO_HEATPRESS": 0.00,
        "HEATPRESS_REGULAR": 25.00,
        "HEATPRESS_TRICKY": 35.00,
        "HEATPRESS_EXTRA": 50.00
    }
    var embroideryLevels = {
        "NO_EMBROIDERY": 0.00,
        "EMBROIDERY_5x5":44.00,
        "EMBROIDERY_6x8":72.00,
        "EMBROIDERY_OVER_6x8":136.00,
        "EMBROIDERY_STRAIGHT_STOCK":28.00
    }
    var orderPurposeMarkup = {
        "INVENTORY_IN_MISSOURI": .25,
        "INVENTORY_IN_PORTLAND": .23,
        "LTO_SPECIAL": .04,
        "NON_STOCK_ONLINE": .05,
        "PERIODIC_SCHEDULED_ONLINE": .08,
        "RECEIVED_INTO_INVENTORY": .20,
        "DIRECT_FROM_RCA":0.02,
        "DIRECT_FROM_VENDOR":0.01
    }
    var paymentMethodMarkups = {
        "credit-card": 0.05,
        "purchase-order": 0.005,
        "will-call": 0.01
    }
    var stockSizeType = {
        "like-a-shirt":1,
        "much-smaller":0.8,
        "much-larger":1.5
    }

    let estimatedAnnual         = $(".estimated-volume").val();
    let averageShippingQuantity = $(".average-shipping-quantity").val();
    let howCustomerWillPay      = $(".how-customer-pay").val();
    let designOptions           = $(".design-options").val();
    let itemSize                = $(".item-size").val();
    let numberOfSkus            = $(".number-of-skus").val();
    let purchaseQuantity        = $(".purchase-quantity").val();
    let purchasePrice           = $(".purchase-price").val();
    let inboundFreight          = $(".inbound-freight").val();
    let howManufactured         = manufacturerMarkupLevels[$(".how-we-manufacture").val()];
    let numberOfScreensFront    = $(".print-screens-front").val();
    let numberOfScreensBack     = $(".print-screens-back").val();
    let numberOfScreensOther    = $(".print-screens-other").val();
    let embroiderySize          = $(".embroidery-size").val();
    let embroideryStitchCount   = $(".embroidery-stitch-count").val();
    let heatpress               = $(".heatpress").val();
    let stockType               = $(".stock-type").val();
    let markUp                  = markUpLevels[$(".markup").val()];

    var perScreenCharge = 45.00;
    var perScreenResetCharge = 15.00;
    var screenWashCharge = 10.00;
    var screenTapeOffCharge = 12.00;
    var embroiderySetup = embroideryLevels[$(".embroidery-size").val()] * markUp;
    var heatpressSetup = heatpressLevels[$(".heatpress").val()] * markUp;

    function calculateSalePriceForItem(){
      if(estimatedAnnual != null && purchasePrice != null && howManufactured != null && markUp != null){
            return estimatedAnnual * purchasePrice * howManufactured * markUp;
      }
    }
    function calculateDecoCharges(){
        var heatpressRunCharges   = calculateHeatpressCharges(purchaseQuantity);
        var embroideryRunCharges  = calculateEmbroideryRunCharges(purchaseQuantity);
        var screenprintRunCharges = calculateScreenCharges();
        console.log("Heatpress Run Charges: " + heatpressRunCharges);
        console.log("Embroidery Run Charges: " + embroideryRunCharges);
        console.log("Print Run Charges: " + screenprintRunCharges);
        //Deco run charges total * 1+
        return (((heatpressRunCharges + embroideryRunCharges + screenprintRunCharges) * estimatedAnnual) * manufacturerMarkupLevels[$(".how-we-manufacture").val()]);
    }
    function calculateDesignServices(){
        if(numberOfSkus != null){
            return numberOfSkus * designCharges[designOptions];
        }
    }
    function checkValue(value){
        if(value == ""){
            return parseInt(0);
        }
        else{
            return parseInt(value);
        }
    }
    function getScreenWashes(){
        return checkValue($(".print-screen-washes").val()) * screenWashCharge;
    }
    function getScreenTapeOffs(){
        return checkValue($(".print-screen-tape-off").val()) * screenTapeOffCharge;
    }
    function calculatePrintRunCharges(quantity, numScreens){
        var quantityOne = 10.00;
        var quantityTwelve = [1.75, 2.45, 10.00, 10.00, 10.00, 10.00, 10.00, 10.00];
        var quantityTwentyFour = [1.45, 2.03, 2.38, 2.75, 10.00, 10.00, 10.00];
        var quantityFourtyEight = [1.25, 1.68, 2.08, 2.48, 2.88, 10.00, 10.00];
        var quantitySeventyTwo = [1.00, 1.30, 1.55, 1.80, 2.05, 2.30, 2.58];
        var quantityOneFourFour = [0.80, 0.95, 1.08, 1.30, 1.33, 1.45, 1.58];
        var quantityTwoEightyEight = [0.53, 0.63, 0.70, 0.75, 0.83, 0.93, 1.02];
        var quantityFiveHundred = [0.46, 0.55, 0.61, 0.66, 0.72, 0.78, 0.84];
        var quantityThousand = [0.39, 0.46, 0.51, 0.56, 0.61, 0.66, 0.71];

        if(numScreens > 0){
            if(quantity >= 1 && quantity < 12){
                return quantityOne;
            }
            else if(quantity >= 12 && quantity < 24){
                return quantityTwelve[numScreens];
            }
            else if(quantity >= 24 && quantity < 48){
                return quantityTwentyFour[numScreens];
            }
            else if(quantity >= 48 && quantity < 72){
                return quantityFourtyEight[numScreens];
            }
            else if(quantity >= 72 && quantity < 144){
                return quantitySeventyTwo[numScreens];
            }
            else if(quantity >= 144 && quantity < 288){
                return quantityOneFourFour[numScreens];
            }
            else if(quantity >= 288 && quantity < 500){
                return quantityTwoEightyEight[numScreens];
            }
            else if(quantity >= 500 && quantity < 1000){
                return quantityFiveHundred[numScreens];
            }
            else if(quantity >= 1000){
                return quantityThousand[numScreens];
            }
        }
        else{
            return 0;
        }

    }
    function calculateInternalShipping(){
        let purchaseQuantity = checkValue($(".purchase-quantity").val());
        let estimatedVolume = checkValue($(".estimated-volume").val());
        var total = 0;
        //TODO: if what happens to order is "inventory in kansas"
        if($(".what-happens").val() == "INVENTORY_IN_MISSOURI"){
            if(purchaseQuantity > 0 && estimatedVolume > 0){
                let inventoryTurns = estimatedVolume / purchaseQuantity;
                total = (inventoryTurns * purchaseQuantity) *.35;
            }
            return (checkValue($(".inbound-freight").val()) * 1.15) + (total);
        }
        else{
            return (checkValue($(".inbound-freight").val()) * 1.15);
        }
        
    }
    function calculateEmbroideryRunCharges(quantity){
        var embroideryCharges = 0;
        var embroideryRunCharges = [5.40, 4.17, 3.48, 2.79, 2.10, 1.95, 1.80, 1.65];
        if(embroiderySize != "NO_EMBROIDERY"){
            if(quantity > 1 && quantity < 6){
                embroideryCharges += embroideryRunCharges[0];
            }
            else if(quantity >= 6 && quantity < 12){
                embroideryCharges += embroideryRunCharges[1];
            }
            else if(quantity >= 12 && quantity < 24){
                embroideryCharges += embroideryRunCharges[2];
            }
            else if(quantity >= 24 && quantity < 48){
                embroideryCharges += embroideryRunCharges[3];
            }
            else if(quantity >= 48 && quantity < 72){
                embroideryCharges += embroideryRunCharges[4];
            }
            else if(quantity >= 72 && quantity < 288){
                embroideryCharges += embroideryRunCharges[5];
            }
            else if(quantity >= 288 && quantity < 576){
                embroideryCharges += embroideryRunCharges[6];
            }
            else{
                embroideryCharges += embroideryRunCharges[7];
            }
        }
        else{
            return 0;
        }
        
        //stitch count
        if(embroideryStitchCount > 5000){
            let tempCount = Math.ceil((embroideryStitchCount - 5000)/1000)
            if(tempCount > 0){
                if(quantity >= 288 && quantity < 576){
                    embroideryCharges += (tempCount * 0.27);
                }
                else if(quantity >= 576){
                    embroideryCharges += (tempCount * 0.24);
                }
                else{
                    embroideryCharges += (tempCount * 0.30);
                }
            }
        }
        return embroideryCharges * markUp;
    }
    function calculateHeatpressCharges(quantity){
        var heatpressRunCharges = 0;
        if(heatpress !="NO_HEATPRESS"){
            if(quantity >= 1 && quantity < 6){
                heatpressRunCharges += 6.01;
            }
            else if(quantity >= 6 && quantity < 12){
                heatpressRunCharges += 2.71;
            }
            else if(quantity >= 12 && quantity < 24){
                heatpressRunCharges += 2.45;
            }
            else if(quantity >= 24 && quantity < 48){
                heatpressRunCharges += 2.14;
            }
            else if(quantity >= 48 && quantity < 72){
                heatpressRunCharges += 1.91;
            }
            else if(quantity >= 72 && quantity < 108){
                heatpressRunCharges += 1.29;
            }
            else{
                heatpressRunCharges += 1.29;
            }

            return heatpressRunCharges * markUp;
        }
        else{
            return 0;
        }
        

    }
    function calculateScreenCharges(){
        let screensFront = checkValue($(".print-screens-front").val());
        let screensBack  = checkValue($(".print-screens-back").val());
        let screensOther = checkValue($(".print-screens-other").val());

        let screenFrontCharges = calculatePrintRunCharges(purchaseQuantity,screensFront-1);
        let screenBackCharges  = calculatePrintRunCharges(purchaseQuantity,screensBack-1);
        let screenOtherCharges = calculatePrintRunCharges(purchaseQuantity,screensOther-1);

        console.log("Front Screen Run Charges: " + screenFrontCharges);
        console.log("Back Screen Run Charges: " + screenBackCharges);
        console.log("Other Screen Run Charges: " + screenOtherCharges);

        let totalScreenRunCharges = (screenFrontCharges + screenBackCharges + screenOtherCharges) * markUp;
        console.log("Total Screen Run Charges: " + totalScreenRunCharges);
        console.log("Total Screen WASH Charges: " + getScreenWashes());
        console.log("Total Screen TAPE OFF Charges: " + getScreenTapeOffs());
        console.log("Total Screen Charges (IF RESET): " + ((screensFront + screensBack + screensOther) * perScreenResetCharge));
        console.log("Total Screen Charges (IF NEW): " + ((screensFront + screensBack + screensOther) * perScreenCharge));

        if(calculateDesignServices() == 0){
            let screenCharges = (((screensFront + screensBack + screensOther) * perScreenResetCharge) + getScreenWashes() + getScreenTapeOffs()) * markUp;
            return (totalScreenRunCharges); //taking off screen charges since its accounted for in the manufacturing run charges
        }
        else{
            let screenCharges = (((screensFront + screensBack + screensOther) * perScreenCharge) + getScreenWashes() + getScreenTapeOffs()) * markUp;
            return (totalScreenRunCharges);//taking off screen charges since its accounted for in the manufacturing run charges
        }
    }
    function calculateMerchantServices(){
        let designServices   = calculateDecoCharges();
        let salePriceForItem = calculateSalePriceForItem();

        if(designServices > 0 || salePriceForItem > 0){
            console.log("TOTAL for Merchant: " +(designServices + salePriceForItem) );
            console.log("TOTAL merchant charge: " + (designServices + salePriceForItem) * paymentMethodMarkups[$(".how-customer-pay").val()]);
            return (designServices + salePriceForItem) * paymentMethodMarkups[$(".how-customer-pay").val()];
        }
        else{
            return 0.00;
        }
    }
    function calculateStoreCharges(){
        let perSKU                  = 1.35*12;
        let handling                = 0.80;
        let numberOfSkus            = $(".number-of-skus").val();

        if(estimatedAnnual != "" && averageShippingQuantity != ""){
            estimatedAnnual = checkValue(estimatedAnnual);
            averageShippingQuantity = checkValue(averageShippingQuantity);
            let numAnnualShipping = estimatedAnnual/averageShippingQuantity;
           
            return ((perSKU * numberOfSkus) + (numAnnualShipping * 0.80));
        }
        return 0.00;
    }
    function calculateWarehouseCharges(){
        let salePriceCalc = calculateSalePriceForItem();
        let decoChargesCalc = calculateDecoCharges();
        
        if(estimatedAnnual != "" && purchaseQuantity != ""){
            let estimatedAnnualTurns = estimatedAnnual/purchaseQuantity;
            return ((salePriceCalc + decoChargesCalc) * (orderPurposeMarkup[$('.what-happens').val()]/estimatedAnnualTurns) * (stockSizeType[itemSize]));
        }
    }
    function calculateProjectWarehouseCharges(){
        let salePriceCalc = calculateSalePriceForItem();
        let decoChargesCalc = calculateDecoCharges();
        
        if(estimatedAnnual != ""){
            /* (sale price + deco charges) * 0.02 if shipping from here or 0.01 if shipping from vendor */
            return ((salePriceCalc + decoChargesCalc) * (orderPurposeMarkup[$('.what-happens').val()]) );
        }
    }
    function calculateRunCharges(){
        let screensFront = checkValue($(".print-screens-front").val());
        let screensBack  = checkValue($(".print-screens-back").val());
        let screensOther = checkValue($(".print-screens-other").val());

        if(calculateDesignServices() == 0 && screensFront != 0 && screensBack != 0){
            let screenCharges = (screensFront + screensBack + screensOther) * perScreenResetCharge;
            let estimatedAnnualTurns = estimatedAnnual/purchaseQuantity;

            return (screenCharges + embroiderySetup + heatpressSetup + (screenCharges * estimatedAnnualTurns) );
        }
        else if(screensFront != 0 && screensBack != 0){
            let screenCharges = (screensFront + screensBack + screensOther) * perScreenCharge;
            let estimatedAnnualTurns = estimatedAnnual/purchaseQuantity;

            return (screenCharges + embroiderySetup + heatpressSetup + (screenCharges * estimatedAnnualTurns) );
        }
        else{
            let estimatedAnnualTurns = estimatedAnnual/purchaseQuantity;
            return  embroiderySetup + heatpressSetup;
        }
    }
    function calculateProjectRunCharges(){
        let screensFront = checkValue($(".print-screens-front").val());
        let screensBack  = checkValue($(".print-screens-back").val());
        let screensOther = checkValue($(".print-screens-other").val());
        let screenCharges = (screensFront + screensBack + screensOther) * perScreenResetCharge;

        if(calculateDesignServices() == 0 && screensFront != 0 && screensBack != 0){
            return (screenCharges + embroiderySetup + heatpressSetup);
        }
        else if(screensFront != 0 && screensBack != 0){
            return (screenCharges + embroiderySetup + heatpressSetup);
        }
        else{
            return  embroiderySetup + heatpressSetup;
        }
    }
    function sumAllCharges(){
        return calculateSalePriceForItem() + calculateDesignServices() 
        + calculateDecoCharges() + calculateInternalShipping() + calculateMerchantServices()
        + calculateStoreCharges() + calculateWarehouseCharges() + calculateRunCharges();
    }
    function sumAllProjectCharges(){
        return calculateSalePriceForItem() + calculateDesignServices() 
        + calculateDecoCharges() + calculateMerchantServices()
        + calculateProjectWarehouseCharges() + calculateProjectRunCharges() + calculateInternalShipping();
    }
    function getProjectShipping(){
        return (checkValue($(".project-shipping").val()));
    }
    //online
    $("#sell-price-project").text(formatter.format(calculateSalePriceForItem()));
    $("#design-services-online").text(formatter.format(calculateDesignServices()));
    $("#decoration-charges-online").text(formatter.format(calculateDecoCharges()));
    $("#internal-shipping-online").text(formatter.format(calculateInternalShipping()));
    $("#annual-store-charges-online").text(formatter.format(calculateStoreCharges()));
    $("#warehousing-charges-online").text(formatter.format(calculateWarehouseCharges()));
    $("#setup-run-charges-online").text(formatter.format(calculateRunCharges()));
    $("#sell-price-total-online").text(formatter.format(sumAllCharges()));
    $("#merchant-services-online").text(formatter.format(calculateMerchantServices()));
    $("#all-in-sales-price-online").text(formatter.format(sumAllCharges()/estimatedAnnual));
    
    //project
    $("#sell-price-online").text(formatter.format(calculateSalePriceForItem()));
    $("#design-services-project").text(formatter.format(calculateDesignServices())); //same as online
    $("#decoration-charges-project").text(formatter.format(calculateDecoCharges()));
    $("#internal-shipping-project").text(formatter.format(calculateInternalShipping())); //no internal shipping charges
    $("#merchant-services-project").text(formatter.format(calculateMerchantServices()));
    $("#annual-store-charges-project").text(formatter.format(0.00)); //No Online Store Charges
    $("#warehousing-charges-project").text(formatter.format(calculateProjectWarehouseCharges()));
    $("#setup-run-charges-project").text(formatter.format(calculateProjectRunCharges()));
    $("#sale-price-total-project").text(formatter.format(sumAllProjectCharges()));
    $(".all-in-sales-price-project").text(formatter.format(sumAllProjectCharges()/estimatedAnnual));

    $("#total-per-item-charge-project").text(formatter.format(calculateSalePriceForItem()/estimatedAnnual));
    $("#total-project-charges").text(formatter.format(calculateProjectRunCharges() + calculateDesignServices() + (0.00 +
                calculateProjectWarehouseCharges() + 0.00)));
    $("#shipping-project").text(formatter.format(getProjectShipping()));

  });
});
</script>
</form>
        <?php include_once("includes/inc-footer.php"); ?>
    </body>
</html>