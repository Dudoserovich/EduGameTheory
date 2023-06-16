<?php

namespace App\DataFixtures;

use App\Entity\Literature;
use App\Entity\Topic;
use App\Entity\TopicLiterature;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LiteratureFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{
    public const TOPICS = [
        "Матричные игры",
        "Кооперативные игры"
    ];
    public const LITERATURES = [
        [
            "name" => "Онлайн-калькулятор по теории игр",
            "description" => "Калькулятор по теории игр, содержащий калькуляторы по методам матричных игр, а так же теоретическое описание работы методов",
            "topic" => [self::TOPICS[0]],
            "link" => "https://math.semestr.ru/games/games_manual.php"
        ],
        [
            "name" => "PHPSimplex",
            "description" => "Онлайн-калькулятор для решения матриц Симплекс-методом",
            "topic" => [self::TOPICS[0]],
            "link" => "https://www.phpsimplex.com/simplex/simplex.htm?l=en"
        ],
        [
            "name" => "Теория от университета МГСУ",
            "description" => "Теоретический материал по матричным играм на сайте университета МГСУ",
            "topic" => [self::TOPICS[0]],
            "link" => "https://cito.mgsu.ru/COURSES/course753/media/337981978535638/pdf/teoria.pdf"
        ],
        [
            "name" => "Элементы теории кооперативных игр. Университет СО РАН",
            "description" => "Письменная лекция по кооперативным играм от университета СО РАН (6 апреля 2021г.)",
            "topic" => [self::TOPICS[1]],
            "link" => "http://old.math.nsc.ru/~mathecon/Marakulin/CooGAMES.pdf"
        ],
        [
            "name" => "Теория игр. Хабр",
            "description" => "Статья по теории игр 2012 года на Хабре",
            "topic" => self::TOPICS,
            "link" => "https://habr.com/ru/articles/163681/"
        ],
        [
            "name" => "Алексей Савватеев | Теория игр вокруг нас",
            "description" => "Видеоролик на YouTube. Спикер доступно рассказывает всё о теории игр, применении ее в повседневной жизни и о том, как не проиграть. ",
            "topic" => self::TOPICS,
            "link" => "https://www.youtube.com/watch?v=zypuneus6b0"
        ],
        [
            "name" => "Решение матричной игры в чистых стратегиях",
            "description" => "Видеоролик на YouTube. Спикер (преподаватель университета НОУ ИНТУИТ) доступным языком рассказывает про решение матричных игр в чистых стратегиях.",
            "topic" => [self::TOPICS[0]],
            "link" => "https://www.youtube.com/watch?v=4SeUROXfMfY"
        ],
        [
            "name" => "Решение матричной игры в смешанных стратегиях",
            "description" => "Видеоролик на YouTube. Спикер (преподаватель университета НОУ ИНТУИТ) доступным языком рассказывает про решение матричных игр в смешанных стратегиях.",
            "topic" => [self::TOPICS[0]],
            "link" => "https://www.youtube.com/watch?v=5hG8A-455Vk&list=PLDrmKwRSNx7LFZahXMjYKixzfMgi5MqFF&index=5"
        ]
    ];

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

//        for ($i = 0; $i < 10; ++$i) {
//            $literature = new Literature();
//            $literature
//                ->setName($this->faker->unique()->word())
//                ->setDescription($this->faker->paragraph(1))
//                ->setLink($this->faker->url());
//            $manager->persist($literature);
//
//            # Решение для включения нескольких тем (топиков)
//            $loopIndex = $this->faker->numberBetween(0, count($topics)-1);
//            for ($j = 0; $j <= $loopIndex; ++$j) {
//                $topicLiterature = new TopicLiterature();
//                $topicLiterature
//                    ->setLiterature($literature)
//                    ->setTopic($topics[$j]);
//
//                $manager->persist($topicLiterature);
//                $this->saveReference($topicLiterature);
//            }
//
//            $this->saveReference($literature);
//        }

        foreach (self::LITERATURES as $literature) {
            $literatureEntity = new Literature();
            $literatureEntity
                ->setName($literature['name'])
                ->setDescription($literature['description'])
                ->setLink($literature['link'])
            ;
            $manager->persist($literatureEntity);

            foreach ($topics as $topicEntity) {
                if (in_array($topicEntity->getName(), $literature['topic'])) {
                    $topicLiterature = new TopicLiterature();
                    $topicLiterature
                        ->setLiterature($literatureEntity)
                        ->setTopic($topicEntity)
                    ;

                    $manager->persist($topicLiterature);
                    $this->saveReference($topicLiterature);
                }
            }

            $this->saveReference($literatureEntity);
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