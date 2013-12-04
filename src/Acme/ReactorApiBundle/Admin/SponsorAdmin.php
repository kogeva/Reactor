<?php
namespace Acme\ReactorApiBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SponsorAdmin extends Admin
{
    public function getExportFormats()
    {
        return array('xls');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('site_url')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('site_url')
            ->add('logo', null, array('label' => 'Logo', 'template' => 'AcmeReactorApiBundle:Admin:logo.html.twig'))
            ->add('selected', null, array('label' => 'Active', 'template' => 'AcmeReactorApiBundle:Admin:selected.html.twig'))
        ;
    }
}