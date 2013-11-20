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
        $request = $this->container->get('request');

        $requestData = $request->request->get('reactr_photo_list');

        $from = $requestData['From'];
        $to = $requestData['To'];
        $search = '';
        $type = '';
        if (isset($requestData['type']))
        {
            $type = $requestData['type'];
            if (isset($requestData['search']))
            {
                $search = $requestData['search'];
            }
        }
        if (isset($_GET['field']) && isset($_GET['string']))
        {
            $type = $_GET['field'];
            $search = $_GET['string'];
        }
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
        $form = $this->container->get('form.factory')->create(new PhotoFormType(), array('from' => $from, 'to' => $to, 'search' => $search, 'field' => $type));


        if (isset($search) && $search && isset($type) && $type)
        {
            $get_string.= '&field='.$type.'&string='.$search;
            switch($type)
            {
                case 'Phone':
                    $users = $this->container->get('doctrine')->getEntityManager()
                        ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDatePhone($from, $to, $search);

                    $countUsers = $users;
                    break;
                case 'Email':
                    $users = $this->container->get('doctrine')->getEntityManager()
                        ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDateEmail($from, $to, $search);

                    $countUsers = $users;
                    break;
                case 'Username':
                    $users = $this->container->get('doctrine')->getEntityManager()
                        ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDateName($from, $to, $search);

                    $countUsers = $users;
                    break;
            }
        }
        else
        {
            $users = $this->container->get('doctrine')->getEntityManager()
                ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDateFromTo($from, $to, $pageFrom, $pageTo);

            $countUsers = $this->container->get('doctrine')->getEntityManager()
                ->getRepository('Acme\ReactorApiBundle\Entity\User')->findAllUsersInDate($from, $to);
        }


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
        $userss = array();
        foreach($users as $key => $user)
        {
            $userss[$key]['phone'] = $user->getPhone();
            $userss[$key]['email'] = $user->getEmail();
            $userss[$key]['username'] = $user->getUsername();
            $userss[$key]['sent'] = $user->sentMessagesNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
            $userss[$key]['received'] = $user->receivedMessagesNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
            $userss[$key]['sentR'] = $user->sentReactionPhotoNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
            $userss[$key]['receivedR'] = $user->receivedReactionPhotoNum($from->format('Y-m-d H:m:s'), $to->format('Y-m-d H:m:s'));
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
                'get_string' => $get_string,
                'xml_users'  => json_encode($userss)
            )
        );
    }

    public function getXmlTableAction($data)
    {
        $excelService = $this->container->get('xls.service_xls5');

        $excelService->excelObj->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Phone')
            ->setCellValue('B1', 'Email')
            ->setCellValue('C1', 'username')
            ->setCellValue('D1', 'sent')
            ->setCellValue('E1', 'received')
            ->setCellValue('F1', 'sentR')
            ->setCellValue('G1', 'receivedR');

        $data = json_decode($data);

        $a = 2;
        for($i=0;$i<count($data);$i++)
        {
            $a++;
            $d = get_object_vars($data[$i]);
            $excelService->excelObj->setActiveSheetIndex(0)
                ->setCellValue("A$a", $d['phone'])
                ->setCellValue("B$a", $d['email'])
                ->setCellValue("C$a", $d['username'])
                ->setCellValue("D$a", $d['sent'])
                ->setCellValue("E$a", $d['received'])
                ->setCellValue("F$a", $d['sentR'])
                ->setCellValue("G$a", $d['receivedR'])
            ;
        }

        $excelService->excelObj->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelService->excelObj->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=st1dream23.xls');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}