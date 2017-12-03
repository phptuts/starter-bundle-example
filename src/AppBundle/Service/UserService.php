<?php

namespace AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;

class UserService extends \StarterKit\StartBundle\Service\UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Returns the user with linked in user id or null
     *
     * @param $githubUserId
     * @return User|null
     */
    public function getLinkedInUserId($githubUserId)
    {
        return $this->userRepository->findByLinkedInUserId($githubUserId);
    }
}