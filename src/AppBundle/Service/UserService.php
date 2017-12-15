<?php

namespace AppBundle\Service;


use AppBundle\Repository\UserRepository;

class UserService extends \StarterKit\StartBundle\Service\UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function findByAuthToken($token)
    {
        return $this->userRepository->findByAuthToken($token);
    }

}