<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    use AuthorizeTrait;

    public function testApiAccess()
    {
        $client = $this->getClient();
        $username = uniqid('testUser');
        $password = 'testPassword';
        $this->register($username, $password);
        $accessToken = $this->login($username, $password);
        $headers = [
            'HTTP_Authorization' => "Bearer {$accessToken}",
            'CONTENT_TYPE' => 'application/json'
        ];
        $client->request(
            'GET',
            '/api',
            [],
            [],
            $headers
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $expectedContent = ['username' => $username];
        $this->assertEquals($expectedContent, $content);
    }
}
