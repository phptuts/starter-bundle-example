<?php

namespace Tests\AppBundle;


use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;

class BaseTestCase extends \StarterKit\StartBundle\Tests\BaseTestCase
{
    public function setUp()
    {
        $this->environment = 'test';
        parent::setUp();
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