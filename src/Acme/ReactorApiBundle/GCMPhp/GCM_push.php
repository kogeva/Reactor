<?php
    $serializeData = $argv[1];

    $data = unserialize($serializeData);

    $apiKey = "AIzaSyBo3MA5K2WCjVhiXNTvnlThV8dsZ_gB4zQ";
    $url = 'https://android.googleapis.com/gcm/send';

    date_default_timezone_set('Europe/Rome');

    error_reporting(-1);
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

foreach($data as $entity)
    {

        $token         = $entity[0];
        $text          = $entity[1];
        $notRead       = $entity[2];
        $messageId     = $entity[3];
        $photo         = $entity[4];
        $reactionPhoto = $entity[5];
        $messageText   = $entity[6];
        $userId        = $entity[7];

        $registrationIDs = array($token);

        $pushNotification = array(
            'badge'               => $notRead,
            'text'                => $text,
            'message_id'          => $messageId,
            'linkToPhoto'         => $photo,
            'linkToReactionPhoto' => $reactionPhoto,
            'messageText'         => $messageText,
            'userId'              => $userId
        );

        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => $pushNotification,
        );

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields ));
        $result = curl_exec($ch);

        echo $result;
}

curl_close($ch);
