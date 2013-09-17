<?php

namespace Acme\ReactorApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StaticInfo
 */
class StaticInfo
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $about_reactr;

    /**
     * @var string
     */
    private $privacy;

    /**
     * @var string
     */
    private $terms;

    /**
     * @var string
     */
    private $contact_us;

    /**
     * @var string
     */
    private $how_it_works;


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
     * Set about_reactr
     *
     * @param string $aboutReactr
     * @return StaticInfo
     */
    public function setAboutReactr($aboutReactr)
    {
        $this->about_reactr = $aboutReactr;
    
        return $this;
    }

    /**
     * Get about_reactr
     *
     * @return string 
     */
    public function getAboutReactr()
    {
        return $this->about_reactr;
    }

    /**
     * Set privacy
     *
     * @param string $privacy
     * @return StaticInfo
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
    
        return $this;
    }

    /**
     * Get privacy
     *
     * @return string 
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * Set terms
     *
     * @param string $terms
     * @return StaticInfo
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;
    
        return $this;
    }

    /**
     * Get terms
     *
     * @return string 
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set contact_us
     *
     * @param string $contactUs
     * @return StaticInfo
     */
    public function setContactUs($contactUs)
    {
        $this->contact_us = $contactUs;
    
        return $this;
    }

    /**
     * Get contact_us
     *
     * @return string 
     */
    public function getContactUs()
    {
        return $this->contact_us;
    }

    /**
     * Set how_it_works
     *
     * @param string $howItWorks
     * @return StaticInfo
     */
    public function setHowItWorks($howItWorks)
    {
        $this->how_it_works = $howItWorks;
    
        return $this;
    }

    /**
     * Get how_it_works
     *
     * @return string 
     */
    public function getHowItWorks()
    {
        return $this->how_it_works;
    }
    /**
     * @var string
     */
    private $email;


    /**
     * Set email
     *
     * @param string $email
     * @return StaticInfo
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
     * @var string
     */
    private $email_body;

    /**
     * @var string
     */
    private $email_subject;


    /**
     * Set email_body
     *
     * @param string $emailBody
     * @return StaticInfo
     */
    public function setEmailBody($emailBody)
    {
        $this->email_body = $emailBody;
    
        return $this;
    }

    /**
     * Get email_body
     *
     * @return string 
     */
    public function getEmailBody()
    {
        return $this->email_body;
    }

    /**
     * Set email_subject
     *
     * @param string $emailSubject
     * @return StaticInfo
     */
    public function setEmailSubject($emailSubject)
    {
        $this->email_subject = $emailSubject;
    
        return $this;
    }

    /**
     * Get email_subject
     *
     * @return string 
     */
    public function getEmailSubject()
    {
        return $this->email_subject;
    }
}