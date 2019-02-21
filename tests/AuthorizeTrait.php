<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait AuthorizeTrait
 */
trait AuthorizeTrait
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getClient(): Client
    {
        if (null === $this->client) {
            $this->client = static::createClient();
        }

        return $this->client;
    }

    /**
     * @param $username
     * @param $password
     */
    protected function register($username, $password)
    {
        $client = $this->getClient();
        $credentials = [
            'username' => $username,
            'password' => $password
        ];
        $client->request(
            'POST',
            '/api/register',
            $credentials,
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $expectedContent = ['username' => $username];
        $this->assertEquals($expectedContent, $content);
    }

    /**
     * @param $username
     * @param $password
     * @return string
     */
    protected function login($username, $password): string
    {
        $client = $this->getClient();
        $credentials = [
            'username' => $username,
            'password' => $password
        ];
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($credentials)
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $content);
        $this->assertNotEmpty($content['token']);

        return $content['token'];
    }
}