<?php
/**
 * @file
 * sample_push.php
 *
 * Push demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://code.google.com/p/apns-php/wiki/License
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to aldo.armiento@gmail.com so we can send you a copy immediately.
 *
 * @author (C) 2010 Aldo Armiento (aldo.armiento@gmail.com)
 * @version $Id: sample_push.php 65 2010-12-13 18:38:39Z aldo.armiento $
 */

$serializeData = $argv[1];

$data = unserialize($serializeData);

// Adjust to your timezone
date_default_timezone_set('Europe/Rome');

// Report all PHP errors
error_reporting(-1);

// Using Autoload all classes are loaded on-demand
require_once 'ApnsPHP/Autoload.php';

$push = new ApnsPHP_Push(
	ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
	__DIR__.'/apns-distr.pem'
);

$push->setRootCertificationAuthority(__DIR__.'/apns-distr.pem');
$push->connect();
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

	$message = new ApnsPHP_Message($token);
	$message->setCustomIdentifier("Message-Badge-1");
	$message->setBadge($notRead);
	$message->setText($text);
	$message->setSound();
	$message->setCustomProperty('message_id', $messageId);
	$message->setCustomProperty('photoLink', $photo);
	$message->setCustomProperty('reactionPhotoLink', $reactionPhoto);
	$message->setCustomProperty('text', $messageText);
	$message->setCustomProperty('user_id', $userId);

//	$message->setCustomProperty('acme3', array('bing', 'bong'));
	$message->setExpiry(30);
	$push->add($message);
	$push->send();
}
$push->disconnect();
$aErrorQueue = $push->getErrors();
if (!empty($aErrorQueue)) {
	var_dump($aErrorQueue);
}
