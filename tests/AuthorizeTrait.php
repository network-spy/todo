<?php

namespace App\Tests;

use \Symfony\Bundle\FrameworkBundle\Client;
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
        $credentials = [
            'username' => $username,
            'password' => $password
        ];
        $this->getClient()->request(
            'POST',
            '/api/register',
            $credentials,
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $expectedContent = ['username' => $username];
        $this->assertEquals($expectedContent, $content);
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    protected function login($username, $password): string
    {
        $credentials = [
            'username' => $username,
            'password' => $password
        ];
        $this->getClient()->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($credentials)
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $content);
        $this->assertNotEmpty($content['token']);

        return $content['token'];
    }
}