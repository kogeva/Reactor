<?php

namespace Acme\ReactorApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friend
 */
class Friend
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $user_id;

    /**
     * @var integer
     */
    private $friend_id;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \Acme\ReactorApiBundle\Entity\User
     */
    private $shipping;

    /**
     * @var \Acme\ReactorApiBundle\Entity\User
     */
    private $user;


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
     * Set user_id
     *
     * @param integer $userId
     * @return Friend
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set friend_id
     *
     * @param integer $friendId
     * @return Friend
     */
    public function setFriendId($friendId)
    {
        $this->friend_id = $friendId;
    
        return $this;
    }

    /**
     * Get friend_id
     *
     * @return integer 
     */
    public function getFriendId()
    {
        return $this->friend_id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Friend
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
     * Set shipping
     *
     * @param \Acme\ReactorApiBundle\Entity\User $shipping
     * @return Friend
     */
    public function setShipping(\Acme\ReactorApiBundle\Entity\User $shipping = null)
    {
        $this->shipping = $shipping;
    
        return $this;
    }

    /**
     * Get shipping
     *
     * @return \Acme\ReactorApiBundle\Entity\User 
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * Set user
     *
     * @param \Acme\ReactorApiBundle\Entity\User $user
     * @return Friend
     */
    public function setUser(\Acme\ReactorApiBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Acme\ReactorApiBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        // Add your code here
    }
    /**
     * @var \Acme\ReactorApiBundle\Entity\User
     */
    private $friend;


    /**
     * Set friend
     *
     * @param \Acme\ReactorApiBundle\Entity\User $friend
     * @return Friend
     */
    public function setFriend(\Acme\ReactorApiBundle\Entity\User $friend = null)
    {
        $this->friend = $friend;
    
        return $this;
    }

    /**
     * Get friend
     *
     * @return \Acme\ReactorApiBundle\Entity\User 
     */
    public function getFriend()
    {
        return $this->friend;
    }
}