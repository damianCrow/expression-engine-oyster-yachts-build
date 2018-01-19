<?php

require_once 'campaignmonitor/csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('095d83a73f42950fb381a0d37e2435a9', '7093bebdb28fc3cf0faa859b2467fd00bdc990e40500bd6d');
$result = $wrap->add(array(
    'EmailAddress' => $_POST['email'],
    'Name' => $_POST['fname'].' '.$_POST['sname'],
    'CustomFields' => array(
        array(
            'Key' => 'Country',
            'Value' => $_POST['country']
        )
    ),
    'Resubscribe' => true
));

if ($result->was_successful()) {
    die('{"status": "success"}');
} else {
    die('{"status": "error"}');
}

?>