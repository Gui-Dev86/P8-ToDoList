<?php

namespace App\Tests\Controller;

use App\Tests\TestsService;

class UserControllerTest extends TestsService
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1')->count());
    }
}
