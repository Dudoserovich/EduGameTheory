<?php

namespace App\DataFixtures;

use App\Entity\Term;
use App\Entity\Topic;
use Doctrine\Persistence\ObjectManager;

class TermFixtures extends BaseFixtureAbstract
{
    public const TOPICS = [
        "Матричные игры",
        "Кооперативные игры"
    ];
    public const TERMS = [
        [
            "name" => "Равновесие Нэша",
            "description" => "Равновесие Нэша — это концепция в теории игр, которая описывает ситуацию, когда игроки выбирают свои стратегии независимо друг от друга и не могут получить больше выигрыша, изменяя свою стратегию в одиночку.",
            "topic" => self::TOPICS[0]
        ],
        [
            "name" => "Стоимость игры",
            "description" => "Стоимость игры — это выигрыш или проигрыш, который получает каждый игрок в матричной игре в зависимости от выбранных ими стратегий и стратегий других игроков.",
            "topic" => self::TOPICS[0]
        ],
        [
            "name" => "Игра с нулевой суммой",
            "description" => "Игра с нулевой суммой — это матричная игра, в которой выигрыш одного игрока равен проигрышу другого игрока.",
            "topic" => self::TOPICS[0]
        ],
        [
            "name" => "Игры с коалициями",
            "description" => "Игры с коалициями — это кооперативные игры, в которых игроки могут объединяться в группы (коалиции), чтобы увеличить свои выигрыши.",
            "topic" => self::TOPICS[1]
        ],
        [
            "name" => "С-ядро",
            "description" => "С-ядро — принцип оптимальности в теории кооперативных игр, представляющий собой множество эффективных распределений выигрыша, устойчивых к отклонениям любой коалиции игроков",
            "topic" => self::TOPICS[1]
        ],
        [
            "name" => "Равновесие по Парето",
            "description" => "Равновесие по Парето — это состояние, в котором ни один игрок не может улучшить свой выигрыш, не ухудшив выигрыш другого игрока.",
            "topic" => self::TOPICS[0]
        ],
        [
            "name" => "Система Шепли",
            "description" => "Система Шепли — это метод распределения выигрышей в кооперативной игре между игроками, который учитывает их вклад в достижение общего результата.",
            "topic" => self::TOPICS[1]
        ],
        [
            "name" => "Игры с симметричными выигрышами",
            "description" => "Игры с симметричными выигрышами — это игры, в которых выигрыши каждого игрока совпадают.",
            "topic" => self::TOPICS[0]
        ],
        [
            "name" => "Матрица выигрышей",
            "description" => "Матрица выигрышей — это таблица, которая показывает выигрыши или проигрыши каждого игрока в матричной игре в зависимости от выбранных ими стратегий и стратегий других игроков.",
            "topic" => self::TOPICS[0]
        ],
        [
            "name" => "Коалиция",
            "description" => "Коалиция — это группа игроков в кооперативной игре, которые объединяются для достижения лучшего результата.",
            "topic" => self::TOPICS[1]
        ],
        [
            "name" => "Смешанные стратегии",
            "description" => "Смешанные стратегии — это стратегии, которые включают вероятности выбора каждой из возможных стратегий в матричной игре.",
            "topic" => self::TOPICS[1]
        ]
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $topics = $this->getReferencesByEntityClass(Topic::class);
//        for ($i = 0; $i < 10; ++$i) {
//            $term = new Term();
//            $term
//                ->setName($this->faker->unique()->word())
//                ->setDescription($this->faker->paragraph(2))
//                ->setTopic($this->faker->randomElement($topics));
//            $manager->persist($term);
//            $this->saveReference($term);
//        }


        foreach (self::TERMS as $term) {
            foreach ($topics as $topic) {
                if ($topic->getName() === $term['topic']) {
                    $termEntity = new Term();
                    $termEntity
                        ->setName($term['name'])
                        ->setDescription($term['description'])
                        ->setTopic($topic);
                    $manager->persist($termEntity);
                    $this->saveReference($termEntity);
                }
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
            TopicFixtures::class
        ];
    }
}