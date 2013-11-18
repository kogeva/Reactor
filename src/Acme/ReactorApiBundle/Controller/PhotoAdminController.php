<?php
namespace Acme\ReactorApiBundle\Controller;

use Acme\ReactorApiBundle\Form\Type\PhotoFormType;
use Composer\DependencyResolver\RuleSet;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

class PhotoAdminController extends ContainerAware
{
    public function photoListAction($page)
    {
        $adminPool = $this->container->get('sonata.admin.pool');
        $form = $this->container->get('form.factory')->create(new PhotoFormType());
        $request = $this->container->get('request');

        $requestData = $request->request->get('reactr_photo_list');
        $from = $requestData['From'];
        $to = $requestData['To'];
        $dateFrom = '';
        $dateTo = '';
        if ($page == '1')
        {
            $pageFrom = 0;
            $pageTo = 15;
        }
        else
        {
            $pageFrom = (($page-1) * 15);
            $pageTo = $pageFrom + 15;
        }


        if ($from)
        {
            foreach($from as $key => $fr)
                if ($key != 'day')
                    $dateFrom.= $fr . '-';
                else
                    $dateFrom.= $fr;
            $dateFrom.= ' 00:00:00';
            $get_string = '?from='.$dateFrom;
            $from = new \DateTime($dateFrom);

        }
        else
        {
            if ($_GET)
            {
                $from = new \DateTime($_GET['from']);
                $get_string = '?from='.$_GET['from'];
            }
            else
            {
                $from = new \DateTime('2013-09-01 00:0:00');
                $get_string = '?from=2013-09-01 00:0:00';
            }
        }
        if ($to)
        {
            foreach($to as $key => $fr)
                if ($key != 'day')
                    $dateTo.= $fr . '-';
                else
                    $dateTo.= $fr;
            $dateTo.= ' 23:59:59';
            $get_string.= '&to='.$dateTo;
            $to = new \DateTime($dateTo);
        }
        else
        {
            if ($_GET)
            {
                $to = new \DateTime($_GET['to']);
                $get_string.= '&to='.$_GET['to'];
            }
            else
            {
                $to = new \DateTime('+ 1 day');
                $get_string.= '&to='.$to->format('Y-m-d H:m:s');
            }

        }
        /*        else
                {
                    $from = new \DateTime($_GET['from']);

                }*/


        $users = $this->container->get('doctrine')->getEntityManager()
            ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDateFromTo($from, $to);

        $countUsers = $this->container->get('doctrine')->getEntityManager()
            ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDate($from, $to);

        $pages = count($countUsers)/15;
        $pages = intval($pages);
        $countPages = array();
        if ($pages!=1)
            for ($i=1;$i<=$pages;$i++)
                $countPages[] = $i;
        $photos = $this->container->get('doctrine')->getEntityManager()
            ->getRepository('Acme\ReactorApiBundle\Entity\Message')->getAllPhotoInDate($from, $to);
        $reactionPhotos = $this->container->get('doctrine')->getEntityManager()
            ->getRepository('Acme\ReactorApiBundle\Entity\Message')->getAllReactionPhotoInDate($from, $to);

        $listPhotos = array();

        foreach($users as $key => $user)
        {
            if ($key>=$pageFrom && $key<$pageTo)
            {
                $listPhotos[$key]['phone'] = $user->getPhone();
                $listPhotos[$key]['email'] = $user->getEmail();
                $listPhotos[$key]['username'] = $user->getUsername();
                $listPhotos[$key]['sent'] = $user->sentMessagesNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
                $listPhotos[$key]['received'] = $user->receivedMessagesNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
                $listPhotos[$key]['sentR'] = $user->sentReactionPhotoNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
                $listPhotos[$key]['receivedR'] = $user->receivedReactionPhotoNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
            }
        }

        return $this->container->get('templating')->renderResponse(
            'AcmeReactorApiBundle:Admin:list.html.twig',
            array(
                'admin_pool' => $adminPool,
                'users'      => $listPhotos,
                'form' 		 => $form->createView(),
                'photos'     => $photos,
                'reaction'   => $reactionPhotos,
                'from'       => $from->format('Y-m-d'),
                'to'         => $to->format('Y-m-d'),
                'count_user' => count($countUsers),
                'pages'      => $countPages,
                'curr_page'  => $page,
                'get_string' => $get_string
            )
        );
    }
}