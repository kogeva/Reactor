<?php
namespace Acme\ReactorApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhotoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('From', 'date', array('input' => 'datetime', 'required' => false, 'data' => new \DateTime('2013-09-01 00:00:00')))
            ->add('To', 'date', array('input' => 'datetime', 'required' => false, 'data' => new \DateTime('+ 1 day')));
    }

    public function getName()
    {
        return 'reactr_photo_list';
    }
}