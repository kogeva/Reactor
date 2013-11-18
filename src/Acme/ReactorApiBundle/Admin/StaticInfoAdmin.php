<?php

namespace Acme\ReactorApiBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class StaticInfoAdmin extends Admin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('about_reactr')
            ->add('privacy')
            ->add('terms')
            ->add('contact_us')
            ->add('email_body', null, array('label' => 'Welcome email body'))
            ->add('email_subject', null, array('label' => 'Welcome email subject'))
            ->add('email_body_remind', null, array('label' => 'Reset email body'))
            ->add('email_subject_remind', null, array('label' => 'Reset email subject'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('About Reactr')
            ->add('Privacy')
            ->add('Terms')
            ->add('Contact Us')
            ->add('email_body', null, array('label' => 'Welcome email body'))
            ->add('email_subject', null, array('label' => 'Welcome email subject'))
            ->add('email_body_remind', null, array('label' => 'Reset email body'))
            ->add('email_subject_remind', null, array('label' => 'Reset email subject'))
        ;
    }
}