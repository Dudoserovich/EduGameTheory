<?php

namespace App\DataFixtures;

use App\Entity\Term;
use App\Entity\Topic;
use Doctrine\Persistence\ObjectManager;

class TermFixtures extends BaseFixtureAbstract
{

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $topics = $this->getReferencesByEntityClass(Topic::class);
        for ($i = 0; $i < 10; ++$i) {
            $term = new Term();
            $term
                ->setName($this->faker->unique()->word())
                ->setDescription($this->faker->paragraph(2))
                ->setTopic($this->faker->randomElement($topics));
            $manager->persist($term);
            $this->saveReference($term);
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
            TopicFixtures::class
        ];
    }
}