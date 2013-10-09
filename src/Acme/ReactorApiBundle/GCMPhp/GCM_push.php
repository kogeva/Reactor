<?php

$serializeData = $argv[1];

$data = unserialize($serializeData);
$url = 'https://android.googleapis.com/gcm/send';
$apiKey = "AIzaSyBo3MA5K2WCjVhiXNTvnlThV8dsZ_gB4zQ";


date_default_timezone_set('Europe/Rome');
error_reporting(-1);

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

foreach($data as $entity)
{
    $token         = $entity[0];
    $text          = $entity[1];
    $registrationIDs = array( $token );

    $fields = array(
        'registration_ids'  => $registrationIDs,
        'data'              => array ("message" => $text ),
    );

    $headers = array(
        'Authorization: key=' . $apiKey,
        'Content-Type: application/json'
    );


    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

    $result = curl_exec($ch);

}
curl_close($ch);
