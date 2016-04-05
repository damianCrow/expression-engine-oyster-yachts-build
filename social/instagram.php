<?php

/*require "vendor/autoload.php";

use MetzWeb\Instagram\Instagram;

//$clientId = '5cfac9a19fd3453e8d7ebf8917a9f732';
//$clientSecret = 'bfcf8e75e10246469606d950087efd2b';

$clientId = '43fb221c69cd4dc4a60b327de4a84a7b';
$clientSecret = '6d9baf3a6cff4502888ec26a2fa23f8e';

$instagram = new Instagram(array(
    'apiKey'      => $clientId,
    'apiSecret'   => $clientSecret,
    'apiCallback' => null
));

var_dump($instagram->getUserMedia(11878454, 20));

/*echo '<pre>';
var_dump($statuses);*/

$url = "https://www.instagram.com/oysteryachts/media/";

$stream = file_get_contents($url);

$streamDecoded = json_decode($stream);

echo '<pre>';

var_dump($streamDecoded);

?>