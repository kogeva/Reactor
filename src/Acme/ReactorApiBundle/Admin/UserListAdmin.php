<?php

namespace Acme\ReactorApiBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class UserListAdmin extends Admin
{

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('username')
            ->add('sentMessagesNum', null, array('label' => 'Sent photos'))
            ->add('receivedMessagesNum', null, array('label' => 'Received photos'))
            ->add('sentReactionPhotoNum', null, array('label' => 'Sent reaction photos'))
            ->add('receivedReactionPhotoNum', null, array('label' => 'Received reaction photos'))
        ;
    }
}