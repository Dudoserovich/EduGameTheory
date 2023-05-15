<?php

namespace App\Controller;

use App\Entity\Task;
use App\Previewer\TaskPreviewer;
use App\Repository\TaskRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Service\Task\TaskPlay;
use App\Service\Task\TaskSolver;
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
 * @OA\Tag(name="Task")
 * @Security(name="Bearer")
 */
#[Route('/tasks', name: 'tasks_')]
class TaskController extends ApiController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private RequestStack $requestStack;

    public function __construct(
        TaskRepository $taskRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        RequestStack $requestStack)
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * Get all tasks
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/TaskView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getTasks(TaskPreviewer $taskPreviewer): JsonResponse
    {
        $taskPreviewers = array_map(
            fn(Task $task): array => $taskPreviewer->preview($task),
            $this->taskRepository->findBy([], ["name" => "ASC"])
        );

        return $this->response($taskPreviewers);
    }

    /**
     * Add new task
     * @OA\RequestBody (
     *     required=true,
     *     description="Добавление задания преподавателем.
     *                  Поля `description`, `init_points`, `matrix`
     *                      и `chance` являются не обязательными.  <br><br>
     *                  **init_scores** - кол-во очков для выигрыша. <br>
     *                  **matrix** - платёжная матрица или матрица последствий. <br>
     *                  **flag_matrix** - флаг, указывающий какая матрица находится во входных данных. <br>
     *                  **chance** - массив состояний для 1-го и 2-го игрока.",
     *     @OA\JsonContent(
     *         example={
     *              "name": "Задание #1",
     *              "description": "Невероятно крутое задание",
     *              "init_scores": 3,
     *              "matrix": {{0, 1, 1}, {1, 0, 1}, {1, 1, 0}},
     *              "flag_matrix": "платёжная матрица",
     *              "chance": {{0.3, 0.2, 0.5}, {0.5, 0.4, 0.1}},
     *              "topic_id": 1
     *         },
     *         @OA\Property(property="name", ref="#/components/schemas/TaskView/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/TaskView/properties/description"),
     *         @OA\Property(property="init_scores", ref="#/components/schemas/TaskView/properties/initScores"),
     *         @OA\Property(property="matrix", ref="#/components/schemas/TaskView/properties/matrix"),
     *         @OA\Property(property="flag_matrix", ref="#/components/schemas/TaskView/properties/flagMatrix"),
     *         @OA\Property(property="chance", ref="#/components/schemas/TaskView/properties/chance"),
     *         @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Task added successfully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postTask(Request $request,
                             TopicRepository $topicRepository): JsonResponse
    {
        $request = $request->request->all();

//        $this->setSoftDeleteable(false);
        $task = $this->taskRepository->findOneBy(['name' => $request['name']]);
        if ($task)
            return $this->respondValidationError('A Task with such name has already been created');

        $topic = $topicRepository->find($request['topic_id']);
        if (!$topic) {
            return $this->respondNotFound("Topic not found");
        }

        $task = new Task();
        try {
            $creatorUser = $this->getUserEntity($this->userRepository);

            // Если нам пришёл какой-либо запрос,
            //  то это значит, что создатель - преподаватель
            $task
                ->setName($request['name'])
                ->setDescription($request['description'])
                ->setType("teacher")
                ->setTopic($topic)
                ->setOwner($creatorUser)
                ->setInitScores($request['init_scores'] ?? 0)
                ->setMatrix($request['matrix'] ?? null)
                ->setFlagMatrix($request['flag_matrix'])
                // TODO: Добавить проверку, что в сумме элементы дают 1
                //  или они все равны 1
                ->setChance($request['chance'] ?? null)
            ;
            $this->em->persist($task);

            $this->em->flush();
            return $this->respondWithSuccess("Task added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Task object
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/TaskView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Task not found"
     * )
     */
    #[Route('/{taskId}',
        name: 'get_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['GET'])
    ]
    public function getTask(
        TaskPreviewer $taskPreviewer,
        int $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Task not found");
        }

        return $this->response($taskPreviewer->preview($task));
    }

    /**
     * Change field of task
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         example={
     *              "name": "Задание #1",
     *              "description": "Невероятно крутое задание",
     *              "init_scores": 3,
     *              "matrix": {{0, 1, 1}, {1, 0, 1}, {1, 1, 0}},
     *              "flag_matrix": "платёжная матрица",
     *              "chance": {{0.3, 0.2, 0.5}, {0.5, 0.4, 0.1}},
     *              "topic_id": 1
     *         },
     *         @OA\Property(property="name", ref="#/components/schemas/TaskView/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/TaskView/properties/description"),
     *         @OA\Property(property="init_scores", ref="#/components/schemas/TaskView/properties/initScores"),
     *         @OA\Property(property="matrix", ref="#/components/schemas/TaskView/properties/matrix"),
     *         @OA\Property(property="flag_matrix", ref="#/components/schemas/TaskView/properties/flagMatrix"),
     *         @OA\Property(property="chance", ref="#/components/schemas/TaskView/properties/chance"),
     *         @OA\Property(property="new_topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Task updated successfully"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Task not found"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     */
    #[Route('/{taskId}',
        name: 'put_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['PUT']
    )]
    public function upTask(Request $request,
                                 int $taskId,
                                 TopicRepository $topicRepository): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task)
            return $this->respondNotFound("Task not found");

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $task->setName($request['name']);
            }
            if (isset($request['description'])) {
                $task->setDescription($request['description']);
            }
            if (isset($request['init_points'])) {
                $task->setInitScores($request['init_scores']);
            }
            if (isset($request['matrix'])) {
                $task->setMatrix($request['matrix']);
            }
            if (isset($request['flag_matrix'])) {
                $task->setMatrix($request['flag_matrix']);
            }

            // TODO: Добавить проверку, что в сумме элементы дают 1
            //  или они все равны 1
            if (isset($request['chance'])) {
                $task->setMatrix($request['chance']);
            }
            if (isset($request['new_topic_id'])) {
                $topic = $topicRepository->find($request['new_topic_id']);

                if (!$topic)
                    return $this->respondNotFound("Topic not found");
                else {
                    $task->setTopic($topic);
                }
            }

            $this->em->flush();

            return $this->respondWithSuccess("Task updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete task
     * @OA\Response(
     *     response=200,
     *     description="Task deleted successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Task not found"
     * )
     */
    #[Route('/{taskId}',
        name: 'delete_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['DELETE']
    )]
    public function delTask(int $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Task not found");
        }

        $this->em->remove($task);
        $this->em->flush();

        return $this->respondWithSuccess("Task deleted successfully");
    }

//    /**
//     * Игра по ходам
//     * @OA\RequestBody(
//     *     required=true,
//     *     @OA\JsonContent(
//     *         example={
//     *              "row_number": 1
//     *         },
//     *         @OA\Property(property="row_number", type="number")
//     *     )
//     * )
//     * @OA\Response(
//     *     response=200,
//     *     description="Ход сделан"
//     * )
//     * @OA\Response(
//     *     response=403,
//     *     description="Permission denied!"
//     * )
//     * @OA\Response(
//     *     response=404,
//     *     description="Task not found"
//     * )
//     */
//    #[Route('/{taskId}/play',
//        name: 'play_task',
//        requirements: ['taskId' => '\d+'],
//        methods: ['PUT']
//    )]
//    public function playTask(Request $request, int $taskId): JsonResponse
//    {
//        $request = $request->request->all();
//        $session = $this->requestStack->getSession();
//
//        $task = $this->taskRepository->find($taskId);
//        if (!$task) {
//            return $this->respondNotFound("Task not found");
//        }
//
//        // получает атрибуты по имени
//        $success = $session->get("task_$taskId")["success"] ?? 0;
//        $fail = $session->get("task_$taskId")["fail"] ?? 0;
//
//        $task_result = $session->get("task_$taskId")["task_result"] ?? null;
////        $session->remove("task_$taskId");
//        // Проверяем есть ли конечный результат по заданию у пользователя
//        if ($task_result)
//            return $this->respondWithSuccess("Вы уже проходили это задание. $task_result");
//
//        $moves = $session->get("task_$taskId")["moves"] ?? [];
//        $moves[] = $request['row_number'];
//
//        // Проверка на валидность переданного номера строки
//        if (!($request['row_number'] <= count($task->getMatrix()) and $request['row_number'] > 0))
//            return $this->respondNotFound("Row number does not exist");
//
//        // вычисляем результат хода
//        $resultMove = TaskPlay::move(
//            $task->getMatrix(), $request['row_number'],
//            $task->getChance()
//        );
//
//        if (is_null($resultMove))
//            return $this->respondNotFound("Matrix or array of chance is empty");
//        elseif ($resultMove < 0) {
//            $fail += 1;
//            $resultMessage = 'Проигрыш!';
//        } elseif ($resultMove == 0) {
//            $resultMessage = 'Ничья!';
//        } else {
//            $success += 1;
//            $resultMessage = 'Выигрыш!';
//        }
//
//        // Так как мы играем до какого-то количества побед,
//        //  то нам нужно рассчитать когда пользователь выиграл, а когда проиграл
//        $scores = $success - $fail;
//        $initScores = $task->getInitScores();
//        $taskResult = null;
//        if ($success >= $initScores and $scores > 0) {
//            $taskResult = "Вы прошли задание!";
//        } elseif ($fail >= $initScores and $scores > 0) {
//            $taskResult = "Задание провалено!";
//        }
//
//        $resultArray =
//            ["success" => $success,
//                "fail" => $fail,
//                "moves" => $moves,
//                "task_result" => $taskResult
//            ];
//        // сохраняет результаты задачи в сессии
//        $session->set(
//            "task_$taskId",
//            $resultArray
//        );
//
//        $resultArray['turn_result'] = $resultMessage;
////        $resultArray['task_result'] = $taskResult;
//        return $this->response($resultArray);
//    }
//
//    /**
//     * Перезапуск задания
//     *
//     * @OA\Response(
//     *     response=200,
//     *     description="Ход сделан"
//     * )
//     * @OA\Response(
//     *     response=403,
//     *     description="Permission denied!"
//     * )
//     * @OA\Response(
//     *     response=404,
//     *     description="Task not found"
//     * )
//     */
//    #[Route('/{taskId}/restart',
//        name: 'restart_task',
//        requirements: ['taskId' => '\d+'],
//        methods: ['PUT']
//    )]
//    public function restartTask(int $taskId): JsonResponse
//    {
//        $session = $this->requestStack->getSession();
//
//        $task = $this->taskRepository->find($taskId);
//        if (!$task) {
//            return $this->respondNotFound("Task not found");
//        }
//
//        $task_result = $session->get("task_$taskId")["task_result"] ?? null;
//        // Проверяем есть ли конечный результат по заданию у пользователя
//        if ($task_result) {
//            $session->remove("task_$taskId");
//            return $this->respondWithSuccess("Задание '{$task->getName()}' перезапущено!");
//        } else {
//            return $this->respondWithSuccess("Задание '{$task->getName()}' ещё не пройдено");
//        }
//
//    }

    /**
     * Поиск оптимальных стратегий
     * @OA\Response(
     *     response=200,
     *     description="Решение получено"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Task not found"
     * )
     */
    #[Route('/{taskId}/optimal',
        name: 'optimal_task',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function optimalTask(Request $request, int $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Task not found");
        }

        // Матрица последствий
        if ($task->getFlagMatrix() != 'платёжная матрица') {
            try {
                $solve = TaskSolver::solveRiskMatrix($task->getMatrix());
            } catch (BadDataException | IncorrectTypeException
            | MatrixException | MathException $e) {
                return $this->respondValidationError($e->getMessage());
            }
        } else {
            // Платёжная матрица
            try {
                $solve = TaskSolver::solvePayoffMatrix($task->getMatrix());
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException $e) {
                return $this->respondValidationError($e->getMessage());
            }
        }

        return $this->response($solve);
    }
}
