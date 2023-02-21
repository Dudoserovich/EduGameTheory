<?php

namespace App\DataFixtures;

use App\Entity\Term;
use Doctrine\Persistence\ObjectManager;

class TermFixtures extends BaseFixtureAbstract
{

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $term = new Term();
            $term
                ->setName($this->faker->word())
                ->setDescription($this->faker->paragraph(2));
            $manager->persist($term);
            $this->saveReference($term);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }
}