<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use App\Entity\Literature;
use App\Entity\Topic;
use App\Entity\TopicLiterature;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

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
            "link" => "https://math.semestr.ru/games/games_manual.php",
            "imageName" => "mathSemestr.png"
        ],
        [
            "name" => "PHPSimplex",
            "description" => "Онлайн-калькулятор для решения матриц Симплекс-методом",
            "topic" => [self::TOPICS[0]],
            "link" => "https://www.phpsimplex.com/simplex/simplex.htm?l=en",
            "imageName" => "PHPSimplex.png"
        ],
        [
            "name" => "Теория от университета МГСУ",
            "description" => "Теоретический материал по матричным играм на сайте университета МГСУ",
            "topic" => [self::TOPICS[0]],
            "link" => "https://cito.mgsu.ru/COURSES/course753/media/337981978535638/pdf/teoria.pdf",
            "imageName" => "matrixGame.png"
        ],
        [
            "name" => "Элементы теории кооперативных игр. Университет СО РАН",
            "description" => "Письменная лекция по кооперативным играм от университета СО РАН (6 апреля 2021г.)",
            "topic" => [self::TOPICS[1]],
            "link" => "http://old.math.nsc.ru/~mathecon/Marakulin/CooGAMES.pdf",
            "imageName" => "coopGame.png"
        ],
        [
            "name" => "Теория игр. Хабр",
            "description" => "Статья по теории игр 2012 года на Хабре",
            "topic" => self::TOPICS,
            "link" => "https://habr.com/ru/articles/163681/",
            "imageName" => "GameTheory.png"
        ],
        [
            "name" => "Алексей Савватеев | Теория игр вокруг нас",
            "description" => "Видеоролик на YouTube. Спикер доступно рассказывает всё о теории игр, применении ее в повседневной жизни и о том, как не проиграть. ",
            "topic" => self::TOPICS,
            "link" => "https://www.youtube.com/watch?v=zypuneus6b0",
            "imageName" => "TheoryGameAroundUs.png"
        ],
        [
            "name" => "Решение матричной игры в чистых стратегиях",
            "description" => "Видеоролик на YouTube. Спикер (преподаватель университета НОУ ИНТУИТ) доступным языком рассказывает про решение матричных игр в чистых стратегиях.",
            "topic" => [self::TOPICS[0]],
            "link" => "https://www.youtube.com/watch?v=4SeUROXfMfY",
            "imageName" => "pureStrategies.png"
        ],
        [
            "name" => "Решение матричной игры в смешанных стратегиях",
            "description" => "Видеоролик на YouTube. Спикер (преподаватель университета НОУ ИНТУИТ) доступным языком рассказывает про решение матричных игр в смешанных стратегиях.",
            "topic" => [self::TOPICS[0]],
            "link" => "https://www.youtube.com/watch?v=5hG8A-455Vk&list=PLDrmKwRSNx7LFZahXMjYKixzfMgi5MqFF&index=5",
            "imageName" => "mixedGames.png"
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

        $targetDirectory = "public/uploads/literature";
        $files = scandir($targetDirectory);
        $nameFiles = array_diff($files, array('.', '..'));

        foreach ($nameFiles as $nameFile) {
            $nameFileWithoutSalt = preg_replace("/(-[A-z0-9]+)+?\.png$/", "", $nameFile);

            $foundKeyByImage =
                array_search(
                    "$nameFileWithoutSalt.png",
                    array_column(self::LITERATURES, 'imageName')
                );
//            print_r(array_column(self::LITERATURES, 'imageName'));
            echo $nameFileWithoutSalt;
            var_dump($foundKeyByImage);
            echo PHP_EOL;

            $image = new File("$targetDirectory/$nameFile");

            $literature = self::LITERATURES[$foundKeyByImage];

            $literatureEntity = new Literature();
            $literatureEntity
                ->setImageFile($image)
                ->setName($literature['name'])
                ->setDescription($literature['description'])
                ->setLink($literature['link'])
                ->setImageSize($image->getSize())
                ->setImageName($image->getFilename())
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