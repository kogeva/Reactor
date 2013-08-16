<?php

namespace Acme\ReactorApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmeReactorApiBundle:Default:index.html.twig', array('name' => $name));
    }
}
