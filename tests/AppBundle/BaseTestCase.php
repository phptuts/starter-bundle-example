<?php

namespace Tests\AppBundle;


use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    const ENVIRONMENT = 'test';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $environment = self::ENVIRONMENT;

    /**
     *
     */
    public function setUp()
    {
        $this->client = static::createClient(['environment' => $this->environment]);
        parent::setUp();
    }

    /**
     * @return Client
     */
    public function makeClient()
    {
        return $this->client;
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->client->getContainer();
    }


    /**
     * Asserts the response's status code
     *
     * @param $statusCode
     * @param Client $client
     */
    public function assertStatusCode($statusCode, Client $client)
    {
        $response = $client->getResponse();
        Assert::assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * @link https://github.com/mockery/mockery/issues/376
     */
    public function tearDown()
    {
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
            \Mockery::close();
        }
    }

    public function setObjectId(&$object, $id)
    {
        $refObject   = new \ReflectionObject( $object );
        $refProperty = $refObject->getProperty( 'id' );
        $refProperty->setAccessible( true );
        $refProperty->setValue($object, $id);
        $refProperty->setAccessible(false);
    }
}