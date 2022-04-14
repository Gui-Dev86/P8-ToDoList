<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
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

    /**
     * Test the tasks route for the tasks to do list if the user is connected
     *
     * @return void
     */
    public function testRouteListTasksToDo(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.glyphicon-remove');
    }

    /**
     * Test the redirect tasks route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectListTasksToDo(): void
    { 
       $this->client->request('GET', '/tasks');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");
    }

    /**
     * Test the tasks/done route for the tasks done list if the user is connected
     *
     * @return void
     */
    public function testRouteListTasksIsDone(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/tasks/done');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.glyphicon-ok');
    }

    /**
     * Test the redirect tasks/done route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectListTasksIsDone(): void
    { 
        $this->client->request('GET', '/tasks/done');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");
    }

    /**
     * Test the create route for create a new task by an user connected
     *
     * @return void
     */
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

    /**
     * Test the redirect create route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectCreateTask(): void
    { 
        $this->client->request('GET', '/tasks/create');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    /**
     * Test the edit route for edit an ancient task "to do" by an user connected
     *
     * @return void
     */
    public function testEditTaskToDo(): void
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
        $this->assertSelectorExists('.glyphicon-remove');
    }

    /**
     * Test the edit route for edit an ancient task "is Done" by an user connected
     *
     * @return void
     */
    public function testEditTaskIsDone(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(27);
        
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
        $this->assertSelectorExists('.glyphicon-ok');
    }

    /**
     * Test the redirect edit route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectEditeTask(): void
    { 
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(5);

        $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    /**
     * Test the delete route for delete a task by is own autor
     *
     * @return void
     */
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

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        
        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("La tâche ".$task->getTitle()." a bien été supprimée.",
        current($flashes['success']));

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test the delete route for delete a task by an other autor (error)
     *
     * @return void
     */
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

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

     /**
     * Test the delete route for delete an anonyme task by an admin
     *
     * @return void
     */
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

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals(
            "La tâche ".$task->getTitle()." a bien été supprimée.",
            current($flashes['success'])
        );

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

     /**
     * Test the delete route for delete an anonyme task by an user (error)
     *
     * @return void
     */
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

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test the redirect delete route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectDeleteTask(): void
    { 
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(32);

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

     /**
     * Test the redirect toggle route for pass a task in "done"
     *
     * @return void
     */
    public function testToggleTaskIsDone(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_3');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(17);

        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
        
        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals(
            "La tâche ".$task->getTitle()." a bien été envoyée dans les tâches terminées.",
            current($flashes['success'])
        );
        $this->client->followRedirect();
        // request the route to initiate the task in "to do" in the database to test again
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
    }

    /**
     * Test the redirect toggle route for pass a task in "to do"
     *
     * @return void
     */
    public function testToggleTaskToDo(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_3');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(9);

        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();
        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals(
            'La tâche '.$task->getTitle().' a bien été envoyée dans les tâches à faire.',
            current($flashes['success'])
        );
        $this->client->followRedirect();
        // request the route to initiate the task in "done" in the database to test again
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
    }

    /**
     * Test the redirect toggle route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectToggleTask(): void
    { 
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->find(41);

        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }
}
