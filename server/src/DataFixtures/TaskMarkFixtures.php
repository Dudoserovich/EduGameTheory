<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use App\Entity\Task;
use App\Entity\TaskMark;
use App\Entity\User;
use App\Entity\UserAchievement;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskMarkFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // TODO: Стоит ещё подумать на счёт вида фикстур для taskMark,
        //  потому что:
        //  - Они должны быть в зависимости с UserAchievement
        //  - Не стоит раздавать результаты сразу для всех заданий всем пользователям
//        $tasks = $this->getReferencesByEntityClass(Task::class);
//        $users = $this->getReferencesByEntityClass(User::class);
//
//        foreach ($tasks as $task) {
//            foreach ($users as $user) {
//                $taskMark = new TaskMark();
//                $taskMark
//                    ->setTask($task)
//                    ->setUser($user)
//                    ->setRating($this->faker->numberBetween(2, 5));
//                $manager->persist($taskMark);
//                $this->saveReference($taskMark);
//            }
//        }
//
//        $manager->flush();
    }
    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TaskFixtures::class
        ];
    }
}