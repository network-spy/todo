<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckerControllerTest extends WebTestCase
{
    public function testAlive()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals('"alive"', $client->getResponse()->getContent());
    }
}
