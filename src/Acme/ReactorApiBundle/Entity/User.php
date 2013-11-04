<?php

namespace Acme\ReactorApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User
{
    private $sentMessagesNum;
    private $receivedMessagesNum;
    private $sentReactionPhotoNum;
    private $receivedReactionPhotoNum;

    public function sentMessagesNum($from, $to)
    {
        $this->sentMessagesNum = 0;
        foreach ($this->fromUser as $message)
        {
            if ( ($message->getCreatedAt()->format('Y-m-d H:m:s') >= $from) && ($message->getCreatedAt()->format('Y-m-d H:m:s') <= $to) )
                if ($message->getReactionPhoto() === null)
                    $this->sentMessagesNum++;
        }
        return $this->sentMessagesNum;
    }

    public function receivedMessagesNum($from, $to)
    {
        $this->receivedMessagesNum = 0;
        foreach ($this->toUser as $message)
        {
            if ( ($message->getCreatedAt()->format('Y-m-d H:m:s') >= $from) && ($message->getCreatedAt()->format('Y-m-d H:m:s') <= $to) )
                if ($message->getReactionPhoto() === null)
                    $this->receivedMessagesNum++;
        }
        return $this->receivedMessagesNum;
    }
    public function sentReactionPhotoNum($from, $to)
    {

        foreach ($this->fromUser as $photo)
        {
            if ($photo->getReactionPhoto() != null && $photo->getCreatedAt()->format('Y-m-d H:m:s') >= $from && $photo->getCreatedAt()->format('Y-m-d H:m:s') <= $to)
                $this->sentReactionPhotoNum[] = $photo->getReactionPhoto();
        }
        return count($this->sentReactionPhotoNum);
    }

    public function receivedReactionPhotoNum($from, $to)
    {
        foreach ($this->toUser as $photo)
        {
            if ($photo->getReactionPhoto() != null && $photo->getCreatedAt()->format('Y-m-d H:m:s') >= $from && $photo->getCreatedAt()->format('Y-m-d H:m:s') <= $to)
                $this->receivedReactionPhotoNum[] = $photo->getReactionPhoto();
        }
        return count($this->receivedReactionPhotoNum);
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var integer
     */
    private $phone;

    /**
     * @var string
     */
    private $device_token;

    /**
     * @var string
     */
    private $session_hash;

    /**
     * @var boolean
     */
    private $privacy_message;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $friends;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->friends = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return integer 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set device_token
     *
     * @param string $deviceToken
     * @return User
     */
    public function setDeviceToken($deviceToken)
    {
        $this->device_token = $deviceToken;
    
        return $this;
    }

    /**
     * Get device_token
     *
     * @return string 
     */
    public function getDeviceToken()
    {
        return $this->device_token;
    }

    /**
     * Set session_hash
     *
     * @param string $sessionHash
     * @return User
     */
    public function setSessionHash($sessionHash)
    {
        $this->session_hash = $sessionHash;
    
        return $this;
    }

    /**
     * Get session_hash
     *
     * @return string 
     */
    public function getSessionHash()
    {
        return $this->session_hash;
    }

    /**
     * Set privacy_message
     *
     * @param boolean $privacyMessage
     * @return User
     */
    public function setPrivacyMessage($privacyMessage)
    {
        $this->privacy_message = $privacyMessage;
    
        return $this;
    }

    /**
     * Get privacy_message
     *
     * @return boolean 
     */
    public function getPrivacyMessage()
    {
        return $this->privacy_message;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Add friends
     *
     * @param \Acme\ReactorApiBundle\Entity\Friend $friends
     * @return User
     */
    public function addFriend(\Acme\ReactorApiBundle\Entity\Friend $friends)
    {
        $this->friends[] = $friends;
    
        return $this;
    }

    /**
     * Remove friends
     *
     * @param \Acme\ReactorApiBundle\Entity\Friend $friends
     */
    public function removeFriend(\Acme\ReactorApiBundle\Entity\Friend $friends)
    {
        $this->friends->removeElement($friends);
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriends()
    {
        return $this->friends;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        // Add your code here
    }
}