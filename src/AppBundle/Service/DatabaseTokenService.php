<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Exception\ProgrammerException;
use StarterKit\StartBundle\Model\Auth\AuthTokenModel;
use StarterKit\StartBundle\Service\AuthTokenServiceInterface;

/**
 * Class DatabaseTokenService
 * @package AppBundle\Service
 */
class DatabaseTokenService implements AuthTokenServiceInterface
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var integer The time to live for the token
     */
    private $ttl;

    /**
     * DatabaseTokenService constructor.
     * @param UserService $userService
     * @param integer $ttl
     */
    public function __construct(UserService $userService, $ttl)
    {
        $this->userService = $userService;
        $this->ttl = $ttl;
    }

    /**
     * Creates a jws token model
     *
     * @param User|BaseUser $user
     * @return AuthTokenModel
     */
    public function createAuthTokenModel(BaseUser $user)
    {
        if (!empty($user->getAuthToken()) && $user->getAuthTokenExpire() > new \DateTime()) {
            return new AuthTokenModel($user->getAuthToken(), $user->getAuthTokenExpire()->getTimestamp());
        }

        $expirationDate = new \DateTime();
        $expirationDate->modify('+' . $this->ttl . ' seconds');
        $user->setAuthToken(bin2hex(random_bytes(50)))
            ->setAuthTokenExpire($expirationDate);

        $this->userService->save($user);

        return new AuthTokenModel($user->getAuthToken(), $expirationDate->getTimestamp());

    }

    /**
     * This will always return true because the "validation" happens when we look the token up in the database
     *
     * @param string $token
     * @return bool
     */
    public function isValid($token)
    {
        return true;
    }

    /**
     * Returns the user's id from the token
     *
     * @param $token
     * @return BaseUser
     * @throws ProgrammerException
     */
    public function getUser($token)
    {
        $user = $this->userService->findByAuthToken($token);

        if (empty($user)) {
            throw new ProgrammerException("Token not found in the database.");
        }

        return $user;
    }

}