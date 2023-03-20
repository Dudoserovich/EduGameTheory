<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use App\Service\FileUploader;
use Container485wsmx\getSluggerService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
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
        array_map('unlink', glob('public/uploads/achievement/*'));

        // непосредственно фикстуры
        for ($i = 0; $i < 5; ++$i) {
            $achievement = new Achievement();
            $targetDirectory = "public/uploads/achievement";
            $imageStr = $this->faker->image($targetDirectory, 360, 360, 'animals');
            $image = new File($imageStr);

            $achievement
                ->setName($this->faker->unique()->word())
                ->setDescription($this->faker->sentence())
                ->setImageSize($image->getSize())
                ->setImageName($image->getFilename())
                ->setImageFile($image);
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