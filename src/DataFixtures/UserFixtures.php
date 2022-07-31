<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private const COUNT = 3;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < self::COUNT; $i++) {
            $user = $this
                ->createUser()
                ->setName($faker->firstName)
                ->setSurname($faker->lastName)
                ->setPatronymic($faker->firstName)
                ->setEmail($faker->email)
            ;
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                1234,
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function createUser(): User
    {
        return new User();
    }
}
