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
            ->add('how_it_works')
            ->add('email')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('About Reactr')
            ->add('Privacy')
            ->add('Terms')
            ->add('Contact Us')
            ->add('How It Works')
            ->add('email')
        ;
    }
}