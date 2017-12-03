<?php

namespace AppBundle\Repository;


use AppBundle\Entity\User;

class UserRepository extends \StarterKit\StartBundle\Repository\UserRepository
{
    /**
     * Returns the user with the github
     *
     * @param $githubUserId
     * @return null|object|User
     */
    public function findByLinkedInUserId($githubUserId)
    {
        return $this->findOneBy(['linkedInUserId' => $githubUserId]);
    }
}