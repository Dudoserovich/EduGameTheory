<?php

namespace App\DataFixtures;

use App\Entity\Education;
use App\Entity\EducationTasks;
use App\Entity\Task;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EducationTasksFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $educations = $this->getReferencesByEntityClass(Education::class);
        $tasks = $this->getReferencesByEntityClass(Task::class);

        /**
         * @var Education[] $educations
         */
        foreach ($educations as $education) {
            $countTasks = $this->faker->numberBetween(1, 3);

            // Оставляем только подходящие по топику задания
            $tasksWithNeedTopic = array_filter(
                $tasks,
                fn(Task $task): bool => $task->getTopic() === $education->getTopic()
            );

            // задаём блоки
            for ($i = 0; $i < $countTasks; $i++) {
                $eduTasks = new EducationTasks();
                $eduTasks
                    ->setTask(
                        $this->faker->randomElement($tasksWithNeedTopic)
                    )
                    ->setBlockNumber($i+1)
                    ->setTheoryText($this->faker->paragraph(20))
                    ->setEdu($education)
                    ;

                $manager->persist($eduTasks);
                $this->saveReference($eduTasks);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }

    public function getDependencies(): array
    {
        return [
            EducationFixtures::class,
            TaskFixtures::class
        ];
    }
}