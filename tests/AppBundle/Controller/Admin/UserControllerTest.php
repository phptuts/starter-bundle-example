<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\Admin\UserController;
use AppBundle\Entity\User;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Event\UserEvent;
use StarterKit\StartBundle\Service\UserService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Tests\AppBundle\BaseTestCase;

class UserControllerTest extends BaseTestCase
{
    /**
     * @var UserController
     */
    protected $userController;

    /**
     * @var EventDispatcherInterface|Mock
     */
    protected $eventDispatcher;

    /**
     * @var UserPasswordEncoderInterface|Mock
     */
    protected $userPasswordEncoder;

    public function setUp()
    {
        parent::setUp();
        $this->eventDispatcher = \Mockery::mock(EventDispatcherInterface::class);
        $this->userPasswordEncoder = \Mockery::mock(UserPasswordEncoderInterface::class);
        $this->userController = new UserController($this->userPasswordEncoder, $this->eventDispatcher);
    }

    public function testPrePersist()
    {
        $user = new User();
        $user->setPlainPassword('moomoo');

        $this->userPasswordEncoder
                ->shouldReceive('encodePassword')
                ->with($user, 'moomoo')
                ->andReturn('hashed_moo');

        $this->eventDispatcher
                ->shouldReceive('dispatch')
                ->with(UserService::REGISTER_EVENT, \Mockery::type(UserEvent::class))
                ->once();

        $this->userController->prePersistEntity($user);

        Assert::assertEquals(UserService::SOURCE_TYPE_ADMIN, $user->getSource());
        Assert::assertEquals('hashed_moo', $user->getPassword());
    }

    public function testUpdatingUserWithNewPassword()
    {
        $user = new User();
        $user->setPlainPassword('moomoo');

        $this->userPasswordEncoder
            ->shouldReceive('encodePassword')
            ->with($user, 'moomoo')
            ->andReturn('hashed_moo');

        $this->userController->preUpdateEntity($user);

        Assert::assertEquals('hashed_moo', $user->getPassword());
    }

    public function testUpdatingUserWithOutNewPassword()
    {
        $user = new User();
        $user->setPassword('not_touched');

        $this->userPasswordEncoder
            ->shouldReceive('encodePassword')
            ->withAnyArgs()
            ->andReturn('hashed_moo');

        $this->userController->preUpdateEntity($user);
        Assert::assertEquals('not_touched', $user->getPassword());
    }
}