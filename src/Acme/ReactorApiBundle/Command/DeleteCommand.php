<?php

namespace Acme\ReactorApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;


class DeleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('reactr:clean')
             ->setDescription('Deleting old messages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine');
        $photos = $em->getRepository('AcmeReactorApiBundle:Message')->deleteOldPhotos();
        $fs = new Filesystem();

        foreach($photos as $photo)
        {
            $photoPath = $photo->getPhoto();
            $reactionPhotoPath = $photo->getReactionPhoto();
            $reactionPhoto = explode('images',$photoPath);
            $photoName = explode('images',$photoPath);
            if (isset($photoName[1]))
            {
                $photoPath = $this->getContainer()->get('kernel')->getRootDir(). '/../web/images' . $photoName[1];
                $fs->remove($photoPath);
            }
            if (isset($reactionPhoto[1]))
            {
                $ReactionPhotoPath = $this->getContainer()->get('kernel')->getRootDir(). '/../web/images' . $reactionPhoto[1];
                $fs->remove($ReactionPhotoPath);
            }
        }
    }
}