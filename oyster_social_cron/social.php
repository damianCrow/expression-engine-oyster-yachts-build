<?php

$system_path = '../oysteryachts.interstateteam.com/system';
require_once 'eecli/bootstrap/bootstrap-ee3.php';

require_once PATH_THIRD.'social_feed/mod.social_feed.php';

$social = new Social_feed();

function send_email($feed) {
	$to      = 'macdochris@gmail.com';
	$subject = 'Oyster cron error ('.$feed.')';
	$message = '';
	$headers = 'From: chris.m@interstateteam.com' . "\r\n" .
    'Reply-To: chris.m@interstateteam.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
}

// Instagram
try {
	$social->update_instagram();
} catch (SomeException $e) {
	send_email('instagram');
}

// Youtube
try {
	$social->update_youtube();
} catch (SomeException $e) {
	send_email('youtube');
}

// Twitter
try {
	$social->update_twitter();
} catch (SomeException $e) {
	send_email('twitter');
}

?>