<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    
    /**
     * Test the homepage route if the user is connected
     *
     * @return void
     */
    public function testRouteHomePage()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }

    /**
     * Test the redirect homepage route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectHomePage()
    {
        $client = static::createClient(['environment' => 'test']);
        $client->request('GET', '/');

        $client->followRedirect();

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    /**
     * Test the error 404 with a bad route (error)
     *
     * @return void
     */
    public function testErrorLink()
    {
        $client = static::createClient(['environment' => 'test']);
        $client->request('GET', '/testErrorLink');
        
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
}
