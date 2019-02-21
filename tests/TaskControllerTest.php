<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use AuthorizeTrait;

    /**
     * @var array
     */
    private $headers;

    protected function setUp()
    {
        parent::setUp();
        $username = uniqid('test_user');
        $password = 'testPassword';
        $this->register($username, $password);
        $accessToken = $this->login($username, $password);
        $this->headers = [
            'HTTP_Authorization' => "Bearer {$accessToken}",
            'CONTENT_TYPE' => 'application/json'
        ];
    }

    public function testPost()
    {
        $client = $this->getClient();
        $body = ['content' => 'some task'];
        $client->request(
            'POST',
            '/api/task',
            [],
            [],
            $this->headers,
            json_encode($body)
        );
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($responseContent['id']);
        $this->assertEquals($body['content'], $responseContent['content']);
        $this->assertEquals(false, $responseContent['completed']);

        return $responseContent;
    }

    /**
     * @depends testPost
     * @param $taskFixture
     * @return array
     */
    public function testPatch($taskFixture)
    {
        $client = $this->getClient();
        $body = ['completed' => true];
        $client->request(
            'PATCH',
            "/api/task/{$taskFixture['id']}",
            [],
            [],
            $this->headers,
            json_encode($body)
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($taskFixture['id'], $responseContent['id']);
        $this->assertEquals($taskFixture['content'], $responseContent['content']);
        $this->assertEquals($body['completed'], $responseContent['completed']);

        return $responseContent;
    }

    /**
     * @depends testPatch
     * @param $taskFixture
     * @return array
     */
    public function testPutExisted($taskFixture)
    {
        $client = $this->getClient();
        $body = [
            'content' => 'another task',
            'completed' => false,
        ];
        $client->request(
            'PUT',
            "/api/task/{$taskFixture['id']}",
            [],
            [],
            $this->headers,
            json_encode($body)
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($taskFixture['id'], $responseContent['id']);
        $this->assertEquals($body['content'], $responseContent['content']);
        $this->assertEquals($body['completed'], $responseContent['completed']);

        return $responseContent;
    }


    /**
     * @depends testPutExisted
     * @param $taskFixture
     * @return int
     */
    public function testGet($taskFixture)
    {
        $client = $this->getClient();
        $client->request(
            'GET',
            "/api/task/{$taskFixture['id']}",
            [],
            [],
            $this->headers
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($taskFixture['id'], $responseContent['id']);
        $this->assertEquals($taskFixture['content'], $responseContent['content']);
        $this->assertEquals($taskFixture['completed'], $responseContent['completed']);

        return $responseContent['id'];
    }

    /**
     * @depends testGet
     * @param $lastInsertedTaskId
     * @return mixed
     */
    public function testPutNew($lastInsertedTaskId)
    {
        $notExistedTaskId = $lastInsertedTaskId + 100500;
        $client = $this->getClient();
        $body = ['content' => 'one more task'];
        $client->request(
            'PUT',
            "/api/task/$notExistedTaskId",
            [],
            [],
            $this->headers,
            json_encode($body)
        );
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($responseContent['id']);
        $this->assertEquals($body['content'], $responseContent['content']);
        $this->assertEquals(false, $responseContent['completed']);

        return $responseContent;
    }

    /**
     * @depends testPutNew
     * @param $taskFixture
     */
    public function testGetAllAndFindLastCreated($taskFixture)
    {
        $client = $this->getClient();
        $client->request(
            'GET',
            '/api/task',
            [],
            [],
            $this->headers
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $index = 0;
        foreach ($responseContent as $key => $responseTask) {
            if ($responseTask['id'] === $taskFixture['id']) {
                $index = $key;
                break;
            }
        }
        $this->assertNotEquals(0, $index);

        return $responseContent[$index];
    }

    /**
     * @depends testGetAllAndFindLastCreated
     * @param $taskFixture
     */
    public function testDelete($taskFixture)
    {
        $client = $this->getClient();
        $client->request(
            'DELETE',
            "/api/task/{$taskFixture['id']}",
            [],
            [],
            $this->headers
        );
        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

        return $taskFixture['id'];
    }

    /**
     * @depends testDelete
     * @param $notExistedTaskId
     */
    public function testGetNotExisted($notExistedTaskId)
    {
        $client = $this->getClient();
        $client->request(
            'GET',
            "/api/task/{$notExistedTaskId}",
            [],
            [],
            $this->headers
        );
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}

