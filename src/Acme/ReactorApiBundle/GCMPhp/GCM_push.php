<?php

require_once "sendNotification.php";

$serializeData = $argv[1];
$data = unserialize($serializeData);
$browserApiKey = 'AIzaSyBY0Oi_BEjZTN7NV_piVpnVaf2cFT_Guik';

foreach($data as $entity)
{

    $token = $entity[0];
    $text = $entity[1];
    $notRead = $entity[2];
    $messageId = $entity[3];
    $photo = $entity[4];
    $reactionPhoto = $entity[5];
    $messageText = $entity[6];
    $userId = $entity[7];

    $response = sendNotification(
        $browserApiKey,
        array($token),
        array(
            'badge' => $notRead,
            'text' => $text,
            'message_id' => $messageId,
            'photoLink' => $photo,
            'reactorPhotoLink' => $reactionPhoto,
            'message_text' => $messageText,
            'user_id' => $userId
        )
    );

    echo $response;
}