<?php
namespace Acme\ReactorApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SponsorLogoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array('data' =>  $options['data']['name']))
            ->add('site_url', 'text', array('data' =>  $options['data']['site_url']))
            ->add('logo', 'file', array('required' => true, 'label' => 'Logo'));
    }

    public function getName()
    {
        return 'reactr_sponsor_logo_form_type';
    }
}