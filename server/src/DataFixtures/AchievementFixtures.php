<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class AchievementFixtures extends BaseFixtureAbstract
{
    public const ACHIEVEMENTS = [
        [
            "name" => "Интересующийся",
            "description" => "Перейти по 5 ссылкам на сторонние ресурсы",
            "imageName" => "interested.png"
        ],
        [
            "name" => "Догадливый",
            "description" => "Пройти задание с первого раза",
            "imageName" => "shrewd.png"
        ],
        [
            "name" => "Наугад",
            "description" => "Пройти задание с 5-го раза",
            "imageName" => "at_random.png"
        ],
        [
            "name" => "Первый блин - комом?",
            "description" => "Создать 1-ое задание",
            "imageName" => "first_task.png"
        ],
        [
            "name" => "Профессор",
            "description" => "Создать 4 задания",
            "imageName" => "professor.png"
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

            $foundKeyByImage =
            array_search(
                "$nameFileWithoutSalt.png",
                array_column(self::ACHIEVEMENTS, 'imageName')
            );

            $achievement = new Achievement();

//            $targetDirectory = "public/uploads/achievement";
//            $imageStr = $this->faker->image($targetDirectory, 360, 360, 'animals');

            $image = new File("$targetDirectory/$nameFile");

            echo($nameFileWithoutSalt . PHP_EOL);
            echo($foundKeyByImage . PHP_EOL);

            $achievement
                ->setImageFile($image)
                ->setName(self::ACHIEVEMENTS[$foundKeyByImage]['name'])
                ->setDescription(self::ACHIEVEMENTS[$foundKeyByImage]['description'])
                ->setImageSize($image->getSize())
                ->setImageName($image->getFilename());
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