<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtureAbstract
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_TEACHER = 'ROLE_TEACHER';
    public const ROLE_USER = 'ROLE_USER';
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * - Сначала создается администратор. Единственный пользователь с ролью администратора.
     * - Затем чередуясь создаются ученики и преподаватели.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Создание администратора.
        $admin = new User();
        $admin
            ->setUsername("admin")
            ->setPassword($this->passwordHasher->hashPassword($admin, "admin"))
            ->setFio($this->faker->name)
            ->setEmail($this->faker->email)
            ->setRoles([self::ROLE_ADMIN]);
        $manager->persist($admin);
        $this->saveReference($admin);

        // Создание учеников и преподавателей.
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user
                ->setRoles($i % 2 ? [self::ROLE_USER] : [self::ROLE_TEACHER])
                ->setUsername("login$i")
                ->setPassword($this->passwordHasher->hashPassword($user, "password$i"))
                ->setFio($this->faker->name)
                ->setEmail($this->faker->email);
            $manager->persist($user);
            $this->saveReference($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }
}