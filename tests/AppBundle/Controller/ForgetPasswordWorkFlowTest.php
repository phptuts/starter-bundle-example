<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Repository\UserRepository;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Tests\Controller\RequestTrait;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\BaseTestCase;

class ForgetPasswordWorkFlowTest extends BaseTestCase
{

    use RequestTrait;

    const EXAMPLE_USER_EMAIL = 'example_user@gmail.com';

    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->getContainer()->get('doctrine')->getRepository(User::class);
    }

    /**
     * This tests that a user can register
     */
    public function testRegisterPage()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/register');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Register')->form();
        $crawler = $client->submit($form);

        $this->assertStatusCode(200, $client);
        Assert::assertEquals(2, $crawler->filter('.has-error')->count());

        $form = $crawler->selectButton('Register')->form();
        $form->setValues(['register[email]' => self::EXAMPLE_USER_EMAIL, 'register[plainPassword]' => 'password']);
        $client->submit($form);

        $this->assertStatusCode(302, $client);
    }


    /**
     * 1) Tests that email address is required
     * 2) Tests that if an email is not in our database an error is shown
     * 3) Tests that if the email exists that the user is redirected
     * 4) Tests the redirect page
     * @depends testRegisterPage
     */
    public function testForgetPassword()
    {
        // Navigating to forget password page
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/forget-password');
        $this->assertStatusCode(200, $client);

        // Submitting form and testing the error show up
        $form = $crawler->selectButton('Forget Password')->form();
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        Assert::assertEquals(1, $crawler->filter('.has-error')->count());

        // Submitting form with email that does not exist in our system and testing that the form shows an error
        $form = $crawler->selectButton('Forget Password')->form();
        $form->setValues(['forget_password[email]' => 'email_does_not@exists.com']);
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        // Because this attaches to the form in a weird way we have to to look at the css and make sure that it validates
        Assert::assertEquals(1, $crawler->filter('.has-error')->count());

        // Testing form submit with a valid email address
        $form = $crawler->selectButton('Forget Password')->form();
        $form->setValues(['forget_password[email]' => self::EXAMPLE_USER_EMAIL]);
        $client->submit($form);
        $this->assertStatusCode(302, $client);

        // Testing after reset page works
        $client->request('GET','/forget-password-success');
        $this->assertStatusCode(200, $client);

    }

    /**
     * 1) Test what happens if a bad token is entered
     * 2) Tests a valid token can access the reset password form
     * 3) Tests if a password that is too short is enter that validation appers
     * 4) Tests that if a valid password is enter the user is redirected
     * @depends testForgetPassword
     */
    public function testResetPassword()
    {
        $client = $this->makeClient();
        // Asserting that the form does not exist for bad tokens
        $crawler = $client->request('GET', '/reset-password/bad_token');
        $this->assertStatusCode(200, $client);
        Assert::assertEquals(0,$crawler->selectButton('Reset Password')->count());


        $user = $this->userRepository->findByEmail(self::EXAMPLE_USER_EMAIL);

        // Going to the forget password page to
        $crawler = $client->request('GET', '/reset-password/' . twig_urlencode_filter($user->getForgetPasswordToken()));
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Reset Password')->form();

        // Submitting password that should be too short and testing validation
        $form->setValues(['reset_password[plainPassword]' => 'sd']);
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        Assert::assertEquals(1, $crawler->filter('.has-error')->count());

        // Submitting valid password
        $form->setValues(['reset_password[plainPassword]' => 'new_password']);
        $client->submit($form);
        $this->assertStatusCode(302, $client);

        // Testing that the redirect page works
        $client->request('GET', '/reset-password-success');
        $this->assertStatusCode(200, $client);

    }

    /**
     * Test that a user can login with the new password and can access a secure area.
     * @depends testResetPassword
     */
    public function testLoginAfterResetPassword()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => 'update_profile@gmail.com', 'password' => 'password']
        );

        $json = json_decode($response->getContent(), true);

        $client->getCookieJar()->set(new Cookie(
                AuthResponseService::AUTH_COOKIE,
                $json['data']['tokenModel']['token'],
                $json['data']['tokenModel']['expirationTimeStamp']
            )
        );


        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);


        // This is testing that the user can login to a secure area which is account settings
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
    }
}