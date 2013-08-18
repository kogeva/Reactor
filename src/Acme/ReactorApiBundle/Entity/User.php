<?php

namespace Acme\ReactorApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User
{
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
     * @var \DateTime
     */
    private $created_at;


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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        // Add your code here
    }
    /**
     * @var string
     */
    private $session_hash;


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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $message;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->message = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add message
     *
     * @param \Acme\ReactorApiBundle\Entity\User $message
     * @return User
     */
    public function addMessage(\Acme\ReactorApiBundle\Entity\User $message)
    {
        $this->message[] = $message;
    
        return $this;
    }

    /**
     * Remove message
     *
     * @param \Acme\ReactorApiBundle\Entity\User $message
     */
    public function removeMessage(\Acme\ReactorApiBundle\Entity\User $message)
    {
        $this->message->removeElement($message);
    }

    /**
     * Get message
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param \Acme\ReactorApiBundle\Entity\Message $message
     * @return User
     */
    public function setMessage(\Acme\ReactorApiBundle\Entity\Message $message = null)
    {
        $this->message = $message;
    
        return $this;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $friends;


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
     * @var string
     */
    private $device_token;


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
}