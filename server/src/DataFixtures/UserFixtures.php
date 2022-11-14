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
//    public const USER_TEAM_COUNT = 10;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Данные генерируются на основе уже существующих специализаций, грейдов и команд:
     *
     * - Сначала создается администратор. Единственный пользователь с ролью администратора.
     * - Затем по каждой специализации создаются self::USER_TEAM_COUNT разработчиков.
     *   Первый всегда имеет роль лида, остальные - нет.
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

        // Создание лидов и разработчиков.
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

        // Спизжено с DromUpgrade, но можно изменить, когда создадим фикстуры для всего основного
//        for ($i = 0; $i < count(SpecFixtures::SPEC_TEAMS) * self::USER_TEAM_COUNT; ++$i) {
//            $user = new User();
//            $user
//                ->setRoles($i % self::USER_TEAM_COUNT ? [self::ROLE_USER] : [self::ROLE_USER, self::ROLE_LEAD])
//                ->setUsername("login$i")
//                ->setPassword($this->passwordHasher->hashPassword($user, "password$i"))
//                ->setFio($this->faker->name)
//                ->setEmail($this->faker->email);
//            $manager->persist($user);
//            $this->saveReference($user);
//        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }
}