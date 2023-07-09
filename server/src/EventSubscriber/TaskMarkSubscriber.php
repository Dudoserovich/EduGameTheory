<?php

namespace App\EventSubscriber;

use App\Entity\Achievement;
use App\Entity\TaskMark;
use App\Entity\UserAchievement;
use App\Repository\AchievementRepository;
use App\Repository\TaskMarkRepository;
use App\Repository\UserAchivementRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

//use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Данный подписчик подходит только для прохождения задания пользователем,
 *  т.к. TaskMark создаётся после прохождения задания.
 */
class TaskMarkSubscriber implements EventSubscriberInterface
{
    private AchievementRepository $achievementRepository;
    private UserAchivementRepository $userAchivementRepository;
    private TaskMarkRepository $taskMarkRepository;
    private HubInterface $hub;

    public function __construct(
        AchievementRepository    $achievementRepository,
        TaskMarkRepository    $taskMarkRepository,
        UserAchivementRepository $userAchivementRepository,
        HubInterface $hub
    )
    {
        $this->achievementRepository = $achievementRepository;
        $this->taskMarkRepository = $taskMarkRepository;
        $this->userAchivementRepository = $userAchivementRepository;
        $this->hub = $hub;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate
//            Events::postRemove
        ];
    }


    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->updateAchievements($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->updateAchievements($args);
    }

    private function updateAchievements(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $em = $args->getObjectManager();

        if (!$entity instanceof TaskMark) {
            return;
        }

        // === Отправка уведомления
        //  о прохождении задания пользователем ===
        if ($entity->getRating()) {
            $update = new Update(
                topics: '/tasks',
                data: json_encode(['message' => "Пользователь \"" . $entity->getUser()->getFio() . "\" прошёл ваше задание!"]),
                type: $entity->getTask()->getOwner()->getId()
            );

            $this->hub->publish($update);
        }

        // === Получение достижения ===
        $user = $entity->getUser();

        // Получаем все возможные достижения, относящиеся к заданиям
        $achievements = $this->achievementRepository->findBy(['subjectOfInteraction' => 'задание']);
        $userAchievements = $this->userAchivementRepository->findBy(["user" => $user]);

        $userAchievementsForTask = array_filter(
            $userAchievements,
            fn(UserAchievement $userAchievement): bool => in_array($userAchievement->getAchievement(), $achievements)
        );

        foreach ($userAchievementsForTask as $userAchievement) {
            // Если достижение уже получено, то пропускаем его
            if ($userAchievement->getAchievementDate()) {
                continue;
            }

            $achievement = $userAchievement->getAchievement();

            // Определяем проверку на количество попыток
            $eqTries =
                !$achievement->getNeedTries()
                || $entity->getCountTries() === $achievement->getNeedTries();
            // определяем проверку на оценку
            $eqRating =
                !$achievement->getRating()
                || $entity->getRating() === $achievement->getRating();

            // === Определяем проверку на количество повторов ===
            $taskMarksByUser = $this->taskMarkRepository->findBy(["user" => $user]);
            $trueAchievement = array_filter(
                $taskMarksByUser,
                function (TaskMark $taskMark) use ($userAchievement)
                {
                    // $userAchievement
                    // this TaskMark
                    $achievement = $userAchievement->getAchievement();

                    // Определяем проверку на количество попыток
                    $eqTries =
                        !$achievement->getNeedTries()
                        || $taskMark->getCountTries() === $achievement->getNeedTries();
                    // определяем проверку на оценку
                    $eqRating =
                        !$achievement->getRating()
                        || $taskMark->getRating() === $achievement->getRating();

                    if ($achievement->getTypeOfInteraction() === "прохождение"
                        and $eqTries and $eqRating
                    ) {
                        return $taskMark;
                    } else return null;
                }
            );
            $trueAchievement = array_filter($trueAchievement, function($element) {
                return !empty($element);
            });

            $eqScore =
                !$achievement->getNeedScore() || count($trueAchievement) >= $achievement->getNeedScore();
            // ======

            if ($achievement->getTypeOfInteraction() === "прохождение"
                and $eqTries and $eqRating and $eqScore
            ) {
                $userAchievement->setTotalScore($achievement->getNeedScore());
                $userAchievement->setAchievementDate();

                $em->persist($userAchievement);
                $em->flush();
            }
        }
    }

    // Стоит удалять только те достижения, которые напрямую связаны через id
//    public function postRemove(LifecycleEventArgs $args): void
//    {
//        $object = $args->getObject();
//    }
}