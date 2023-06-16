<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use Attribute;
use Doctrine\Persistence\ObjectManager;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\File\File;

class AchievementFixtures extends BaseFixtureAbstract
{
    public const ACHIEVEMENTS = [
        [
            "name" => "Интересующийся",
            "description" => "Перейти по 5 ссылкам на сторонние ресурсы",
            "imageName" => "interested.png",
            "typeOfInteraction" => "переходы(клики)",
            "needScore" => 5,
            "needTries" => null,
            "rating" => null
        ],
        [
            "name" => "Догадливый",
            "description" => "Пройти задание с первого раза",
            "imageName" => "shrewd.png",
            "typeOfInteraction" => "прохождение",
            "needScore" => 1,
            "needTries" => 1,
            "rating" => null
        ],
        [
            "name" => "Наугад",
            "description" => "Пройти задание с 5-го раза",
            "imageName" => "atRandom.png",
            "typeOfInteraction" => "прохождение",
            "needScore" => 1,
            "needTries" => 5,
            "rating" => null
        ],
        [
            "name" => "Упорный",
            "description" => "Пройти 5 заданий",
            "imageName" => "professor.png",
            "typeOfInteraction" => "прохождение",
            "needScore" => 5,
            "needTries" => null,
            "rating" => null
        ],
        [
            "name" => "Трипл",
            "description" => "Пройти 3 задания на оценку 5",
            "imageName" => "firstTask.png",
            "typeOfInteraction" => "прохождение",
            "needScore" => 3,
            "needTries" => null,
            "rating" => 5
        ],
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Удаление файлов из директории
//        array_map('unlink', glob('public/uploads/achievement/*'));

        // непосредственно фикстуры

        $targetDirectory = "public/uploads/achievement";
        $files = scandir($targetDirectory);
        $nameFiles = array_diff($files, array('.', '..'));

        foreach ($nameFiles as $nameFile) {
            $nameFileWithoutSalt = preg_replace("/(-[A-z0-9]+)+.png$/", "", $nameFile);

            echo $nameFileWithoutSalt;

            $foundKeyByImage =
            array_search(
                "$nameFileWithoutSalt.png",
                array_column(self::ACHIEVEMENTS, 'imageName')
            );

            $achievement = new Achievement();

//            $targetDirectory = "public/uploads/achievement";
//            $imageStr = $this->faker->image($targetDirectory, 360, 360, 'animals');

            $image = new File("$targetDirectory/$nameFile");

            $foundAchievement = self::ACHIEVEMENTS[$foundKeyByImage];

            $achievement
                ->setImageFile($image)
                ->setName($foundAchievement['name'])
                ->setDescription($foundAchievement['description'])
                ->setImageSize($image->getSize())
                ->setImageName($image->getFilename())
                ->setNeedScore($foundAchievement['needScore'])
                ->setNeedTries($foundAchievement['needTries'])
                ->setRating($foundAchievement['rating'])
                ->setTypeOfInteraction($foundAchievement['typeOfInteraction'])
            ;

            $manager->persist($achievement);
            $this->saveReference($achievement);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [self::DEV_GROUP];
    }
}