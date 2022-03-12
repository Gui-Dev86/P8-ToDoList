<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{/*
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }*/

    public function testLoginAuthentications()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username_1',
            '_password' => 'Azerty!1'
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorNotExists('.alert.alert-danger');
    }
/*
    public function testLoginBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username_1',
            '_password' => 'Fake'
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }*/
}
