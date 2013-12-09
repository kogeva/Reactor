<?php
namespace Acme\ReactorApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SponsorLogoFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['data']['name']) && isset($options['data']['site_url']))
        {
            $name = $options['data']['name'];
            $url  = $options['data']['site_url'];
        }
        else
        {
            $name = '';
            $url  = '';
        }
        $builder
            ->add('name', 'text', array('data' =>  $name, 'required' => false))
            ->add('site_url', 'text', array('data' =>  $url, 'required' => false))
            ->add('logo', 'file', array('required' => true, 'label' => 'Logo', 'required' => false));
    }

    public function getName()
    {
        return 'reactr_sponsor_logo_form_type';
    }
}