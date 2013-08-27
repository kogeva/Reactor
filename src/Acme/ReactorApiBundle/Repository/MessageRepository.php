<?php

namespace Acme\ReactorApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    public function findAllByUserId($id)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT ms.id, ms.from_user, ms.to_user, ms.text, ms.photo, ms.reaction_photo, ms.is_read, ms.created_at, u.username
                FROM AcmeReactorApiBundle:Message ms
                LEFT JOIN ms.from u
                WHERE ms.to_user = :id
                  OR ms.from_user = :id
                ORDER BY ms.created_at DESC'
            )->setParameter('id', $id)->getArrayResult();
    }

    public function findByIdAndToUser($id, $toUser)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT ms
                FROM AcmeReactorApiBundle:Message ms
                WHERE ms.to_user = :to_user
                  AND ms.id = :id
                ORDER BY ms.created_at DESC'
            )->setParameters(array('id' => $id, 'to_user' => $toUser))->getOneOrNullResult();
    }
}
