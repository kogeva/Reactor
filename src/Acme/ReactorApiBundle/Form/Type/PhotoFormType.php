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
            ->add('From', 'date', array('input' => 'datetime', 'required' => false, 'data' => new \DateTime('2008-01-01')))
            ->add('To', 'date', array('input' => 'datetime', 'required' => false, 'data' => new \DateTime('now')));
    }

    public function getName()
    {
        return 'reactr_photo_list';
    }
}