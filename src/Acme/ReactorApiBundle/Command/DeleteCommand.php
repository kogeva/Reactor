<?php

namespace Acme\ReactorApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


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
      $em->getRepository('AcmeReactorApiBundle:Message')->deleteOldMessages();
    }
}