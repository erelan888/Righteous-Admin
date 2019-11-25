<?php
$ch = curl_init();
# Use your ordoro username/password here
$username = 'dustin@rchq.com';
$password = 'Pangia88!';
$url = 'https://api.ordoro.com/order/';
# All params are optional for fetching an orders list
# For more information on which params are valid, check the docs http://docs.ordoro.apiary.io/#reference/order/order/get
$params = array('status' => 'new', 'start_order_date' => '2019-10-08');
$url .= '?' . http_build_query($params);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, base64_encode($username . ':' . $password));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
curl_close($ch);
print $response;