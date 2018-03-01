<?php

namespace AppBundle\Client;

use GuzzleHttp\Client;
use StarterKit\StartBundle\Model\User\OAuthUser;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class LinkedInClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $clientId;

    public function __construct(Client $client, $secret, $clientId)
    {
        $this->client = $client;
        $this->secret = $secret;
        $this->clientId = $clientId;
    }

    /**
     * Returns oauth user
     *
     * @param string $code
     * @return OAuthUser
     */
    public function getUserFromOAuthCode($code)
    {
        return $this->getUser($this->getAccessToken($code));
    }

    private function getAccessToken($code)
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'https://e177ac55.ngrok.io/oauth/linkedin'
        ];

        $request = $this->client->request('POST', 'https://www.linkedin.com/oauth/v2/accessToken', [
            'form_params' => $params,
        ]);



        $data = json_decode($request->getBody()->getContents(), true);

        if (empty($data['access_token'])) {
            throw new UsernameNotFoundException('Error with access token');
        }

        return $data['access_token'];
    }

    private function getUser($token)
    {

        $request = $this->client->request('GET', 'https://api.linkedin.com/v1/people/~:(id,email-address)?format=json', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
        ]);

        $data = json_decode($request->getBody()->getContents(), true);

        return new OAuthUser(
            !empty($data['id']) ? $data['id'] : null,
            !empty($data['emailAddress']) ? $data['emailAddress'] : null
        );
    }



}