<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class UserEntityTest extends KernelTestCase
{
    private const EMAIL_NOT_BLANK = "Ce champ est requis.";

    private const EMAIL_INVALID = "Veuillez entrer une adresse email valide.";

    private const EMAIL_INVALID_VALUE = "testFakeEmail@gmail";

    private const EMAIL_VALID_VALUE = "testEmail@gmail.fr";

    private const PASSWORD_NOT_BLANK = "Ce champ est requis.";

    private const PASSWORD_REGEX = "Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.";

    private const PASSWORD_INVALID_VALUE = "testPass";

    private const PASSWORD_VALID_VALUE = "testPass123";

    private const USERNAME_NOT_BLANK = "Ce champ est requis.";

    private const USERNAME_VALID_VALUE = "TestUsername";

    private const USER_ROLES = ['ROLE_USER'];

    protected function setUp(): void
    {
        $kernel= self::bootKernel();

        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testUserEntityValid(): void
    {
        $user = new User();

        $user->setUsername(self::USERNAME_VALID_VALUE);
        $this->assertEquals(self::USERNAME_VALID_VALUE, $user->getUserIdentifier());
        $user->setEmail(self::EMAIL_VALID_VALUE);
        $this->assertEquals(self::EMAIL_VALID_VALUE, $user->getEmail());
        $user->setPassword(self::PASSWORD_VALID_VALUE);
        $this->assertEquals(self::PASSWORD_VALID_VALUE, $user->getPassword());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());

        $this->getValidationErrors($user, 0);
    }

    public function testUserEntityNoValidNoUsername(): void
    {
        $user = new User();

        $user->setEmail(self::EMAIL_VALID_VALUE);
        $this->assertEquals(self::EMAIL_VALID_VALUE, $user->getEmail());
        $user->setPassword(self::PASSWORD_VALID_VALUE);
        $this->assertEquals(self::PASSWORD_VALID_VALUE, $user->getPassword());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());

        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::USERNAME_NOT_BLANK, $errors[0]->getMessage());
    }

    public function testUserEntityNoValidNoPassword(): void
    {
        $user = new User();

        $user->setUsername(self::USERNAME_VALID_VALUE);
        $this->assertEquals(self::USERNAME_VALID_VALUE, $user->getUserIdentifier());
        $user->setEmail(self::EMAIL_VALID_VALUE);
        $this->assertEquals(self::EMAIL_VALID_VALUE, $user->getEmail());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());

        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::PASSWORD_NOT_BLANK, $errors[0]->getMessage());
    }

    public function testUserEntityNoValidNoEmail(): void
    {
        $user = new User();

        $user->setUsername(self::USERNAME_VALID_VALUE);
        $this->assertEquals(self::USERNAME_VALID_VALUE, $user->getUserIdentifier());
        $user->setPassword(self::PASSWORD_VALID_VALUE);
        $this->assertEquals(self::PASSWORD_VALID_VALUE, $user->getPassword());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());

        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::EMAIL_NOT_BLANK, $errors[0]->getMessage());
    }

    public function testUserEntityNoValidPasswordNoRegex(): void
    {
        $user = new User();
        
        $user->setUsername(self::USERNAME_VALID_VALUE);
        $this->assertEquals(self::USERNAME_VALID_VALUE, $user->getUserIdentifier());
        $user->setEmail(self::EMAIL_VALID_VALUE);
        $this->assertEquals(self::EMAIL_VALID_VALUE, $user->getEmail());
        $user->setPassword(self::PASSWORD_INVALID_VALUE);
        $this->assertEquals(self::PASSWORD_INVALID_VALUE, $user->getPassword());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());

        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::PASSWORD_REGEX, $errors[0]->getMessage());
    }

    public function testUserEntityNoValidEmail(): void
    {
        $user = new User();
        
        $user->setUsername(self::USERNAME_VALID_VALUE);
        $this->assertEquals(self::USERNAME_VALID_VALUE, $user->getUserIdentifier());
        $user->setEmail(self::EMAIL_INVALID_VALUE);
        $this->assertEquals(self::EMAIL_INVALID_VALUE, $user->getEmail());
        $user->setPassword(self::PASSWORD_VALID_VALUE);
        $this->assertEquals(self::PASSWORD_VALID_VALUE, $user->getPassword());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());

        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::EMAIL_INVALID, $errors[0]->getMessage());
    }

    public function testTasks(): void
    {
        $this->user = new User();
        $this->task = new Task();

        $tasks = $this->user->getTasks($this->task->getUser());
        $this->assertSame($this->user->getTasks(), $tasks);

        $this->user->addtask($this->task);
        $this->assertCount(1, $this->user->getTasks());

        $this->user->removeTask($this->task);
        $this->assertCount(0, $this->user->getTasks());
    }

    private function getValidationErrors(User $user, int $numberOfExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;
    }
}
