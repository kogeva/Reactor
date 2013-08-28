<?php

namespace Acme\ReactorApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function getFriends($id)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u.id, u.username, u.phone
                FROM AcmeReactorApiBundle:User u
                LEFT JOIN u.friends fr
                WHERE fr.user_id = :id'
            )->setParameter('id', $id)->getArrayResult();
    }

    public function findByUserName($username)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u.id, u.username, u.phone
                FROM AcmeReactorApiBundle:User u
                WHERE u.username LIKE :username'
            )->setParameter('username', '%'.$username.'%')->getArrayResult();
    }

    public function findUserByPhone($phone)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u.id
                FROM AcmeReactorApiBundle:User u
                WHERE u.phone = :phone'
            )->setParameter('phone', $phone)->getArrayResult();
    }

    public function findByUsernameAndEmail($username, $email)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u.username, u.email
                FROM AcmeReactorApiBundle:User u
                WHERE u.username = :username
                  OR u.email = :email'
            )->setParameters(array('username' => $username, 'email' => $email))->getArrayResult();
    }
}