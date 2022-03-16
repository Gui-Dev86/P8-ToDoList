<?php

namespace App\Tests\Controller;

use App\Tests\TestsService;

class DefaultControllerTest extends TestsService
{
    
    public function testHomePageRoute()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->request('GET', '/');
        $client->followRedirect();
  
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testHomePageConnected()
    {
        $this::createUserClient();
        
        static::assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }
    
    public function testErrorLink()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->request('GET', '/testErrorLink');
        
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
}
