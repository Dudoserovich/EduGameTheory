<?php

namespace App\EventListener;

use App\Entity\UserAchievement;
use Doctrine\Persistence\Event\LifecycleEventArgs;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class UserAchievementListener
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    /**
     * Отправка уведомлений на фронт при получении достижения.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof UserAchievement) {
            return;
        }

        $achievement = $entity->getAchievement();
        $nameAchievement = $achievement->getName();

        if ($entity->getAchievementDate()) {
            $update = new Update(
                topics: '/achievements',
                data: json_encode(['message' => "Достижение \"$nameAchievement\" получено!"]),
                type: $entity->getUser()->getId()
            );

            $this->hub->publish($update);
        }
    }
}