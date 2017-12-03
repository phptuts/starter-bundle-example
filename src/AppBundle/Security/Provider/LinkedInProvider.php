<?php

namespace AppBundle\Security\Provider;


use AppBundle\Client\LinkedInClient;
use AppBundle\Entity\User;
use AppBundle\Service\UserService;
use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Model\User\OAuthUser;
use StarterKit\StartBundle\Security\Provider\CustomProviderTrait;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LinkedInProvider implements UserProviderInterface
{
    use CustomProviderTrait;

    /**
     * @var LinkedInClient
     */
    private $client;

    public function __construct(UserServiceInterface $userService, LinkedInClient $client)
    {
        $this->client = $client;
        $this->userService = $userService;
    }

    /**
     * @param string $username
     * @return mixed
     */
    public function loadUserByUsername($username)
    {
        $githubUserModel = $this->client->getUserFromOAuthCode($username);

        if (!$githubUserModel->isValid()) {
            throw new UsernameNotFoundException('No access token found.');
        }

        $user = $this->userService->findBySlackUserId($githubUserModel->getUserId());

        if (!empty($user)) {
            return $user;
        }
        /** @var User $user */
        $user = $this->userService->findUserByEmail($githubUserModel->getEmail());

        if (!empty($user)) {
            $user->setLinkedInUserId($githubUserModel->getUserId());

            return $user;
        }

        return $this->registerUser($githubUserModel);

    }

    /**
     * We register the user with their google id and email.
     *
     * @param OAuthUser $githubUser
     * @return User|BaseUser
     */
    protected function registerUser(OAuthUser $githubUser)
    {
        $user = new User();
        $user->setEmail($githubUser->getEmail())
            ->setSlackUserId($githubUser->getUserId())
            ->setPlainPassword(base64_encode(random_bytes(20)));

        return $this->userService->registerUser($user, UserService::SOURCE_TYPE_SLACK);
    }


}