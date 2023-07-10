<?php

namespace App\Controller;

use App\Entity\PlayTask;
use App\Entity\Task;
use App\Entity\TaskMark;
use App\Previewer\TaskMarkPreviewer;
use App\Previewer\TaskPreviewer;
use App\Repository\PlayTaskRepository;
use App\Repository\TaskMarkRepository;
use App\Repository\TaskRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Service\Declension;
use App\Service\Task\TaskBrownRobinson;
use App\Service\Task\TaskPlay;
use App\Service\Task\TaskSolver;
use App\Service\TaskMarkService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

use MathPHP\Exception\BadDataException;
use MathPHP\Exception\IncorrectTypeException;
use MathPHP\Exception\MathException;
use MathPHP\Exception\MatrixException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="UserTask")
 * @Security(name="Bearer")
 */
#[Route('/tasks', name: 'user_tasks_')]
class UserTaskController extends ApiController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private RequestStack $requestStack;
    private TaskMarkRepository $taskMarkRepository;
    private PlayTaskRepository $playTaskRepository;

    public function __construct(
        TaskRepository         $taskRepository,
        UserRepository         $userRepository,
        TaskMarkRepository     $taskMarkRepository,
        PlayTaskRepository     $playTaskRepository,
        EntityManagerInterface $em,
        RequestStack           $requestStack)
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->taskMarkRepository = $taskMarkRepository;
        $this->playTaskRepository = $playTaskRepository;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * Игра по ходам. Получение изначальной информации по игре
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/{taskId}/turns/play',
        name: 'play_task_info',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function playTaskInfo(
        Request           $request,
        int               $taskId,
        TaskBrownRobinson $taskBrownRobinson
    ): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }
        if ($task->getFlagMatrix() !== 'платёжная матрица') {
            return $this->respondValidationError("Задание не содержит платёжную матрицу");
        }

        // вычисляем результат хода
        $taskBrownRobinson->BraunRobinson($task->getMatrix());

        $v = $taskBrownRobinson->getV();
        if ($v >= 0) {
            $vFour = ceil($taskBrownRobinson->getV()) * 4;
            $subMess = "более";
        } else {
            $vFour = floor($taskBrownRobinson->getV()) * 4;
            $subMess = "менее";
        }

        $resultArray =
            [
                "matrix" => $task->getMatrix(),
                "chance_first" => $taskBrownRobinson->getP(),
                "chance_second" => $taskBrownRobinson->getQ(),
                "description" => "Данная игра направлена на попытку сыграть наилучшим образом. 
                За минимальное кол-во ходов вам нужно попытаться сформировать наилучшую для себя стратегию, 
                    набрав $vFour или $subMess очков. 
                Перед вами ваша оптимальная стратегия и противника. 
                Если вы будете играть опрометчиво, то не сможете сыграть оптимальным образом. 
                Для предоставления информации по вероятностям используется метод Брауна-Робинсона.
                "
            ];

        return $this->response($resultArray);
    }

    /**
     * Игра по ходам
     * @OA\RequestBody(
     *     required=true,
     *     description="Суть задания в том, чтобы за 10 ходов попытаться набрать максимально приближённое к оптимальному решение для первого игрока(т.е. пользователю)<br>Сейчас всё хранится в сессии и поэтому есть ошибки",
     *     @OA\JsonContent(
     *         example={
     *              "row_number": 1
     *         },
     *         @OA\Property(property="row_number", type="number")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/{taskId}/turns/play',
        name: 'play_task',
        requirements: ['taskId' => '\d+'],
        methods: ['PUT']
    )]
    public function playTask(
        Request           $request,
        int               $taskId,
        TaskBrownRobinson $taskBrownRobinson
    ): JsonResponse
    {
        $request = $request->request->all();

        // Валидация задания
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }
        if ($task->getFlagMatrix() !== 'платёжная матрица') {
            return $this->respondValidationError("Задание не содержит платёжную матрицу");
        }

        $user = $this->getUserEntity($this->userRepository);

        // Объект игры по заданию
        $playTask = $this->playTaskRepository->findOneBy([
            "user" => $user,
            "task" => $task
        ]);

        if (!$playTask) {
            $playTask = new PlayTask();
            $playTask
                ->setUser($user)
                ->setTask($task);
        }

        // Проверяем прошёл ли пользователь игру по заданию
        if ($playTask->getSuccess()) {
            $resultMessage = "Вы уже прошли это задание";

            return ($this->response(
                array("success" => $resultMessage))
            );
        }

        // Проверка на валидность переданного номера строки
        if (!($request['row_number'] < count($task->getMatrix()) and $request['row_number'] >= 0))
            return $this->respondNotFound("Неверный номер строки");

        $playTask->addMove($request['row_number']);
        $this->em->persist($playTask);

        // вычисляем результат хода
        $taskBrownRobinson->BraunRobinson($task->getMatrix());
        $chanceSecond = $taskBrownRobinson->getQ();
        $resultMove = TaskPlay::move(
            $task->getMatrix(), $request['row_number'],
            $chanceSecond
        );

        if (is_null($resultMove))
            return $this->respondNotFound("Пустая матрица");

        $playTask->addTotalScore($resultMove);
        $this->em->persist($playTask);
        // Подсчитываем вероятности пользователя
        $your_chance = array_count_values($playTask->getMoves());
        ksort($your_chance);
        for ($i = 0; $i < count($task->getMatrix()); $i++) {
            if (!array_key_exists($i, $your_chance))
                $your_chance[$i] = 0;
            else
                $your_chance[$i] /= count($playTask->getMoves());
        }

        $message = null;

        // Вычисление признака конца игры
        $v = $taskBrownRobinson->getV();
        if ($v >= 0) {
            $needScore = ceil($v) * 4;
            $isMore = true;
        } else {
            $needScore = floor($v) * 4;
            $isMore = false;
        }
        $queryEndPlay = $isMore
            ? $playTask->getTotalScores() >= $needScore
            : $playTask->getTotalScores() <= $needScore;

        // проверка на конец игры
        if ($queryEndPlay) {
            for ($i = 0; $i < count($taskBrownRobinson->getP()); $i++) {
                $message = "Задание пройдено. 
                    Вы играли оптимальным способом и набрали "
                    . $playTask->getTotalScores()
                    . " очков за " . count($playTask->getMoves())
                    . " "
                    . Declension::doByThreeForms(
                        count($playTask->getMoves()),
                        "ход",
                        "хода",
                        "ходов"
                    );

                if (round($taskBrownRobinson->getP()[$i], 1) !== round($your_chance[$i], 1)) {
                    $playTask->setSuccess(true);

                    $message = "Задание пройдено. 
                    Вы играли не оптимальным способом. 
                    Тем не менее вы набрали "
                        . $playTask->getTotalScores()
                        . " очков за " . count($playTask->getMoves())
                        . " "
                        . Declension::doByThreeForms(
                            count($playTask->getMoves()),
                            "ход",
                            "хода",
                            "ходов"
                        );
                    break;
                }
            }
        }

        $resultArray =
            [
                "moves" => $playTask->getMoves(),
                "chance_first" => $taskBrownRobinson->getP(),
                "chance_second" => $taskBrownRobinson->getQ(),
                "your_chance" => $your_chance,
                "result_move" => $resultMove,
                "min_score" => min($task->getMatrix()[$request['row_number']]),
                "max_score" => max($task->getMatrix()[$request['row_number']]),
                "score" => $playTask->getTotalScores(),
                "success" => $message
            ];
        $this->em->flush();

        return $this->response($resultArray);
    }

    /**
     * Перезапуск задания по шагам
     *
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/{taskId}/turns/restart',
        name: 'restart_task',
        requirements: ['taskId' => '\d+'],
        methods: ['PUT']
    )]
    public function restartTask(int $taskId): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        // Удаление лишних данных
        $playTask = $this->playTaskRepository->findOneBy([
            "user" => $user,
            "task" => $task
        ]);
        if (!$playTask || !$playTask->getSuccess()) {
            return $this->respondWithErrors("Вы ещё не прошли игру по заданию");
        } else {
            $playTask
                ->setTotalScore(0)
                ->setMoves([])
                ->setSuccess(false);
        }

        return $this->respondWithSuccess("Игра по заданию '{$task->getName()}' перезапущена!");
    }

    /**
     * Получение короткого результата задания (платёжная матрица/матрица последствий)
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/{taskId}/solve',
        name: 'solve_task',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function solveTask(int $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        // Матрица последствий
        if ($task->getFlagMatrix() != 'платёжная матрица') {
            try {
                $solve = TaskSolver::solveRiskMatrix($task->getMatrix());
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException|Exception $e) {
                return $this->respondValidationError($e->getMessage());
            }
        } else {
            // Платёжная матрица
            try {
                $solve = TaskSolver::solvePayoffMatrix($task->getMatrix());
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException|Exception $e) {
                return $this->respondValidationError($e->getMessage());
            }
        }

        return $this->response($solve);
    }

    /**
     * Получение возможных стратегий платёжной матрицы
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              type="string"
     *          )
     *      )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/payoff/strategy',
        name: 'get_all_strategy',
        methods: ['GET']
    )]
    public function getPayoffStrategy(): JsonResponse
    {
        return $this->response(
            array(
                "чистые стратегии",
                "смешанные стратегии"
            )
        );
    }

    /**
     * Получение возможных типов платёжной матрицы
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              type="string"
     *          )
     *      )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/payoff/flagMatrix',
        name: 'get_flag',
        methods: ['GET']
    )]
    public function getPayoffFlag(): JsonResponse
    {
        return $this->response(
            array(
                "платёжная матрица",
                "матрица последствий"
            )
        );
    }

    /**
     * Найдите решение игры с платёжной матрицей
     * @OA\RequestBody(
     *     required=true,
     *     description="**strategy** - строка, равная `чистые стратегии` или `смешанные стратегии`.<br><br>**first_player** и **second_player** - стратегии первого и второго игрока соответственно. Могут быть либо числом, являющимися номером строки и столбца соответвественно для чистых стратегий. Либо массивом из чисел, являющимся массивом вероятностей выбора стратегий для первого и второго игрока соответственно.<br><br>**game_price** - цена игры.",
     *     @OA\JsonContent(
     *         @OA\Property(property="strategy",
     *                      type="string",
     *                      enum={"чистые стратегии", "смешанные стратегии"}
     *         ),
     *         @OA\Property(
     *             property="first_player",
     *             oneOf={
     *                 @OA\Schema(type="number"),
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(type="number")
     *                 )
     *             }
     *         ),
     *         @OA\Property(
     *             property="second_player",
     *             oneOf={
     *                 @OA\Schema(type="number"),
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(type="number")
     *                 )
     *             }
     *         ),
     *         @OA\Property(property="game_price", type="number")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Решение получено"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/{taskId}/solve/payoff',
        name: 'solve_payoff',
        requirements: ['taskId' => '\d+'],
        methods: ['PUT']
    )]
    public function solvePayoff(
        Request $request,
        int     $taskId
    ): JsonResponse
    {
        $request = $request->request->all();

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        $user = $this->getUserEntity($this->userRepository);
        $taskMark = $this->taskMarkRepository
            ->findOneBy(["user" => $user, "task" => $task]);
        if (!$taskMark) {
            $taskMark = new TaskMark();
            $taskMark
                ->setTask($task)
                ->setUser($user)
                ->setCountTries(0);
            $this->em->persist($taskMark);
            $this->em->flush();
        }

        if ($taskMark->getRating()) {
            return $this->response(
                array(
                    "message" => "Вы уже прошли это задание",
                    "success" => false
                )
            );
        }

        $result = null;
        // Платёжная матрица
        if ($task->getFlagMatrix() == 'платёжная матрица') {
            try {
                $result = TaskSolver::comparisionPaymentResult($task->getMatrix(), $request);
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException|Exception $e) {
                return $this->respondValidationError($e->getMessage());
            }
        } else {
            return $this->respondValidationError('У данного задания неверный тип матрицы.');
        }

        $taskMark->incCountTries();

        if ($result['success']) {
            $tries = $taskMark->getCountTries();

            try {
                $rating = TaskMarkService::get($tries);
                $taskMark->setRating($rating);
            } catch (Exception $e) {
                return $this->respondWithErrors($e->getMessage());
            }

            // TODO: Получение полного решения при успешном прохождении
//            try {
//                return $this->response(TaskSolver::solvePayoffMatrix($task->getMatrix(), true));
//            } catch (BadDataException|IncorrectTypeException
//                        |MatrixException|MathException|Exception $e) {
//                return $this->respondValidationError($e->getMessage());
//            }
        }

        $this->em->persist($taskMark);
        $this->em->flush();

        return $this->response($result);
    }

    /**
     * Найдите решение игры с матрицей последствий
     * @OA\RequestBody(
     *     required=true,
     *     description="",
     *     @OA\JsonContent(
     *         @OA\Property(property="min_value", type="number"),
     *         @OA\Property(property="min_index", type="number")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route('/{taskId}/solve/risk',
        name: 'solve_risk',
        requirements: ['taskId' => '\d+'],
        methods: ['PUT']
    )]
    public function solveRisk(Request $request,
                              int     $taskId): JsonResponse
    {
        $request = $request->request->all();

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        $user = $this->getUserEntity($this->userRepository);
        $taskMark = $this->taskMarkRepository
            ->findOneBy(["user" => $user, "task" => $task]);
        if (!$taskMark) {
            $taskMark = new TaskMark();
            $taskMark
                ->setTask($task)
                ->setUser($user)
                ->setCountTries(0);
            $this->em->persist($taskMark);
            $this->em->flush();
        }

        $taskMark->incCountTries();

        $resultMessage = null;
        // Матрица последствий
        if ($task->getFlagMatrix() == 'матрица последствий') {
            try {
                $resultSolve = TaskSolver::solveRiskMatrix($task->getMatrix());

                if ($resultSolve['min_value'] == $request['min_value']
                    and $resultSolve["min_index"] == $request["min_index"]) {
                    $resultMessage = "Вы правильно решили задание!";

                    $tries = $taskMark->getCountTries();
                    try {
                        $rating = TaskMarkService::get($tries);
                    } catch (Exception $e) {
                        return $this->respondWithErrors($e->getMessage());
                    }

                    $taskMark->setRating($rating);

                    $this->em->persist($taskMark);
                    $this->em->flush();
                } else
                    $resultMessage = "Вы неправильно решили задание!";
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException|Exception $e) {
                return $this->respondValidationError($e->getMessage());
            }
        } else {
            return $this->respondValidationError('У данного задания неверный тип матрицы.');
        }

        // TODO: если задание решено правильно, вернуть полное решение игры

        return $this->response($resultMessage);
    }

    /**
     * Получение всех пройденных заданий текущего пользователя
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the achievement"
     *             ),
     *             @OA\Property(
     *                 property="rating",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="count_tries",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
    #[Route(
        '/completed/self',
        name: 'get_completed',
        methods: ['GET']
    )]
    public function getCompletedTasks(
        TaskMarkPreviewer $taskMarkPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $taskMarks = $this->taskMarkRepository->findBy(
            ["user" => $user],
            ['updatedAt' => 'ASC']
        );
        $taskPreviewers = array_map(
            fn(TaskMark $taskMark): array => $taskMarkPreviewer->previewOnlyResult($taskMark),
            $taskMarks
        );

        return $this->response($taskPreviewers);
    }

    /**
     * Получение всех пройденных заданий пользователя по id
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the achievement"
     *             ),
     *             @OA\Property(
     *                 property="rating",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="count_tries",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
    #[Route(
        '/completed/{userId}',
        name: 'get_completed_by_id',
        requirements: ['userId' => '\d+'],
        methods: ['GET']
    )]
    public function getCompletedTasksByUser(
        TaskMarkPreviewer $taskMarkPreviewer,
        int               $userId
    ): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        $taskMarks = $this->taskMarkRepository->findBy(
            ["user" => $user],
            ['updatedAt' => 'ASC']
        );
        $taskPreviewers = array_map(
            fn(TaskMark $taskMark): array => $taskMarkPreviewer->previewOnlyResult($taskMark),
            $taskMarks
        );

        return $this->response($taskPreviewers);
    }
}
