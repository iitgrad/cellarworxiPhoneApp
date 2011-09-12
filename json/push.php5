<?php 
// test_push.php 
// Requires libcurl support. 
define('APPKEY','TMXhdpjcSlmKCZO3zkyrpg'); 
define('PUSHSECRET', 'dUCudu5nQAO2svq2Awmqng'); 
define('PUSHURL', 'https://go.urbanairship.com/api/push/'); 
// The device aliases you want to send to 
$aliases =  array('steven'); 
//device tokens 
$devices = array('8EF9E24E34DE5659BDDAC35CFA09DB8E8A04E0DF6C0F94AC953BB58EA4D4666D'); 
$contents = array(); 
$contents['badge'] = 42; 
$contents['alert'] = "Hello, from Urban Airship"; 
//$contents['sound'] = "cow"; 
$push = array("aps" => $contents); 
// if ($aliases) 
//    $push["aliases"] = $aliases; 
if ($devices) 
   $push["device_tokens"] = $devices; 
$json = json_encode($push); 
$session = curl_init(PUSHURL); 
curl_setopt($session, CURLOPT_USERPWD, APPKEY . ':' . PUSHSECRET); 
curl_setopt($session, CURLOPT_POST, True); 
curl_setopt($session, CURLOPT_POSTFIELDS, $json); 
curl_setopt($session, CURLOPT_HEADER, False); 
curl_setopt($session, CURLOPT_RETURNTRANSFER, True); 
curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_exec($session); 
// Check if any error occured 
$response = curl_getinfo($session); 
if($response['http_code'] != 200) { 
   echo "Got negative response from server, http code: ". 
$response['http_code'] . "\n"; 
} else { 

   echo "Yah, it worked!\n"; 
} 

curl_close($session); 
?>