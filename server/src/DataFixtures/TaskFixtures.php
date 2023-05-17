<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\TaskMark;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends BaseFixtureAbstract implements DependentFixtureInterface
{
    public const TYPES = [
        'system',
        'teacher'
    ];

    const FLAGS_MATRIX = [
        'платёжная матрица',
        'матрица последствий'
    ];

    public const TASKS = [
//        [ 'name' => 'Камень-ножницы-бумага',
//            'description' => 'Стандартная игра в камень, ножницы, бумагу.
//                                Однако, и она имеет математическую интерпретацию.
//                                Играем до 3-х побед, посмотрим кто сможет выиграть',
//            'initPoints' => 3,
//            'matrix' => [[0, 1, -1], [-1, 0, 1], [1, -1, 0]],
//            'flagMatrix' => self::FLAGS_MATRIX[0],
//            'chance' => null
//        ],
//        [ 'name' => 'Камень-Ножницы-Бумага-Ящерица-Спок',
//            'description' => 'Усложнённый вариант камень, ножницы, бумага, показанный в сериале "Теория большого взрыва".
//                                Однако, и она имеет математическую интерпретацию.
//                                Играем до 3-х побед, посмотрим кто сможет выиграть',
//            'initPoints' => 3,
//            'matrix' => [[0, -1, 1, 1, -1], [1, 0, -1, -1, 1], [-1, 1, 0, 1, -1],
//                            [-1, 1, -1, 0, 1], [1, -1, 1, -1, 0]],
//            'flagMatrix' => self::FLAGS_MATRIX[0],
//            'chance' => null
//        ]
        ['name' => 'Битва за сокровище',
            'description' => '
                Какое-то крутое описание...
                
                Тебе необходимо найти смешанные стратегии первого и второго игрока.
            ',
            'initScores' => 0,
            'matrix' => [[4, 7], [5, 3]],
            'flagMatrix' => self::FLAGS_MATRIX[0],
            'chance' => null
        ],
        ['name' => 'Триада возможностей',
            'description' => '
                Какое-то крутое описание...
                
                Тебе необходимо найти смешанные стратегии первого и второго игрока.
            ',
            'initScores' => 0,
            'matrix' => [[4, 7, 2], [7, 3, 2], [2, 1, 8]],
            'flagMatrix' => self::FLAGS_MATRIX[0],
            'chance' => null
        ],
        ['name' => 'Страхование груза',
            'description' => '
                Владелец груза должен выбрать одну из двух альтернатив: страховать груз или не страховать. Риск заключается в том, что с вероятностью 0,1 возможна катастрофа, в результате которой груз будет утрачен. Если груз застрахован, то в случае его
                    утраты владелец получает компенсацию его стоимости (100 000 руб.). Стоимость страхового полиса 5000 руб. Требуется определить, стоит ли страховать груз?
            ',
            'initScores' => 0,
            'matrix' => [[0, -1500000], [-100000, -100000]],
            'flagMatrix' => self::FLAGS_MATRIX[0],
            'chance' => null
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
         * @var User[] $users
         */
        $topics = $this->getReferencesByEntityClass(Topic::class);
        $users = $this->getReferencesByEntityClass(User::class);

        $matrixTopic = null;
        foreach ($topics as $topic) {
            if ("Матричные игры" === $topic->getName())
                $matrixTopic = $topic;
        }

        $owners = array_filter(
            $users,
            fn(User $user): bool => in_array("ROLE_TEACHER", $user->getRoles()) ||
                in_array("ROLE_ADMIN", $user->getRoles())
        );

        foreach (self::TASKS as $task) {
            $taskEntity = new Task();
            $taskEntity
                ->setName($task['name'])
                ->setDescription($task['description'])
                ->setType($this->faker->randomElement(self::TYPES))
                ->setTopic($matrixTopic)
                ->setOwner($this->faker->randomElement($owners))
                ->setInitScores($task['initScores'])
                ->setMatrix($task['matrix'])
                ->setFlagMatrix($task['flagMatrix'])
                ->setChance($task['chance']);
            $manager->persist($taskEntity);
            $this->saveReference($taskEntity);
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
            TopicFixtures::class,
            UserFixtures::class
        ];
    }

}