<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use App\Entity\Task;
use App\Entity\TaskMark;
use App\Entity\Topic;
use App\Entity\User;
use App\Entity\UserAchievement;
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
        [ 'name' => 'Камень-ножницы-бумага',
            'description' => '
            Стандартная игра в камень, ножницы, бумагу.
            Однако, и она имеет математическую интерпретацию.
            
            Данная игра относится к играм, не имеющим конечного числа оптимальных стратегий.
            Играем до 3-х побед, посмотрим кто сможет выиграть.',
            'matrix' => [[0, 1, -1], [-1, 0, 1], [1, -1, 0]],
            'flagMatrix' => self::FLAGS_MATRIX[0]
        ],
        [ 'name' => 'Камень-Ножницы-Бумага-Ящерица-Спок',
            'description' => '
            Усложнённый вариант камень, ножницы, бумага, показанный в сериале "Теория большого взрыва".
            Однако, и она имеет математическую интерпретацию.
            
            Данная игра относится к играм, не имеющим конечного числа оптимальных стратегий.
            Играем до 3-х побед, посмотрим кто сможет выиграть.',
            'matrix' => [[0, -1, 1, 1, -1], [1, 0, -1, -1, 1], [-1, 1, 0, 1, -1],
                            [-1, 1, -1, 0, 1], [1, -1, 1, -1, 0]],
            'flagMatrix' => self::FLAGS_MATRIX[0]
        ],
        ['name' => 'Конкуренция на рынке',
            'description' => '
                В платёжной матрице размером 3x3 указано, какую долю рынка выиграет предприятие у своего единственного конкурента, если оно будет действовать согласно каждой из возможных трех стратегий, а конкурент – согласно каждой из своих возможных трех стратегий. 
                
                Требуется найти оптимальное решение для 1-го и 2-го предприятия, а так же цену игры.
            ',
            'matrix' =>
                [
                    [0.1, 0.4, 0.2],
                    [0.5, 0.4, 0.3],
                    [0.3, 0.2, 0.1]
                ],
            'flagMatrix' => self::FLAGS_MATRIX[0]
        ],
        ['name' => 'Битва за сокровище',
            'description' => '
                Два игрока ищут сокровища и каждый из их может выбрать стратегию, которой он будет придерживаться при поиске клада.
                В платежной матрице размером 2x2 указано, какое количество сокровищ выиграет каждый из двух игроков, если они будут действовать согласно каждой из возможных двух стратегий. 
                
                Требуется найти оптимальное решение для 1-го и 2-го игрока, а так же цену игры.
            ',
            'matrix' =>
                [
                    [4, 7],
                    [5, 3]
                ],
            'flagMatrix' => self::FLAGS_MATRIX[0]
        ],
        ['name' => 'Акционерное общество',
            'description' => '
                Два игрока покупают акции и каждый из них может выбрать одну из трех стратегий, которой он будет придерживаться при скупке акций. 
                В платежной матрице размером 3x3 указано, на сколько пунктов вырастут акции каждого из двух игроков, если они будут действовать согласно каждой из возможных трех стратегий. 
                
                Требуется найти оптимальное решение для первого и второго игроков, а также цену игры. Контекст игры - поиск сокровищ.
            ',
            'matrix' =>
                [
                    [4, 7, 2],
                    [7, 3, 2],
                    [2, 1, 8]
                ],
            'flagMatrix' => self::FLAGS_MATRIX[0]
        ],
        ['name' => 'Страхование груза',
            'description' => '
                Владелец груза должен выбрать одну из двух альтернатив: страховать груз или не страховать. 
                Риск заключается в том, что с вероятностью 0,1 возможна катастрофа, в результате которой груз будет утрачен. 
                Если груз застрахован, то в случае его утраты владелец получает компенсацию его стоимости (100 000 руб.). 
                Стоимость страхового полиса 5000 руб. 
                
                Требуется определить, стоит ли страховать груз?
            ',
            'matrix' =>
                [
                    [0, -1500000],
                    [-100000, -100000]
                ],
            'flagMatrix' => self::FLAGS_MATRIX[1]
        ],
        ['name' => 'SpaceX',
            'description' => '
                Вы - Илон Маск, запускающий ракету на Марс.
                Запуск должен состояться уже сегодня, но что-то пошло не так, есть риск, что запуск пройдёт неудачно.
                
                Какое решение стоит принять на основе матрицы последствий, чтобы снизить риски?
            ',
            'matrix' =>
                [
                    [2, 5, 8, 4],
                    [2, 3, 4, 12],
                    [8, 5, 3, 10],
                    [1, 4, 2, 8]
                ],
            'flagMatrix' => self::FLAGS_MATRIX[1]
        ]
    ];

    /**
     * @param string $str
     * @return string
     */
    private function normalizeDescription(string $str): string
    {
        $rows = explode(PHP_EOL, $str);

        foreach ($rows as &$row) {
            $row = ltrim($row);
        }

        $result = "";
        unset($row);
        array_pop($rows);
        foreach ($rows as $row) {
            $result .= $row . PHP_EOL;
        }

        return trim($result);
    }

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
            $type = $this->faker->randomElement(self::TYPES);

            $taskEntity
                ->setName($task['name'])
                ->setDescription($this->normalizeDescription($task['description']))
                ->setType($type)
                ->setTopic($matrixTopic)
                ->setOwner($type == self::TYPES[1] ? $this->faker->randomElement($owners) : null)
                ->setMatrix($task['matrix'])
                ->setFlagMatrix($task['flagMatrix'])
                ;
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
            UserFixtures::class,
            AchievementFixtures::class
        ];
    }

}