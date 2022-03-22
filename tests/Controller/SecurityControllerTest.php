<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Test the login route for the login page
     *
     * @return void
     */
    public function testLoginRoute()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur :")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe :")')->count());
    }

    /**
     * Test the login route for authenticate correctly the user
     *
     * @return void
     */
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
        static::assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }

    /**
     * Test the login route for authenticate the user with bad credentials and redirect for the login page
     *
     * @return void
     */
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
    
    /**
     * Test the logout route for deconect the user and redirect for the login page
     *
     * @return void
     */
    public function testLogoutRoute()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->request('GET', '/logout');
        $this->assertTrue($client->getResponse()->isRedirection());

        $crawler = $client->followRedirect();

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur :")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe :")')->count());
    }

}
