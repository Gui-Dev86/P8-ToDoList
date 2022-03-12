<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use DateTime;

class TaskEntityTest extends KernelTestCase
{
        
    private const TITLE_VALID_VALUE = "Titre tâche";
   
    private const CONTENT_VALID_VALUE = "Contenu tâche";
      
    private const IS_DONE = false;

    private const TITLE_NOT_BLANK = "Un nom de tâche est requis.";

    private const CONTENT_NOT_BLANK = "Une description de la tâche est requise.";

    protected function setUp(): void
    {
        $kernel= self::bootKernel();

        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testTaskEntityValid(): void
    {
        $task = new Task();

        $task->setCreatedAt(new DateTime());
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
        $task->setTitle(self::TITLE_VALID_VALUE);
        $this->assertEquals(self::TITLE_VALID_VALUE, $task->getTitle());
        $task->setContent(self::CONTENT_VALID_VALUE);
        $this->assertEquals(self::CONTENT_VALID_VALUE, $task->getContent());
        $task->setIsDone(self::IS_DONE);
        $this->assertEquals(self::IS_DONE, $task->getIsDone());

        $this->getValidationErrors($task, 0);
    }

    public function testTaskEntityNoValidNoTitle(): void
    {
        $task = new Task();

        $task->setCreatedAt(new DateTime());
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
        $task->setContent(self::CONTENT_VALID_VALUE);
        $this->assertEquals(self::CONTENT_VALID_VALUE, $task->getContent());
        $task->setIsDone(self::IS_DONE);
        $this->assertEquals(self::IS_DONE, $task->getIsDone());

        $errors = $this->getValidationErrors($task, 1);

        $this->assertEquals(self::TITLE_NOT_BLANK, $errors[0]->getMessage());
    }

    public function testTaskEntityNoValidNoContent(): void
    {
        $task = new Task();

        $task->setCreatedAt(new DateTime());
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
        $task->setTitle(self::TITLE_VALID_VALUE);
        $this->assertEquals(self::TITLE_VALID_VALUE, $task->getTitle());
        $task->setIsDone(self::IS_DONE);
        $this->assertEquals(self::IS_DONE, $task->getIsDone());

        $errors = $this->getValidationErrors($task, 1);

        $this->assertEquals(self::CONTENT_NOT_BLANK, $errors[0]->getMessage());
    }

    private function getValidationErrors(Task $task, int $numberOfExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($task);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;
    }
}
