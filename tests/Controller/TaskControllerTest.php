<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{

    public function testRouteListTaskToDo(): void
    { 
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.glyphicon-remove');
        
    }

    public function testRouteListTaskIsDone(): void
    { 
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/done');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.glyphicon-ok');
    }

    public function testCreateTask(): void
    { 
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');
        
        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'Titre test';
        $form['task[content]'] = 'Contenu test';

        $client->submit($form);

        $session = $client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();

        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("La tâche a été bien été ajoutée.",current($flashes['success']));

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }


    public function testEditTask(): void
    { 
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');
        
        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'Titre test';
        $form['task[content]'] = 'Contenu test';

        $client->submit($form);

        $session = $client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("La tâche a été bien été ajoutée.",current($flashes['success']));

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }

}
