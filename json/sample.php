<?php

// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);

require_once 'urbanairship.php';

// Your testing data
$APP_MASTER_SECRET = 'RePn8gAMR52zp5HUChw7Qg';
$APP_KEY = 'HVHWF4NpRHGvMHrsyEkWuA';
echo '<pre>';
//$TEST_DEVICE_TOKEN = 'c2e26852139a7c28e7542d672333f1f7276c26a62dc882f40c5bdb425a02ac4c';
$TEST_DEVICE_TOKEN = 'beb5d5d8eb5a5869c735a8f33f48ad9755a0bc9e1b1b32c28c76457d91f29166';

// Create Airship object
$airship = new Airship($APP_KEY, $APP_MASTER_SECRET);

// Test feedback

// $time = new DateTime('now', new DateTimeZone('UTC'));
// $time->modify('-1 day');
// echo $time->format('c') . '\n';
// print_r($airship->feedback($time));

// Test register

//$airship->register($TEST_DEVICE_TOKEN, 'testTag');

// Test get device token info
//print_r($airship->get_device_token_info($TEST_DEVICE_TOKEN));

// Test get device tokens

//$tokens = $airship->get_device_tokens();
//echo 'Device tokens count is:' . count($tokens);
//foreach ($tokens as $item) {
    //var_dump($item);
//}

// Test deregister

//$airship->deregister($TEST_DEVICE_TOKEN);


// Test push

$message = array('aps'=>array('alert'=>'hello'));
$airship->push($message, null, array('testTag'));

// Test broadcast

//$broadcast_message = array('aps'=>array('alert'=>'hello to all'));
//$airship->broadcast($broadcast_message, array($TEST_DEVICE_TOKEN));

?>
