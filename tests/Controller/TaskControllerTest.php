<?php

namespace App\Tests\Controller;

use App\Tests\TestsService;

class TaskControllerTest extends TestsService
{
    public function testListTaskToDo(): void
    {
        $client = static::createClient(['environment' => 'test']);

        $crawler = $client->request('GET', '/tasks');
        
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
}
