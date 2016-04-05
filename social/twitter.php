<?php

require "../vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumerKey = 'YNjvlEk1W699d7ZizPqmgE4D1';
$consumerSecret = '7Z4PmOlvuS3dl1Gxzo8H7Dix2AIZE6NYk4y56N7cSAQrgoZ9yA';
$accessToken = '122032530-46fb61LeusIszAyXoZtYmUD08aDI0BxOVz3kkIv2';
$accessTokenSecret = 'ONXKkCIIOCHSjTz3zoCc4sGtqOA9QnkqToAOJZULvRNLZ';

$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
//$content = $connection->get("account/verify_credentials");

$statuses = $connection->get("statuses/user_timeline", ["screen_name" => "OysterMarine", "count" => 20, "exclude_replies" => true]);

echo '<pre>';
var_dump($statuses);

?>