<?php
namespace Acme\ReactorApiBundle\Controller;

use Acme\ReactorApiBundle\Form\Type\PhotoFormType;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

class PhotoAdminController extends ContainerAware
{
    public function photoListAction()
    {
        $adminPool = $this->container->get('sonata.admin.pool');
        $form = $this->container->get('form.factory')->create(new PhotoFormType());
        $request = $this->container->get('request');

        $users = $this->container->get('doctrine')->getEntityManager()
            ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAll();


        $requestData = $request->request->get('reactr_photo_list');
        $from = $requestData['From'];
        $to = $requestData['To'];
        $dateFrom = '';
        $dateTo = '';
        if ($from)
        {
            foreach($from as $key => $fr)
                if ($key != 'day')
                    $dateFrom.= $fr . '-';
                else
                    $dateFrom.= $fr;
            $dateFrom.= ' 00:00:00';
            $from = new \DateTime($dateFrom);

        }
        else
        {
            $from = new \DateTime('2008-01-01 00:00:00');
        }
        if ($to)
        {
            foreach($to as $key => $fr)
                if ($key != 'day')
                    $dateTo.= $fr . '-';
                else
                    $dateTo.= $fr;
            $dateTo.= ' 00:00:00';
            $to = new \DateTime($dateTo);
        }
        else
        {
            $to = new \DateTime('now');
        }
        $photos = $this->container->get('doctrine')->getEntityManager()
            ->getRepository('Acme\ReactorApiBundle\Entity\Message')->getAllPhotoInDate($from, $to);
        $listPhotos = array();

        foreach($users as $key => $user)
        {
            $listPhotos[$key]['username'] = $user->getUsername();
            $listPhotos[$key]['sent'] = $user->sentMessagesNum($from->format('Y-m-d'), $to->format('Y-m-d'));
            $listPhotos[$key]['received'] = $user->receivedMessagesNum($from->format('Y-m-d'), $to->format('Y-m-d'));
            $listPhotos[$key]['sentR'] = $user->sentReactionPhotoNum($from->format('Y-m-d'), $to->format('Y-m-d'));
            $listPhotos[$key]['receivedR'] = $user->receivedReactionPhotoNum($from->format('Y-m-d'), $to->format('Y-m-d'));
        }

        return $this->container->get('templating')->renderResponse(
            'AcmeReactorApiBundle:Admin:list.html.twig',
            array(
                'admin_pool' => $adminPool,
                'users'      =>     $listPhotos,
                'form' 		 => $form->createView(),
                'photos'     => $photos,
                'from'       => $from->format('Y-m-d'),
                'to'         => $to->format('Y-m-d')
            )
        );
    }
}