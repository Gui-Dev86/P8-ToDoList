<?php

namespace App\Tests\Controller;

use App\Tests\TestsService;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends TestsService
{
    public function testLoginRoute()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur :")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe :")')->count());
    }

    public function testLoginAuthentications()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username_1',
            '_password' => 'Azerty!1'
        ]);

        $client->submit($form);
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username_1',
            '_password' => 'Fake'
        ]);

        $client->submit($form);
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLogoutRoute()
    {
        $client = static::createClient(['environment' => 'test'],
        [
            'PHP_AUTH_USER' => 'user1',
            'PHP_AUTH_PW'   => 'test1',
        ]);

        $client->request('GET', '/logout');
        $this->assertTrue($client->getResponse()->isRedirection());
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

}
