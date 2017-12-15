<?php

namespace Tests\AppBundle\Controller;

use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Service\AuthResponseService;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\BaseTestCase;

class AccountSettingControllerTest extends BaseTestCase
{
    use RequestTrait;

    /**
     * Tests that account settings login is required
     */
    public function testAccountSettingAuthRequired()
    {
        $client = $this->makeClient();
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(302, $client);

        $client->request('GET', '/account-settings/change-password');
        $this->assertStatusCode(302, $client);
    }

    /**
     * What is commented out is done so that it will fun on travis ci.
     *
     * Test that a user can upload a file and change account setting
     */
    public function testAccountSettingEmailIsPopulated()
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


        $crawler = $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
        Assert::assertEquals('update_profile@gmail.com', $crawler->filter('#update_user_email')->first()->attr('value'));

        $form = $crawler->selectButton('Update')->form();

        $form['update_user[displayName]']->setValue('blue_man');
        $form['update_user[bio]']->setValue('this is about me');
        // $form['update_user[image]']->upload(__DIR__ .'/cat.png');
        $crawler = $client->submit($form);

        Assert::assertEquals(1, $crawler->filter('#success-flash-message')->count());
        Assert::assertEquals('update_profile@gmail.com', $crawler->filter('#update_user_email')->first()->attr('value'));
        Assert::assertEquals('this is about me', $crawler->filter('#update_user_bio')->first()->text());
        Assert::assertEquals('blue_man', $crawler->filter('#update_user_displayName')->first()->attr('value'));


        // IF EVER TURNED ON USE USER REPOSITORY INSTEAD
//          $user =  $this->getContainer()
//                            ->get('AppBundle\Repository\UserRepository')
//                           ->findUserByEmail('update_profile@gmail.com');
//
//         Assert::assertNotEmpty($user->getImageUrl());
    }

    /**
     * Tests that a user can change their password and login
     * @depends testAccountSettingEmailIsPopulated
     */
    public function testAccountSettingUpdatePassword()
    {
        // log user in
        $client = $this->makeClient();

        $response = $this->makeJsonRequest(
            $client,
            Request::METHOD_POST,
            '/login_check',
            ['email' => 'change_password@gmail.com', 'password' => 'password']
        );

        $json = json_decode($response->getContent(), true);

        $client->getCookieJar()->set(new Cookie(
            AuthResponseService::AUTH_COOKIE,
            $json['data']['tokenModel']['token'],
            $json['data']['tokenModel']['expirationTimeStamp']
            )
        );

        Assert::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        // Change the password and make sure the flash message appears
        $crawler = $client->request('GET', '/account-settings/change-password');
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Change Password')->form();
        $form->get('change_password[currentPassword]')->setValue('password');
        $form->get('change_password[newPassword]')->setValue('new_password');
        $crawler = $client->submit($form);
        Assert::assertEquals(1,$crawler->filter('#success-flash-message')->count());

        // logout user
        $client->request('GET', '/logout');

        // login in user
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form->setValues(['email' => 'change_password@gmail.com', 'password' => 'new_password']);
        $client->submit($form);

        // Go to protected page to make sure user is logged in
        $client->request('GET', '/account-settings/information');
        $this->assertStatusCode(200, $client);
    }
}