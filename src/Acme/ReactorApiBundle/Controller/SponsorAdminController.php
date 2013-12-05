<?php
namespace Acme\ReactorApiBundle\Controller;

use Acme\ReactorApiBundle\Form\Type\SponsorLogoFormType;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ReactorApiBundle\Entity\Sponsor;

class SponsorAdminController extends Controller
{
    public function changeAction($id)
    {
        $adminPool = $this->container->get('sonata.admin.pool');

        $request = $this->container->get('request');

        $error = false;
        $success = false;


        $sponsor = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Sponsor')->find($id);

        if($request->getMethod() == 'POST') {

            $request = $this->container->get('request');
            $requestData = $request->request->get('reactr_sponsor_logo_form_type');
            $form = $this->container->get('form.factory')->create(new SponsorLogoFormType(),
                array(
                    'name' => $requestData['name'],
                    'site_url' => $requestData['site_url']));
            $files = $request->files->get('reactr_sponsor_logo_form_type');

            $name = $requestData['name'];
            $site = $requestData['site_url'];

            $logo = $files['logo'];

            if($logo) {
                $result = true;

                if($result) {
                    $size = getimagesize($logo);

                    if ($size[0] > 380 or $size[1] > 300)
                    {
                        if( $size['mime'] == 'image/jpeg' ) {
                            $image = imagecreatefromjpeg($logo);
                        } elseif( $size['mime'] == 'image/gif' ) {
                            $image = imagecreatefromgif($logo);
                        } elseif( $size['mime'] == 'image/png' ) {
                            $image = imagecreatefrompng($logo);
                        }

                        $x = imagesx($image);
                        $y = imagesy($image);
                        $ratio = 380 / $x;
                        $height = $y * $ratio;

                        $ratio = 300 / $y;
                        $width = $x * $ratio;

                        $new_image = imagecreatetruecolor(380, 300);
                        imagecopyresampled($new_image, $image, 0, 0, 0, 0, 380, 300, $x, $y);
                        $image = $new_image;

                        if( $size['mime'] == 'image/jpeg' ) {
                            $result = imagejpeg($image,$this->get('kernel')->getRootDir(). '/../web/images/'.$logo->getClientOriginalName());
                        } elseif( $size['mime'] == 'image/gif' ) {
                            $result = imagegif($image,$this->get('kernel')->getRootDir(). '/../web/images/'.$logo->getClientOriginalName());
                        } elseif( $size['mime'] == 'image/png' ) {
                            $result = imagepng($image,$this->get('kernel')->getRootDir(). '/../web/images/'.$logo->getClientOriginalName());
                        }

                    }
                    else
                        $result= $logo->move($this->get('kernel')->getRootDir(). '/../web/images', $logo->getClientOriginalName());
                    $sponsor->setName($name);
                    $sponsor->setSiteUrl($site);
                    $sponsor->setLogoUrl('http://'.$this->getRequest()->getHost().'/images/'.$logo->getClientOriginalName());

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($sponsor);
                    $em->flush();
                }

            } else {
                $error = 'Please, choose image to upload';
            }
        }else
        {
            $form = $this->container->get('form.factory')->create(new SponsorLogoFormType(),
                array(
                    'name' => $sponsor->getName(),
                    'site_url' => $sponsor->getSiteUrl()));
        }

        return $this->container->get('templating')->renderResponse(
            'AcmeReactorApiBundle:Admin:sponsor.html.twig',
            array(
                'admin_pool' 					=> $adminPool,
                'form' 		 					=> $form->createView(),
                'error'							=> $error,
                'success'    					=> $success,
                'id'                            => $id,
                'url'                           => $sponsor->getLogoUrl()
            )
        );
    }

    public function addNewSponsorAction()
    {
        $adminPool = $this->container->get('sonata.admin.pool');

        $request = $this->container->get('request');

        $error = false;
        $success = false;

        $form = $this->container->get('form.factory')->create(new SponsorLogoFormType());

        $logo_url = null;
        if($request->getMethod() == 'POST') {

            $request = $this->container->get('request');
            $requestData = $request->request->get('reactr_sponsor_logo_form_type');
            $files = $request->files->get('reactr_sponsor_logo_form_type');

            $name = $requestData['name'];
            $site = $requestData['site_url'];
            $logo = $files['logo'];

            $sponsor = new Sponsor();

            if($logo) {
                $result = true;

                if($result) {

                    $size = getimagesize($logo);

                    if ($size[0] > 380 or $size[1] > 300)
                    {
                        if( $size['mime'] == 'image/jpeg' ) {
                            $image = imagecreatefromjpeg($logo);
                        } elseif( $size['mime'] == 'image/gif' ) {
                            $image = imagecreatefromgif($logo);
                        } elseif( $size['mime'] == 'image/png' ) {
                            $image = imagecreatefrompng($logo);
                        }

                        $x = imagesx($image);
                        $y = imagesy($image);
                        $ratio = 380 / $x;
                        $height = $y * $ratio;

                        $ratio = 300 / $y;
                        $width = $x * $ratio;

                        $new_image = imagecreatetruecolor(380, 300);
                        imagecopyresampled($new_image, $image, 0, 0, 0, 0, 380, 300, $x, $y);
                        $image = $new_image;

                        if( $size['mime'] == 'image/jpeg' ) {
                            $result = imagejpeg($image,$this->get('kernel')->getRootDir(). '/../web/images/'.$logo->getClientOriginalName());
                        } elseif( $size['mime'] == 'image/gif' ) {
                            $result = imagegif($image,$this->get('kernel')->getRootDir(). '/../web/images/'.$logo->getClientOriginalName());
                        } elseif( $size['mime'] == 'image/png' ) {
                            $result = imagepng($image,$this->get('kernel')->getRootDir(). '/../web/images/'.$logo->getClientOriginalName());
                        }

                    }
                    else
                        $result= $logo->move($this->get('kernel')->getRootDir(). '/../web/images', $logo->getClientOriginalName());

                    $sponsor->setName($name);
                    $sponsor->setSiteUrl($site);
                    $sponsor->setLogoUrl('http://'.$this->getRequest()->getHost().'/images/'.$logo->getClientOriginalName());
                    $sponsor->setSelected(false);
                    $logo_url = $sponsor->getLogoUrl();
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($sponsor);
                    $em->flush();
                }

            } else {
                $error = 'Please, choose image to upload';
            }
        }

        return $this->container->get('templating')->renderResponse(
            'AcmeReactorApiBundle:Admin:new_sponsor.html.twig',
            array(
                'admin_pool' 					=> $adminPool,
                'form' 		 					=> $form->createView(),
                'error'							=> $error,
                'success'    					=> $success,
                'url'                           => $logo_url
            )
        );
    }

    public function selectSponsorAction($id)
    {
        if($this->container->get('request')->isXMLHttpRequest()) {
            $request = $this->container->get('request')->attributes;
            $id = $request->get('id');

            $em = $this->container->get('doctrine')->getEntityManager();
            $sponsor = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Sponsor')->find($id);
            $sponsors = $this->getDoctrine()->getRepository('AcmeReactorApiBundle:Sponsor')->findAll();

            foreach($sponsors as $sp)
            {
                if($sp->getId() != $id)
                {
                    $sp->setSelected(false);
                    $em->persist($sp);
                }
            }

            if (!$sponsor) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
            }

            $enabled = $sponsor->getSelected();
            $sponsor->setSelected(!$enabled);

            $em->persist($sponsor);
            $em->flush();

            $response = $sponsor->getSelected() ? 'Selected' : 'Not selected';

            return new Response($response);
        } else throw new NotFoundHttpException('Route not found');
    }
}