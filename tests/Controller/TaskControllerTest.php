<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $entityManager;

    private $client;

    protected function setUp(): void {
        
        $this->client = $this->createClient(
            ['environment' => 'test']);

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testRouteListTaskToDo(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.glyphicon-remove');
    }

    public function testRouteRedirectListTaskToDo(): void
    { 
        $crawler = $this->client->request('GET', '/tasks');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    public function testRouteListTaskIsDone(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.glyphicon-ok');
    }

    public function testRouteRedirectListTaskIsDone(): void
    { 
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    public function testCreateTask(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Titre test';
        $form['task[content]'] = 'Contenu test';
        $this->client->submit($form);

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();

        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("La tâche a été bien été ajoutée.",current($flashes['success']));

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRouteRedirectCreate(): void
    { 
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    public function testEditTask(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(26);
        
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Titre mofifié test';
        $form['task[content]'] = 'Contenu modifié test';
        $crawler = $this->client->submit($form);

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals('La tâche a bien été modifiée.',
        current($flashes['success']));

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRouteRedirectEdite(): void
    { 
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(5);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    public function testDeleteOwnTask(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_10');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(10);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        
        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("La tâche Title_10 a bien été supprimée.",
        current($flashes['success']));

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteOtherTask(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(14);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteAnonymeTask(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(2);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals(
            "La tâche Title_2 a bien été supprimée.",
            current($flashes['success'])
        );

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserDeleteAnonymeTask(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_3');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(3);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testRouteRedirectDelete(): void
    { 
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(32);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }
/*
    public function testTogleToDo(): void
    {

    }

    public function testTogleIsDone(): void
    {

    }
*/
    public function testRouteRedirectToggle(): void
    { 
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(41);

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }
}
