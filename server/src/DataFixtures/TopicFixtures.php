<?php

namespace App\DataFixtures;

use App\Entity\Topic;
use Doctrine\Persistence\ObjectManager;

class TopicFixtures extends BaseFixtureAbstract
{
    public const NAMES = [
        'Матричные игры',
        'Кооперативные игры'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::NAMES as $name) {
            $topic = new Topic();
            $topic->setName($name);
            $manager->persist($topic);
            $this->saveReference($topic);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }
}