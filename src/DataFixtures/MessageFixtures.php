<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private const COUNT = 6;

    private Generator $faker;

    private array $users = [];

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    private function getUserWithout(User $user): ?User
    {
        $userCount = count($this->users);

        while ($userCount >= 2) {
            $randomUser = $this->faker->randomElement($this->users);
            if ($randomUser !== $user) {
                return $randomUser;
            }
        }

        return null;
    }

    public function load(ObjectManager $manager)
    {
        $this->users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < self::COUNT; $i++) {
            $sender = $this->faker->randomElement($this->users);
            $recipient = $this->getUserWithout($sender);

            $message = new Message();
            $message
                ->setTopic($this->faker->word)
                ->setText($this->faker->text)
                ->setRecipient($recipient)
                ->setSender($sender)
            ;
            $manager->persist($message);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
