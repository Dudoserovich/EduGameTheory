<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskMark;
use App\Previewer\TaskMarkPreviewer;
use App\Previewer\TaskPreviewer;
use App\Repository\TaskMarkRepository;
use App\Repository\TaskRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
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

    public function __construct(
        TaskRepository         $taskRepository,
        UserRepository         $userRepository,
        TaskMarkRepository     $taskMarkRepository,
        EntityManagerInterface $em,
        RequestStack           $requestStack)
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->taskMarkRepository = $taskMarkRepository;
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

        $resultArray =
            [
                "matrix" => $task->getMatrix(),
                "chance_first" => $taskBrownRobinson->getP(),
                "chance_second" => $taskBrownRobinson->getQ(),
                "description" => "Данная игра направлена на попытку сыграть наилучшим образом. За 10 ходов вам нужно попытаться сформировать наилучшую для себя стратегию. Перед вами ваша оптимальная стратегия и противника. Если вы будете играть опрометчиво, то не сможете набрать наилучшее количество очков. Для предоставления информации по вероятностям используется метод Брауна-Робинсона"
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
        // TODO: Хранить результаты не в сессии, а в бд

        $request = $request->request->all();
        $session = $this->requestStack->getSession();

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }
        if ($task->getFlagMatrix() !== 'платёжная матрица') {
            return $this->respondValidationError("Задание не содержит платёжную матрицу");
        }

        $moves = $session->get("task_$taskId")["moves"] ?? [];
        if (count($moves) > 10) {
            $result = $session->get("task_$taskId");
            $resultMessage = "Вы уже прошли это задание";
            $result["success"] = $resultMessage;
            $session->set(
                "task_$taskId",
                $result
            );
            return $this->response(
                $result
            );
        }
        $moves[] = $request['row_number'];

        // Проверка на валидность переданного номера строки
        if (!($request['row_number'] < count($task->getMatrix()) and $request['row_number'] >= 0))
            return $this->respondNotFound("Неверный номер строки");

        // вычисляем результат хода
        $taskBrownRobinson->BraunRobinson($task->getMatrix());
        $chanceSecond = $taskBrownRobinson->getQ();
        $resultMove = TaskPlay::move(
            $task->getMatrix(), $request['row_number'],
            $chanceSecond
        );

        if (is_null($resultMove))
            return $this->respondNotFound("Пустая матрица");

        $score = $session->get("task_$taskId")["score"] ?? 0;
        $score += $resultMove;
        // Подсчитываем вероятности пользователя
        $your_chance = array_count_values($moves);
        ksort($your_chance);
        for ($i = 0; $i < count($task->getMatrix()); $i++) {
            if (!array_key_exists($i, $your_chance))
                $your_chance[$i] = 0;
            else
                $your_chance[$i] /= count($moves);
        }
        $message = null;
        $chanceFirst = null;
        if (count($moves) === 10) {
            // Вычисление самого максимального числа очков
            $max = null;
            foreach ($task->getMatrix() as $row) {
                if (!$max)
                    $max = max($row);
                elseif ($max < max($row))
                    $max = max($row);
            }
            $maxScore = $max * count($moves);

            for ($i = 0; $i < count($taskBrownRobinson->getP()); $i++) {
                if (round($taskBrownRobinson->getP()[$i], 1) !== round($your_chance[$i], 1)) {
                    $message = "Задание пройдено. Вы играли рискованно, поэтому ваша вероятность выбора стратегий сильно расходится с наилучшей. Тем не менее вы набрали $score очков из максимальных $maxScore";
                    break;
                }
            }
            $chanceFirst = $taskBrownRobinson->getP();
        }
        $resultArray =
            [
                "moves" => $moves,
                "chance_first" => $taskBrownRobinson->getP(),
                "chance_second" => $taskBrownRobinson->getQ(),
                "your_chance" => $your_chance,
                "result_move" => $resultMove,
                "score" => $score,
                "success" => $message
            ];
        // сохраняет результаты задачи в сессии
        $session->set(
            "task_$taskId",
            $resultArray
        );

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
        $session = $this->requestStack->getSession();

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        $session->remove("task_$taskId");
        return $this->respondWithSuccess("Задание '{$task->getName()}' перезапущено!");
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
            return $this->response("Вы уже прошли это задание");
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
            } catch (Exception $e) {
                return $this->respondWithErrors($e->getMessage());
            }

            $taskMark->setRating($rating);

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
                    and $resultSolve["min_index"] == $request["min_index"])
                {
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
