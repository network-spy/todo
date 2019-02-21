<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    use AuthorizeTrait;

    public function testApiAccess()
    {
        $username = uniqid('testUser');
        $password = 'testPassword';
        $this->register($username, $password);
        $accessToken = $this->login($username, $password);
        $headers = [
            'HTTP_Authorization' => "Bearer {$accessToken}",
            'CONTENT_TYPE' => 'application/json'
        ];
        $this->getClient()->request(
            'GET',
            '/api',
            [],
            [],
            $headers
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $expectedContent = ['username' => $username];
        $this->assertEquals($expectedContent, $content);
    }
}
