<?php

namespace App\DataFixtures;

use App\Entity\Education;
use App\Entity\EducationTasks;
use App\Entity\Task;
use App\Entity\Topic;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EducationFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $topics = $this->getReferencesByEntityClass(Topic::class);
        $tasks = $this->getReferencesByEntityClass(Task::class);

        for ($i = 0; $i < 10; ++$i) {
            $education = new Education();
            $education
                ->setName($this->faker->unique()->word())
                ->setDescription($this->faker->paragraph(1))
                ->setConclusion($this->faker->paragraph(1))
                ->setTopic($this->faker->randomElement($topics))
            ;
            $manager->persist($education);
            $this->saveReference($education);

            // EduTasks
            $countTasks = $this->faker->numberBetween(3, count($tasks));
            for ($j = 0; $j < $countTasks; $j++) {
                $educationTasks = new EducationTasks();
                $educationTasks
                    ->setTheoryText($this->faker->paragraph(2))
                    ->setTask($this->faker->unique()->randomElement($tasks))
                    ->setEdu($education);

                $manager->persist($education);
                $this->saveReference($education);
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
            TopicFixtures::class,
            TaskFixtures::class
        ];
    }
}