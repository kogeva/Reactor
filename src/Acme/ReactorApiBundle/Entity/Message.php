<?php

namespace Acme\ReactorApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 */
class Message
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $from_user;

    /**
     * @var integer
     */
    private $to_user;

    /**
     * @var string
     */
    private $photo;

    /**
     * @var \DateTime
     */
    private $created_at;

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
     * Set from_user
     *
     * @param integer $fromUser
     * @return Message
     */
    public function setFromUser($fromUser)
    {
        $this->from_user = $fromUser;
    
        return $this;
    }

    /**
     * Get from_user
     *
     * @return integer 
     */
    public function getFromUser()
    {
        return $this->from_user;
    }

    /**
     * Set to_user
     *
     * @param integer $toUser
     * @return Message
     */
    public function setToUser($toUser)
    {
        $this->to_user = $toUser;
    
        return $this;
    }

    /**
     * Get to_user
     *
     * @return integer 
     */
    public function getToUser()
    {
        return $this->to_user;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Message
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    
        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Message
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
     * Set user
     *
     * @param \Acme\ReactorApiBundle\Entity\User $user
     * @return Message
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
     * @var string
     */
    private $reaction_photo;


    /**
     * Set reaction_photo
     *
     * @param string $reactionPhoto
     * @return Message
     */
    public function setReactionPhoto($reactionPhoto)
    {
        $this->reaction_photo = $reactionPhoto;
    
        return $this;
    }

    /**
     * Get reaction_photo
     *
     * @return string 
     */
    public function getReactionPhoto()
    {
        return $this->reaction_photo;
    }
}