<?php

namespace AppBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Service\UserService;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class AdminController
 * @package AppBundle\Controller\Admin
 */
class AdminController extends BaseAdminController
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * AdminController constructor.
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Runs before saving a user for the first time
     *
     * @param object $entity
     */
    public function prePersistEntity($entity)
    {
        if ($entity instanceof BaseUser) {
            $entity = $this->encodePasswordForUser($entity);
            $entity->setEnabled(true)
                ->setSource(UserService::SOURCE_TYPE_ADMIN)
                ->setRoles(['ROLE_USER']);
        }

        parent::prePersistEntity($entity);
    }

    /**
     * Runs before updating a user
     *
     * @param object $entity
     */
    public function preUpdateEntity($entity)
    {
        if ($entity instanceof BaseUser && !empty($entity->getPlainPassword())) {
            $entity = $this->encodePasswordForUser($entity);
        }

        parent::preUpdateEntity($entity);
    }

    /**
     * Returns a user with the encoded password
     *
     * @param BaseUser $user
     * @return BaseUser
     */
    private function encodePasswordForUser(BaseUser $user)
    {
        $user->setPlainPassword(base64_encode(random_bytes(20)));
        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));

        return $user;
    }
}