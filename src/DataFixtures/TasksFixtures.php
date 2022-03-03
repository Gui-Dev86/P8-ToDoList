<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TasksFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tasks = [];

        for ($i = 1; $i < 50; $i++) {
            
            $task = new Task();
            $task->setCreatedAt(new \DateTime())
                ->setTitle('Title_' . $i)
                ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
                ->setIsDone(rand(true, false))
                ->setUser($this->getReference(rand(1,10)));

            $manager->persist($task);
            $tasks[] = $task;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
        ];
    }
}
