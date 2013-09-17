<?php


namespace Acme\ReactorApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class StaticInfoRepository extends EntityRepository
{
    public function getStaticInfo()
    {
       return $this->getEntityManager()
            ->createQuery('SELECT info FROM AcmeReactorApiBundle:StaticInfo info')->getSingleResult();
    }
}