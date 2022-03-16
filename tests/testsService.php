<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestsService extends WebTestCase
{
    
    /**
     * Create an user with USER_ROLE
     */
    protected function createUserClient()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->followRedirects();

        $crawler = $client->request('GET', '/login');

        $crawler = $client->submitForm('Se connecter', [
            '_username' => 'username_3',
            '_password' => 'Azerty!1'
        ]);
        static::assertEquals(200, $client->getResponse()->getStatusCode());

        return $crawler;
    }

    /**
     * Create an user with USER_ADMIN
     */
    protected function createAdminClient()
    {
        $client = static::createClient(['environment' => 'test']);

        $client->followRedirects();

        $crawler = $client->request('GET', '/login');

        $crawler = $client->submitForm('Se connecter', [
            '_username' => 'username_1',
            '_password' => 'Azerty!1'
        ]);
        static::assertEquals(200, $client->getResponse()->getStatusCode());

        return $crawler;
    }
}
