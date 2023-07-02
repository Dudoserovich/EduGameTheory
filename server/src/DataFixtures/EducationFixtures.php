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

        // оставляем только топик с матричными играми
        $matrixTopic = null;
        foreach ($topics as $topic) {
            if ("Матричные игры" === $topic->getName())
                $matrixTopic = $topic;
        }

        for ($i = 0; $i < 5; ++$i) {
            $education = new Education();
            $education
                ->setName($this->faker->unique()->word())
                ->setDescription($this->faker->paragraph(4))
                ->setConclusion($this->faker->paragraph(4))
                ->setTopic($matrixTopic)
            ;
            $manager->persist($education);
            $this->saveReference($education);
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