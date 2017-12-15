<?php


namespace Tests\AppBundle\Controller;


use AppBundle\Service\DatabaseTokenService;
use AppBundle\Service\UserService;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Tests\Controller\BaseApiTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;

class AuthTest extends BaseApiTestCase
{
    /**
     * @var DatabaseTokenService
     */
    protected $dbAuthTokenService;

    public function setUp()
    {
        parent::setUp();
        $this->environment = 'test';
        $userService = new UserService(
            $this->getContainer()->get('doctrine.orm.entity_manager'),
            $this->getContainer()->get('security.encoder_factory'),
            $this->getContainer()->get('event_dispatcher'),
            $this->getContainer()->getParameter('starter_kit_start.refresh_token_ttl'),
            $this->getContainer()->getParameter('starter_kit_start.user_class')
        );

        $this->dbAuthTokenService = new DatabaseTokenService($userService, $this->getContainer()->getParameter('app.jws_ttl'));

    }

    public function testRegister()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/api/users',
            ['email' => 'test_register_user_1@google.com', 'plainPassword' => 'password']
        );

        $json = $this->getJsonResponse($response);

        Assert::assertNotEmpty($json['meta']);
        Assert::assertEquals('authentication', $json['meta']['type']);

        $user = $this->dbAuthTokenService->getUser($json['data']['tokenModel']['token']);
        Assert::assertEquals('test_register_user_1@google.com', $user->getEmail());
        Assert::assertTrue((new \DateTime())->getTimestamp() < $json['data']['tokenModel']['expirationTimeStamp']);

        return $json['data']['tokenModel'];
    }

    /**
     * @param $authModelArray
     * @depends testRegister
     */
    public function testStateCookieLogin($authModelArray)
    {
        $client = $this->makeClient();

        $client->getCookieJar()->set(new Cookie(
                AuthResponseService::AUTH_COOKIE,
                $authModelArray['token'],
                $authModelArray['expirationTimeStamp']
            )
        );

        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
    }

    /**
     * @param $authModelArray
     * @depends testRegister
     */
    public function testLoginWithUserCreateEmail($authModelArray)
    {
        $client = $this->makeClient();

        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => 'test_register_user_1@google.com', 'password' => 'password']
        );

        $json = json_decode($response->getContent(), true);

        Assert::assertNotEmpty($json['meta']);
        Assert::assertEquals('authentication', $json['meta']['type']);

        $user = $this->dbAuthTokenService->getUser($json['data']['tokenModel']['token']);
        Assert::assertEquals('test_register_user_1@google.com', $user->getEmail());
        Assert::assertTrue((new \DateTime())->getTimestamp() < $json['data']['tokenModel']['expirationTimeStamp']);

        // Asserting that the auth token did not chance.
        Assert::assertEquals($authModelArray['token'], $json['data']['tokenModel']['token']);
    }

}