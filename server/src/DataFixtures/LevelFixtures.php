<?php

namespace App\DataFixtures;

use App\Entity\Level;
use Doctrine\Persistence\ObjectManager;

class LevelFixtures extends BaseFixtureAbstract
{
    const SCORES = [0, 5, 10, 20, 30, 50];

    /**
     * Запись всех возможных уровней в справочник уровней.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Можно вывести какую-нибудь логарифмическую функцию
        for ($i = 0; $i <= 5; $i++) {
            $level = new Level();
            $level
                ->setName("Уровень $i")
                ->setNeedScores(self::SCORES[$i])
            ;

            $manager->persist($level);
            $this->saveReference($level);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }

}