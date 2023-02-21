<?php

namespace App\DataFixtures;

use App\Entity\Attestation;
use App\Entity\CompetenceQuestion;
use App\Entity\Literature;
use App\Entity\Topic;
use App\Entity\TopicLiterature;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LiteratureFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        /**
         * @var Topic[] $topics
         */
        $topics = $this->getReferencesByEntityClass(Topic::class);

        for ($i = 0; $i < 10; ++$i) {
            $literature = new Literature();
            $literature
                ->setName($this->faker->unique()->word())
                ->setLink($this->faker->url());
            $manager->persist($literature);

            # Решение для включения нескольких тем (топиков)
            $loopIndex = $this->faker->numberBetween(0, count($topics)-1);
            for ($j = 0; $j <= $loopIndex; ++$j) {
                $topicLiterature = new TopicLiterature();
                $topicLiterature
                    ->setLiterature($literature)
                    ->setTopic($topics[$j]);

                $manager->persist($topicLiterature);
                $this->saveReference($topicLiterature);
            }

            $this->saveReference($literature);
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