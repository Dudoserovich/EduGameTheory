<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class AchievementFixtures extends BaseFixtureAbstract
{
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
            $achievement = new Achievement();

//            $targetDirectory = "public/uploads/achievement";
//            $imageStr = $this->faker->image($targetDirectory, 360, 360, 'animals');

            $image = new File("$targetDirectory/$nameFile");

//            $image = $this->random_pic();

            $achievement
                ->setImageFile($image)
                ->setName($this->faker->unique()->word())
                ->setDescription($this->faker->sentence())
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