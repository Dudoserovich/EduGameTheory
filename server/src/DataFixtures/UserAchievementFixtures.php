<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use App\Entity\User;
use App\Entity\UserAchievement;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserAchievementFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $this->getReferencesByEntityClass(User::class);
        $achievements = $this->getReferencesByEntityClass(Achievement::class);

        foreach ($achievements as $achievement) {
            foreach ($users as $user) {
                $userAchievement = new UserAchievement();
                $userAchievement
                    ->setUser($user)
                    ->setAchievement($achievement)
                ;

                $manager->persist($userAchievement);
                $this->saveReference($userAchievement);
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
            UserFixtures::class,
            AchievementFixtures::class
        ];
    }
}