<?php

$serializeData = $argv[1];

$data = unserialize($serializeData);

date_default_timezone_set('Europe/Rome');
error_reporting(-1);

$url = 'https://android.googleapis.com/gcm/send';
$apiKey = "AIzaSyBo3MA5K2WCjVhiXNTvnlThV8dsZ_gB4zQ";

$headers = array(
    'Authorization: key=' . $apiKey,
    'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);

foreach($data as $entity)
{
    $token               = $entity[0];
    $text                = $entity[1];
    $messageId           = $entity[2];
    $linkToPhoto         = $entity[3];
    $linkToReactionPhoto = $entity[4];
    $textPhoto           = $entity[5];
    $senderId            = $entity[6];

    $registrationIDs = array( $token );

    if (!$linkToReactionPhoto)
        $linkToReactionPhoto = false;

    $fields = array(
        'registration_ids'  => $registrationIDs,
        'data'              => array ("message" => $text,
                                      "message_id" => $messageId,
                                      "photo" => $linkToPhoto,
                                      "text" => $textPhoto,
                                      "reactionPhoto" => $linkToReactionPhoto,
                                      "senderId" => $senderId
                                      ));

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

    $result = curl_exec($ch);
    echo $result;
}
curl_close($ch);
