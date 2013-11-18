<?php

namespace Acme\ReactorApiBundle\Controller;

use Acme\ReactorApiBundle\Entity\Friend;
use Acme\ReactorApiBundle\Entity\Message;
use Acme\ReactorApiBundle\Entity\User;
use Acme\ReactorApiBundle\Entity\DeletedMessage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    const WEEK = 7;
    public function indexAction($name)
    {
        var_dump($this->getRequest()->files) or die;
        return $this->render('AcmeReactorApiBundle:Default:index.html.twig', array('name' => $name));
    }

    public function registrationAction(Request $request)
    {
        $userName    = $request->get('username', false);
        $email       = $request->get('email', false);
        $password    = $request->get('password', false);
        $phone       = $request->get('phone', false);
        $deviceToken = $request->get('device_token', false);

        if($userName && $email && $password && $phone && $deviceToken)
        {
                $user = new User();
                $user->setUsername($userName);
                $user->setEmail($email);
                $user->setPhone($phone);
                $user->setDeviceToken($deviceToken);
                $user->setPrivacyMessage(true);
                $user->setSessionHash(md5(time()));
                $user->setCreatedAt(new \DateTime());
                $pass = base64_encode($password.'|'.$user->getCreatedAt()->format('c').$user->getUsername());
                $user->setPassword($pass);

                $validator = $this->get('validator');
                $errors =  $validator->validate($user);

                if(count($errors))
                {
                    $messages = array();
                    foreach($errors as $error)
                        $messages[$error->getPropertyPath()] = $error->getMessage();
                    return new JsonResponse(array( 'status' => 'failed', 'errors' => $messages));
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                //Sending an email
                $staticInfo = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:StaticInfo')->findAll();
                $info = $staticInfo[0];
                $email_message = \Swift_Message::newInstance()
                    ->setSubject($info->getEmailSubject())
                    ->setFrom(array('no-reply@reactrapp.com' => 'Reactr'))
                    ->setTo($email)
                    ->setBody(
                        $this->renderView('AcmeReactorApiBundle:Api:email.html.twig',
                            array(
                                'staticInfo' => $info)),'text/html'
                    );

                $this->get('mailer')->send($email_message);

                return new JsonResponse(array(
                    'status'       => 'success',
                    'user_id'      => $user->getId(),
                    'session_hash' => $user->getSessionHash()
                ));
        }
        else
            return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required fields is empty'));
    }

    public function loginAction(Request $request)
    {
        $email       = $request->get('email', false);
        $password    = $request->get('password', false);
        $deviceToken = $request->get('device_token', false);

        if($email && $password && $deviceToken)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneByEmail($email);

            if(!$user)
                return new JsonResponse(array( 'status' => 'failed', 'error' => 'Email not found'));

            $pass = base64_encode($password.'|'.$user->getCreatedAt()->format('c').$user->getUsername());

            if($user->getPassword() === md5($password))
            {
                $user->setSessionHash(md5(time()));
                $user->setPassword($pass);
                $user->setDeviceToken($deviceToken);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return new JsonResponse(array(
                    'status' => 'success',
                    'user_id' => $user->getId(),
                    'session_hash' => $user->getSessionHash(),
                    'privacy_message' => $user->getPrivacyMessage(),
                    'username' => $user->getUsername(),
                    'phone' => $user->getPhone()
                ));
            }
            elseif ($user->getPassword() === $pass)
            {
                $user->setSessionHash(md5(time()));
                $user->setDeviceToken($deviceToken);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return new JsonResponse(array(
                    'status' => 'success',
                    'user_id' => $user->getId(),
                    'session_hash' => $user->getSessionHash(),
                    'privacy_message' => $user->getPrivacyMessage(),
                    'username' => $user->getUsername(),
                    'phone' => $user->getPhone()
                ));
            }
            else
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect password'));
        }
    }

    public function setPrivacyMessageAction(Request $request)
    {
        $userId          = $request->get('user_id', false);
        $sessionHash     = $request->get('session_hash', false);
        $privacyMessage = $request->get('privacy_message', '');

        if($userId && $sessionHash && $privacyMessage != '')
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));
            $user->setPrivacyMessage((boolean)$privacyMessage);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(array( 'status' => 'success'));
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function editUserDataAction(Request $request)
    {
        $userId          = $request->get('user_id', false);
        $sessionHash     = $request->get('session_hash', false);
        $email           = $request->get('email', false);
        $phone           = $request->get('phone', false);

        if($userId && $sessionHash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $errorArray = array();

            if($email)
            {
               $existUser = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('email' => $email));
                if($existUser)
                    $errorArray[] = 'This email already exists in the system';
                else
                    $user->setEmail($email);
            }

            if($phone)
            {
               $existUser = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('phone' => $phone));
                if($existUser)
                    $errorArray[] = 'This phone already exists in the system';
                else
                    $user->setPhone($phone);
            }

            if(count($errorArray))
                return new JsonResponse(array( 'status' => 'failed', 'errors' => $errorArray));

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(array( 'status' => 'success'));
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function addFriendAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);
        $friendPhone = $request->get('friend_phone', false);

        if($userId && $sessionHash)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $friendUser = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneByPhone($friendPhone);

            $ids = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->checkExistRelationFriends($userId, $friendUser->getId());
            if($ids)
                return new JsonResponse(array( 'status' => 'failed', 'error' => 'the user is already in your friends'));

            if($friendUser)
            {
                $friend = new Friend();
                $friend->setUser($friendUser);
                $friend->setFriend($user);
                $friend->setShipping($friendUser);
                $friend->setUserId($user->getId());
                $friend->setFriendId($friendUser->getId());
                $friend->setCreatedAt(new \DateTime());
                $friend->setBlocked(false);

                $em->persist($friend);
                $em->flush();

                return new JsonResponse(array(
                    'status' => 'success',
                    'friend_id'  => $friendUser->getId(),
                    'friend_phone' => $friendUser->getPhone(),
                    'friend_username' => $friendUser->getUserName())
                );
            }
            else
            {
                $apns = new \ApnsPHP_Push_Server(
                    \ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
                    'reactr.pem');
                die('die');
            }

        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));

    }

    public function getFriendsAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);

        if($userId && $sessionHash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $friends = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->getFriendsArray($userId);

            foreach($friends as $key => $friend)
            {
                $friendIds = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->getIdAndBlockedFriends($friend['id']);

                $friendIdsArray = array();
                $isBlockedMe = null;

                foreach($friendIds as $friendId)
                {
                    $friendIdsArray[] = $friendId['friend_id'];
                    if($friendId['friend_id'] == $userId)
                        $isBlockedMe = $friendId['blocked'];
                }

                $friends[$key]['blocked_me'] = $isBlockedMe;

                if(array_search($userId,$friendIdsArray) !== false)
                    $friends[$key]['confirmed'] = true ;
                else
                    $friends[$key]['confirmed'] = false ;
            }

            return new JsonResponse(array(
                    'status' => 'success',
                    'friends' => $friends)
            );
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function setBlockFriendAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);
        $friendId    = $request->get('friend_id', false);
        $blocked     = $request->get('set_block', false);

        if($userId && $sessionHash && $friendId)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            /** @var $friend Friend */
            $friend = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->findOneByFriendId($friendId, $userId);

            if($friend)
            {
                if($blocked)
                    $friend->setBlocked(true);
                else
                    $friend->setBlocked(false);

                $em =$this->getDoctrine()->getEntityManager();
                $em->persist($friend);
                $em->flush();

                return new JsonResponse(array('status' => 'success'));
            }
            else
                return new JsonResponse(array(
                    'status' => 'failed',
                    'error'  => 'friend not found'
                ));
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));

    }

    public function deleteFriendAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);
        $friendId    = $request->get('friend_id', false);

        if($userId && $sessionHash && $friendId)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $friend = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->findOneBy(array('friend_id' => $friendId, 'user_id' => $userId));
            if(!$friend)
                return new JsonResponse(array( 'status' => 'failed', 'error' => 'Friend not found'));

            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($friend);
            $em->flush();

            return new JsonResponse(array( 'status' => 'sucess'));
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function checkUserInSystemAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);
        $phones      = $request->get('phones', false);

        if($userId && $sessionHash && $phones)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $phoneArray = explode(',', $phones);

            $phoneArraInSystem = array();

            foreach($phoneArray as $phone)
            {
                if ($phone[0] == "+")
                    $phone = str_replace("+","",$phone);

                $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findUserByPhone($phone);

                $phoneArraInSystem[] = array(
                    'phone' => $phone,
                    'id' => (!empty($user)) ? $user[0]['id'] : null,
                    'username' => (!empty($user)) ? $user[0]['username'] : null);
            }

            return new JsonResponse(array(
                    'status' => 'success',
                    'users' => $phoneArraInSystem)
            );
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function getWhoAddMeAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);

        if($userId && $sessionHash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $friends = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->getIdFriendsWhoAddMe($user);

            return new JsonResponse(array(
                    'status' => 'success',
                    'friends' => $friends)
            );

        }
        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function searchFriendsAction(Request $request)
    {
        $userId      = $request->get('user_id', false);
        $sessionHash = $request->get('session_hash', false);
        $username    = $request->get('friend_username', false);

        if($userId && $sessionHash && $username)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $friends = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findByUserName($username);

            return new JsonResponse(array(
                    'status' => 'success',
                    'friends' => (!empty($friends)) ? $friends : 'null'
                )
            );
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function sendMessagesAction(Request $request)
    {
        $userId       = $request->get('user_id', false);
        $session_hash = $request->get('session_hash', false);
        $friend_ids   = $request->get('friend_ids', false);
        $text         = $request->get('text', '');
        $file         = $request->files->get('photo');
        $reactionFile = $request->files->get('reaction_photo', false);

        if($userId && $session_hash && $friend_ids && $file)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $filename = sha1(microtime());
            $file = $file->move($this->get('kernel')->getRootDir(). '/../web/images', $filename);
            if($reactionFile)
            {
                $reactionFilename = sha1(microtime());
                $reactionFile = $reactionFile->move($this->get('kernel')->getRootDir(). '/../web/images', $reactionFilename);
            }

            $friends = explode(',', $friend_ids);

            $sendedMessages = array();

            $pushNotificationDataIOS = array();
            $pushNotificationDataAndroid = array();
            foreach($friends as $friend_id)
            {
                $friend_user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($friend_id);

                $message = new Message();
                $message->setFromUser($userId);
                $message->setToUser($friend_id);
                $message->setPhoto($this->generateSrcImage($filename));
                $message->setText($text);
                $message->setDeletedByFrom('none');
                $message->setDeletedByTo('none');

                $reactionPhoto = null;
                $reactionPhotoName = null;
                if($reactionFile)
                {
                     $message->setReactionPhoto($this->generateSrcImage($reactionFilename));
                     $reactionPhoto = $message->getReactionPhoto();
                     $reactionPhotoName = explode('images', $reactionPhoto);
                }
                $message->setTo($friend_user);
                $message->setFrom($user);
                $message->setCreatedAt(new \DateTime());

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($message);
                $em->flush();

                $countNotReadMessage = $em->getRepository('AcmeReactorApiBundle:Message')->countNotRead($friend_id);
                $photo = $message->getPhoto();
                $photoName = explode('images',$photo);

                if (strlen($friend_user->getDeviceToken())  > 73)
                    $pushNotificationDataAndroid[] = array(str_replace(array(' ', '>', '<'), '', $friend_user->getDeviceToken()),'You have new message from '. $user->getUsername(),
                        (string)$message->getId(), $photo, $reactionPhoto, $message->getText(), (string)$userId );
                else
                    $pushNotificationDataIOS[] = array(str_replace(array(' ', '>', '<'), '', $friend_user->getDeviceToken()),'You have new message from '. $user->getUsername(),
                        $countNotReadMessage, $message->getId(), $photoName[1], $reactionPhotoName[1], $message->getText(), $userId);

                $sendedMessages[] = $message->toArray();
            }
            exec("php ".__DIR__."/../ApnsPHPBundle/sample_push.php '".serialize($pushNotificationDataIOS) ."' > /dev/null &");
            exec("php ".__DIR__."/../GCMPhp/GCM_push.php '".serialize($pushNotificationDataAndroid) ."' > /dev/null &");

            return new JsonResponse(array(
                    'status' => 'success',
                    'messages' => $sendedMessages
                )
            );
        }
        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));

    }

    public function countNotReadMessageAction(Request $request)
    {

        $userId       = $request->get('user_id', false);
        $session_hash = $request->get('session_hash', false);

        if($userId && $session_hash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));
            $em = $this->getDoctrine()->getEntityManager();


            $countMessage = $em->getRepository('AcmeReactorApiBundle:Message')->countNotRead($userId);

            return new JsonResponse(array(
                    'status' => 'success',
                    'count' => $countMessage
                )
            );
        }
        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function getMessagesAction(Request $request)
    {
        $userId       = $request->get('user_id', false);
        $session_hash = $request->get('session_hash', false);
        $from         = $request->get('from', 0);
        $to           = $request->get('to', 15);

        if($userId && $session_hash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $em = $this->getDoctrine()->getEntityManager();
            $messages = $em->getRepository('AcmeReactorApiBundle:Message')->findAllByUserId($userId, $from, $to - $from);

            $currentDate = new \DateTime("now");

            foreach($messages as $key => $value)
            {
                $interval = $currentDate->diff($value['created_at']);
                $messages[$key]['deleted'] = ($interval->d > self::WEEK) ? true : false ;

                if($value['from_user'] == $userId)
                    $messages[$key]['from_me'] = true;
                else
                    $messages[$key]['from_me'] = false;
            }
            return new JsonResponse(array(
                'status' => 'success',
                'messages' => $messages)
            );
        }
    }

    public function readMessagesAction(Request $request)
    {
        $userId       = $request->get('user_id', false);
        $session_hash = $request->get('session_hash', false);
        $messageId    = $request->get('message_id', false);

        if($userId && $session_hash && $messageId)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $message = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Message')->findByIdAndToUser($messageId, $userId);

            if($message)
            {
                $message->setIsRead(true);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($message);
                $em->flush();

                return new JsonResponse(array('status' => 'success'));
            }

            return new JsonResponse(array( 'status' => 'failed', 'error' => 'message not found in system'));
        }

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function getStaticInfoAction(Request $request)
    {
        $staticInfoCollection = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:StaticInfo')->findAll();

        foreach($staticInfoCollection as  $staticInfo)
            return new JsonResponse(array(
                    'status' => 'success',
                    'static_info' => array(
                         'about_reactr' => $staticInfo->getAboutReactr(),
                         'privacy' => $staticInfo->getPrivacy(),
                         'terms' => $staticInfo->getTerms(),
                         'contact_us' => $staticInfo->getContactUs(),
                         'how_it_works' => $staticInfo->getHowItWorks()
                    ))
            );

        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function checkUsernameAndEmailAction (Request $request)
    {
        $username = $request->get('username', false);
        $email =  $request->get('email', false);

        $users = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')
            ->findByUsernameAndEmail($username, $email);

        if(count($users) > 0)
        {
            $existStack = array();
            foreach($users as $user)
            {
                if($user['username'] == $username)
                    $existStack[] = array('username' => $user['username']);
                elseif($user['email'] == $email)
                    $existStack[] = array('email' => $user['email']);
            }

            return new JsonResponse(array(
                    'status' => 'success',
                    'fields' => $existStack)
            );
        }
        return new JsonResponse(array( 'status' => 'failed'));
    }

    public function deleteMessageAction(Request $request)
    {
        $userId       = $request->get('user_id', false);
        $session_hash = $request->get('session_hash', false);
        $messageId    = $request->get('message_id', false);

        if ($userId && $session_hash && $messageId)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $message = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Message')->find($messageId);

            if($message)
            {
                if ($message->getFromUser() == $userId)
                    $message->setDeletedByFrom($userId);
                else
                    $message->setDeletedByTo($userId);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($message);
                $em->flush();

                return new JsonResponse(array('status' => 'success'));
            }
            else
                return new JsonResponse(array('status' => 'failed', 'error' => 'message not found in system'));

        }
        return new JsonResponse(array( 'status' => 'failed', 'error' => 'one of required parameters not defined'));
    }

    public function remindPasswordAction()
    {
        $request = $this->container->get('request')->request;
        $email = $request->get('email');
        $phone = $request->get('phone');

        if ($email)
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('email' => $email));
        elseif ($phone)
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('phone' => $phone));

        $reset_url = 'http://reactrapp.com/reset.php?username='.base64_encode($user->getUsername()).'&reset='.md5($user->getUsername().$user->getSessionHash()).'&time='.(time() + 10800);

        $this->sendRemindPassword($user, $reset_url);

        return new JsonResponse(array( 'status' => 'success'));
    }

    protected function sendRemindPassword($user, $url)
    {
        $email_message = \Swift_Message::newInstance()
            ->setSubject('Remind password')
            ->setFrom(array('no-reply@reactrapp.com' => 'Reactr'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('AcmeReactorApiBundle:Api:remind.html.twig',
                    array('username' => $user->getUsername(),
                        'url' => $url
                    )),'text/html'
            );

        $this->get('mailer')->send($email_message);
    }

    public function resetPasswordAction()
    {
        $request = $this->container->get('request')->request;
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('username' => $username));
        $password = base64_encode($password.'|'.$user->getCreatedAt()->format('c').$user->getUsername());
        $user->setPassword($password);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse('Password changed');
    }


    private function generateSrcImage($filename)
    {
        return 'http://'.$this->getRequest()->getHost().'/images/'.$filename;
    }

}
