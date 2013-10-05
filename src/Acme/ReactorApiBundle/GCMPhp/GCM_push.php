<?php

require_once "sendNotification.php";

$serializeData = $argv[1];
$data = unserialize($serializeData);

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

    $data = array(
            'badge' => $notRead,
            'text' => $text,
            'message_id' => $messageId,
            'photoLink' => $photo,
            'reactorPhotoLink' => $reactionPhoto,
            'message_text' => $messageText,
            'user_id' => $userId
    );

    $apiKey = "AIzaSyA93BAblwJIDa3bEknH6dUMH4OjrjWCwzU";
    $devices = array($token);
    $message = $data;

    $gcpm = new GCMPushMessage($apiKey);
    $gcpm->setDevices($devices);
    $response = $gcpm->send($message);
}