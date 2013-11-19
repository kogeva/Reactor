<?php
namespace Acme\ReactorApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhotoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['data']['from'] && $options['data']['to'])
        {
            $from = $options['data']['from'];
            $to   = $options['data']['to'];
        }
        else
        {
            $from = new \DateTime('2013-09-01 00:00:00');
            $to   = new \DateTime('+ 1 day');
        }
        if ($options['data']['search'])
            $search = $options['data']['search'];
        else
            $search = '';

        if ($options['data']['field'])
            $field = $options['data']['field'];
        else
            $field = 'Phone';
        $builder
            ->add('From', 'date', array('input' => 'datetime', 'required' => false, 'data' => $from))
            ->add('To', 'date', array('input' => 'datetime', 'required' => false, 'data' => $to))
            ->add('type', 'choice', array(
                'choices'   => array('Phone' => 'Phone', 'Email' => 'Email', 'Username' => 'Username'),
                'required'  => false,
                'multiple'  => false,
                'empty_value' => false,
                'label'     => 'Field',
                'data'      => $field

            ))
            ->add('search', 'text', array('required' => false, 'data' => $search, 'label' => 'Search'));
    }

    public function getName()
    {
        return 'reactr_photo_list';
    }
}