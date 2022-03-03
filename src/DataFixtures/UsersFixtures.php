<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        for ($i = 1; $i <= 10; $i++) {
            
            $user = new User();
            $user->setUsername('username_' . $i)
                ->setPassword($this->passwordHasher->hashPassword($user, 'azerty'))
                ->setEmail('username_' . $i . '@gmail.fr');
                if ($i < 3) {
                    $user->setRoles(['ROLE_ADMIN']);
                } else {
                    $user->setRoles(['ROLE_USER']);
                }
                $this->addReference($i, $user);

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
