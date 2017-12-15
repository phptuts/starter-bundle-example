<?php

namespace AppBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Event\UserEvent;
use StarterKit\StartBundle\Service\UserService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminController
 * @package AppBundle\Controller\Admin
 */
class UserController extends BaseAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * AdminController constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, EventDispatcherInterface $dispatcher)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Runs before saving a user for the first time
     *
     * @param BaseUser $user
     */
    public function prePersistEntity($user)
    {
        $user->setEnabled(true)
            ->setSource(UserService::SOURCE_TYPE_ADMIN)
            ->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword()));

        $this->dispatcher->dispatch(UserService::REGISTER_EVENT, new UserEvent($user));

        parent::prePersistEntity($user);
    }

    /**
     * Runs before updating a user
     *
     * @param BaseUser $user
     */
    public function preUpdateEntity($user)
    {
        if (!empty($user->getPlainPassword())) {
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword()));
        }

        parent::preUpdateEntity($user);
    }
}