<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\CallbackTransformer;
class UserControllerTest extends WebTestCase
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
     * Test the users route for the users list if the user is connected and admin
     *
     * @return void
     */
    public function testRouteListUsersAdmin()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/users');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('h1', "Liste des utilisateurs");
    }

    /**
     * Test the users route for the users list if the user is connected and user (error)
     *
     * @return void
     */
    public function testRouteError403ListUsersUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_3');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/users');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test the redirect users route for the users list if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectListUsersUser()
    {
        $this->client->request('GET', '/users');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");
    }

    /**
     * Test the create route for create a new user by an user connected and admin
     *
     * @return void
     */
    public function testCreateUser(): void
    { 
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/users/create');
        
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'testUser';
        $form['user[password][first]'] = 'Password!1';
        $form['user[password][second]'] = 'Password!1';
        $form['user[email]'] = 'testUser@gmail.com';
        // create an user admin
        $form['user[roles][0]']->tick();
        // create an simple user
        // $form['user[roles][1]']->tick();
        
        $crawler = $this->client->submit($form);

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();

        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("L'utilisateur a bien été ajouté.",current($flashes['success']));

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('h1', "Liste des utilisateurs");
    }

    /**
     * Test the edit route for edit an ancient user if the user is connected and user (error)
     *
     * @return void
     */
    public function testRouteError403CreateUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_3');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/users/create');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test the redirect create route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectCreateUser(): void
    { 
        $this->client->request('GET', '/users/create');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }

    /**
     * Test the edit route for edit an user if the user is connected and admin
     *
     * @return void
     */
    public function testRouteEditUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_1');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $user = $this->entityManager
            ->getRepository(User::class)
            ->find(5);

        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'testUserModify';
        $form['user[password][first]'] = 'Password!1';
        $form['user[password][second]'] = 'Password!1';
        $form['user[email]'] = 'testUserModify@gmail.com';
        $form['user[roles][1]']->tick();

        $crawler = $this->client->submit($form);

        $session = $this->client->getContainer()->get('session');
        $flashes = $session->getBag('flashes')->all();

        $this->assertArrayHasKey('success', $flashes);
        $this->assertCount(1, $flashes['success']);
        $this->assertEquals("L'utilisateur a bien été modifié",current($flashes['success']));

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('h1', "Liste des utilisateurs");
    }
    
    /**
     * Test the edit route for edit an ancient user if the user is connected and user (error)
     *
     * @return void
     */
    public function testRouteError403EditUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('username_3');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $user = $this->entityManager
            ->getRepository(User::class)
            ->find(6);

        $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test the redirect create route for the login page if the user isn't connected
     *
     * @return void
     */
    public function testRouteRedirectEditUser(): void
    { 
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find(7);

        $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSelectorTextContains('button', "Se connecter");    
    }
}
