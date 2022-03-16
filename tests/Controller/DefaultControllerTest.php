<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    
    public function testHomePageRoute()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->request('GET', '/');
        $client->followRedirect();
  
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testErrorLink()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->request('GET', '/testErrorLink');
        
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
}
