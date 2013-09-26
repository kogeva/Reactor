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
    private $text;

    /**
     * @var string
     */
    private $photo;

    /**
     * @var string
     */
    private $reaction_photo;

    /**
     * @var boolean
     */
    private $is_read;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var integer
     */
    private $deletedByTo;

    /**
     * @var integer
     */
    private $deletedByFrom;

    /**
     * @var \Acme\ReactorApiBundle\Entity\User
     */
    private $to;

    /**
     * @var \Acme\ReactorApiBundle\Entity\User
     */
    private $from;


    public function toArray()
    {
        return array(
            'id'             => $this->id,
            'from_user'      => $this->from_user,
            'to_user'        => $this->to_user,
            'photo'          => $this->photo,
            'reaction_photo' => $this->reaction_photo,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
            'text'           => $this->text
        );
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
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
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

    /**
     * Set is_read
     *
     * @param boolean $isRead
     * @return Message
     */
    public function setIsRead($isRead)
    {
        $this->is_read = $isRead;
    
        return $this;
    }

    /**
     * Get is_read
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->is_read;
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
     * Set deletedByTo
     *
     * @param integer $deletedByTo
     * @return Message
     */
    public function setDeletedByTo($deletedByTo)
    {
        $this->deletedByTo = $deletedByTo;
    
        return $this;
    }

    /**
     * Get deletedByTo
     *
     * @return integer 
     */
    public function getDeletedByTo()
    {
        return $this->deletedByTo;
    }

    /**
     * Set deletedByFrom
     *
     * @param integer $deletedByFrom
     * @return Message
     */
    public function setDeletedByFrom($deletedByFrom)
    {
        $this->deletedByFrom = $deletedByFrom;
    
        return $this;
    }

    /**
     * Get deletedByFrom
     *
     * @return integer 
     */
    public function getDeletedByFrom()
    {
        return $this->deletedByFrom;
    }

    /**
     * Set to
     *
     * @param \Acme\ReactorApiBundle\Entity\User $to
     * @return Message
     */
    public function setTo(\Acme\ReactorApiBundle\Entity\User $to = null)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return \Acme\ReactorApiBundle\Entity\User 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set from
     *
     * @param \Acme\ReactorApiBundle\Entity\User $from
     * @return Message
     */
    public function setFrom(\Acme\ReactorApiBundle\Entity\User $from = null)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return \Acme\ReactorApiBundle\Entity\User 
     */
    public function getFrom()
    {
        return $this->from;
    }
}