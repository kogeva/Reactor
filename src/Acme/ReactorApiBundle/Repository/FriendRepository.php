<?php

namespace Acme\ReactorApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FriendRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FriendRepository extends EntityRepository
{
    public function getFriendsArray($id)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT  u.id, u.username, u.phone, u.privacy_message
                FROM AcmeReactorApiBundle:Friend fr
                JOIN fr.user u
                WHERE fr.user_id = :id'
            )->setParameter('id', $id)->getArrayResult();
    }

    public function getIdAndBlockedFriends($id)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT  fr.friend_id, fr.blocked
                FROM AcmeReactorApiBundle:Friend fr
                WHERE fr.user_id = :id'
            )->setParameter('id', $id)->getArrayResult();
    }

    public function getIdFriendsWhoAddMe($id)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT  u.id, u.username, u.phone , u.privacy_message, fr.user_id
                FROM AcmeReactorApiBundle:Friend fr
                JOIN fr.friend u
                WHERE fr.friend_id = :id'
            )->setParameter('id', $id)->getArrayResult();
    }

    public function checkExistRelationFriends($userId, $friendId)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT  fr.friend_id
                FROM AcmeReactorApiBundle:Friend fr
                WHERE fr.user_id = :user_id AND fr.friend_id = :friend_id '
            )->setParameters(array('user_id' => $userId, 'friend_id' => $friendId))->getArrayResult();
    }
}