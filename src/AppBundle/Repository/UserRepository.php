<?php
namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use StarterKit\StartBundle\Exception\ProgrammerException;

/**
 * Class UserRepository
 * @package AppBundle\Repository
 */
class UserRepository extends \StarterKit\StartBundle\Repository\UserRepository
{
    /**
     * Finds a user by the token
     *
     * @param $token
     * @return null|object|User
     * @throws ProgrammerException
     */
    public function findByAuthToken($token)
    {
        try {
            $builder = $this->createQueryBuilder('u');
            return  $builder->where($builder->expr()->eq('u.authToken', ':token'))
                ->andWhere('u.authTokenExpire > :today')
                ->setParameter('today', new \DateTime())
                ->setParameter('token', $token)
                ->getQuery()
                ->getSingleResult();

        }
        catch (NoResultException $ex) {
            return null;
        }

        catch (NonUniqueResultException $ex) {
            throw new ProgrammerException('Not Unique Token!!!');
        }
    }
}