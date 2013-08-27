<?php

namespace Acme\ReactorApiBundle\Controller;

use Acme\ReactorApiBundle\Entity\Friend;
use Acme\ReactorApiBundle\Entity\Message;
use Acme\ReactorApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
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
            $user->setPassword(md5($password));
            $user->setPhone($phone);
            $user->setDeviceToken($deviceToken);
            $user->setPrivacyMessage(false);
            $user->setSessionHash(md5(time()));
            $user->setCreatedAt(new \DateTime());

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
            return new JsonResponse(array(
                'status'       => 'success',
                'user_id'      => $user->getId(),
                'session_hash' => $user->getSessionHash()));
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

            if($user->getPassword() === md5($password))
            {
                $user->setSessionHash(md5(time()));
                $user->setDeviceToken($deviceToken);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return new JsonResponse(array(
                    'status' => 'success',
                    'user_id' => $user->getId(),
                    'session_hash' => $user->getSessionHash()));
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
        $username        = $request->get('username', false);
        $phone           = $request->get('phone', false);

        if($userId && $sessionHash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $sessionHash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $errorArray = array();

            if($username)
            {
               $existUser = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('username' => $username));
                if($existUser)
                    $errorArray[] = 'This user name already exists in the system';
                else
                    $user->setUsername($username);
            }

            if($phone)
            {
               $existUser = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findOneBy(array('phone' => $phone));
                if($existUser)
                    $errorArray[] = 'This phone already exists in the system';
                else
                    $user->setPhone($phone);
            }

            if(count($errorArray) > 0)
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
                die('die'); //do someething

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
                $friendIds = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->getIdFriens($friend['id']);
                $friendIdsArray = array();

                foreach($friendIds as $friendId)
                {
                    $friendIdsArray[] = $friendId['friend_id'];
                }

                if(array_search($friend['id'],$friendIdsArray) !== false)
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

            $friend = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Friend')->findOneBy(array('friend_id' => $friendId));
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
                $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->findUserByPhone($phone);

                $phoneArraInSystem[] = array('phone' => $phone, 'id' => (!empty($user)) ? $user[0]['id'] : null );
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
        $friend_ids    = $request->get('friend_ids', false);
        $text         = $request->get('text', '');
        $file         = $request->files->get('photo');
        $reactionFile = $request->files->get('reaction_photo', false);

        if($userId && $session_hash && $friend_ids && $file)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

            $filename = sha1(time());
            $file = $file->move($this->get('kernel')->getRootDir(). '/../web/images', $filename);
            if($reactionFile)
            {
                $reactionFilename = sha1(time());
                $reactionFile = $reactionFile->move($this->get('kernel')->getRootDir(). '/../web/images', $reactionFilename);
            }

            $friends = explode(',', $friend_ids);

            $sendedMessages = array();

            foreach($friends as $friend_id)
            {
                $friend_user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($friend_id);

                $message = new Message();
                $message->setFromUser($userId);
                $message->setToUser($friend_id);
                $message->setPhoto($this->generateSrcImage($filename));
                $message->setText($text);
                if($reactionFile)
                    $message->setReactionPhoto($this->generateSrcImage($reactionFilename));
                $message->setUser($friend_user);
                $message->setFrom($user);
                $message->setCreatedAt(new \DateTime());

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($message);
                $em->flush();

                $sendedMessages[] = $message->toArray();
            }

            return new JsonResponse(array(
                    'status' => 'success',
                    'messages' => $sendedMessages
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
            $messages = $em->getRepository('AcmeReactorApiBundle:Message')->findAllByUserId($userId);
            foreach($messages as $key => $value)
            {
                $value = $value[0];
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
        $userId       = $request->get('user_id', false);
        $session_hash = $request->get('session_hash', false);

        if($userId && $session_hash)
        {
            $user = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:User')->find($userId);
            if($user->getSessionHash() !== $session_hash)
                return new JsonResponse(array( 'status' => 'failed', 'error' => ' incorrect session hash'));

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
        }

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

    private function generateSrcImage($filename)
    {
        return 'http://'.$this->getRequest()->getHost().'/images/'.$filename;
    }
}
