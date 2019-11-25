<?php
define("API_TOKEN","057b7128760efe57302835217de648ddc62923d1");
define("COMPANY_DOMAIN","righteous");
define("COMPANIES_PER_PAGE",100);
define("PHP_EOL_FIX", "\n<br>");

$servername = "localhost";
$db_username = "fe32045_dev_dustin";
$db_password = "@TbGG3Fdau1m";
$db = "fe32045_admin_catalog";

global $conn;

$conn = new mysqli($servername, $db_username, $db_password,$db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function getOrganizations($limit = COMPANIES_PER_PAGE, $start = 0) {
    $url = 'https://' . COMPANY_DOMAIN . '.pipedrive.com/v1/organizations?api_token='
        . API_TOKEN . '&start=' . $start . '&limit=' . $limit;
 
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
    curl_close($ch);
      
    $result = json_decode($output, true);
    $orgs = [];
 
    if (!empty($result['data'])) {
        foreach ($result['data'] as $org) {
            $orgs[] = $org;
        }
    }
 
    if (!empty($result['additional_data']['pagination']['more_items_in_collection'] 
        && $result['additional_data']['pagination']['more_items_in_collection'] === true)) {
        usleep(500);
        $orgs = array_merge($orgs, getOrganizations($limit, $result['additional_data']['pagination']['next_start']));
    }
    
    return $orgs;
}
function checkCompany($company_id){
    global $conn;

    $check_query = "SELECT * FROM `admin_sales_organizations` WHERE `reference_id`=" . $company_id;
    $results = mysqli_query($conn, $check_query);

    $count = mysqli_num_rows($results);

    return ($count > 0? TRUE:FALSE);
}
 
$companies = getOrganizations(500,0);

if(!empty($companies)){
    foreach($companies as $company){
        if($company['label'] != null){
            //check if company exists in table
            if(checkCompany($company["id"]) === FALSE){
                $insert_query = "INSERT INTO `admin_sales_organizations` (company_name, label, reference_id) VALUES ('" 
                                . mysqli_real_escape_string($company["name"]) . "','" . $company["label"] . "','" . $company["id"] ."');";
                if($conn->query($insert_query) === TRUE){
                    echo "Organization Uploaded..." . PHP_EOL_FIX;
                }
                else{
                    echo "Upload failed..." . PHP_EOL_FIX;
                    echo mysqli_error($conn) . PHP_EOL_FIX;
                }
            }
        }
    } //END FOR LOOP
} // END IF
?>