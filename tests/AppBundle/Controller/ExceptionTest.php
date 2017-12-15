<?php

namespace Tests\AppBundle\Controller;


use PHPUnit\Framework\Assert;
use Tests\AppBundle\BaseTestCase;

class ExceptionTest extends BaseTestCase
{
    public function testApiException()
    {
        $client = $this->makeClient();
        $client->request('GET', '/exception', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ]);

        $response = $client->getResponse();

        $json = json_decode($response->getContent(), true);

        Assert::assertEquals($json['data']['message'], 'Silly Exception');
        $meta = $json['meta'];
        Assert::assertArrayHasKey('exceptionCode',$meta);
        Assert::assertArrayHasKey('type',$meta);
        Assert::assertArrayHasKey('lookupCode',$meta);
        Assert::assertArrayHasKey('instance',$meta);
    }

    public function testWebsiteException()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/exception');

        $message = $crawler->filter('h1')->first()->text();

        // Asserting that error number is display on the page
        Assert::assertRegExp("/Hey can you report the error number: [0-9]+\-[0-9]+ below to/", $message);
    }
}