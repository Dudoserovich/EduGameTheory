<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\UserAchievement;
use App\Repository\AchievementRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserListener
{
    private AchievementRepository $achievementRepository;

    public function __construct(
        AchievementRepository $achievementRepository
    )
    {
        $this->achievementRepository = $achievementRepository;
    }

    /**
     * Отправка уведомлений на фронт при получении достижения.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $achievements = $this->achievementRepository->findAll();

        foreach ($achievements as $achievement) {
            $userAchievement = new UserAchievement();
            $userAchievement
                ->setUser($entity)
                ->setAchievement($achievement)
            ;
        }
    }
}