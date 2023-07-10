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
     * Получение всех заданий
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
     *     description="Доступ запрещен"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getTasks(TaskPreviewer $taskPreviewer): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $taskPreviewers = array_map(
            fn(Task $task): array => $taskPreviewer->previewWithRating($task, $user),
            $this->taskRepository->findBy([])
        );

        return $this->response($taskPreviewers);
    }

    /**
     * Добавление нового задания
     * @OA\RequestBody (
     *     required=true,
     *     description="Добавление задания преподавателем.
     *                  Поля `description`, `name_first_player`, `name_second_player`, `name_first_strategies`, `name_second_strategies` являются не обязательными.  <br><br>
     *                  **matrix** - платёжная матрица или матрица последствий. <br>
     *                  **flag_matrix** - флаг, указывающий какая матрица находится во входных данных. ",
     *     @OA\JsonContent(
     *         example={
     *              "name": "Задание #1",
     *              "description": "Невероятно крутое задание",
     *              "matrix": {{0, 1, 1}, {1, 0, 1}, {1, 1, 0}},
     *              "flag_matrix": "платёжная матрица",
     *              "topic_id": 1
     *         },
     *         @OA\Property(property="name", ref="#/components/schemas/TaskView/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/TaskView/properties/description"),
     *         @OA\Property(property="matrix", ref="#/components/schemas/TaskView/properties/matrix"),
     *         @OA\Property(property="flag_matrix", ref="#/components/schemas/TaskView/properties/flag_matrix"),
     *         @OA\Property(property="name_first_player", ref="#/components/schemas/TaskView/properties/name_first_player"),
     *         @OA\Property(property="name_second_player", ref="#/components/schemas/TaskView/properties/name_second_player"),
     *         @OA\Property(property="name_first_strategies", ref="#/components/schemas/TaskView/properties/name_first_strategies"),
     *         @OA\Property(property="name_second_strategies", ref="#/components/schemas/TaskView/properties/name_second_strategies"),
     *         @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Задание успешно добавлено"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * * @OA\Response(
     *     response=422,
     *     description="Некорректные данные"
     * )
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postTask(Request         $request,
                             TopicRepository $topicRepository): JsonResponse
    {
        $request = $request->request->all();

        $this->setSoftDeleteable($this->em, false);
        $task = $this->taskRepository->findOneBy(['name' => $request['name']]);
        if ($task)
            if (!$task->getDeletedAt())
                return $this->respondValidationError('Задание с таким названием уже существует');
            else $task->setDeletedAt(null);

        $topic = $topicRepository->find($request['topic_id']);
        if (!$topic) {
            return $this->respondNotFound("Тип задания не найден");
        }

        $task = $task ?? new Task();
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
                ->setMatrix($request['matrix'] ?? null)
                ->setFlagMatrix($request['flag_matrix']);

            if (isset($request['name_first_player']))
                $task->setNameFirstPlayer($request['name_first_player']);
            if (isset($request['name_second_player']))
                $task->setNameFirstPlayer($request['name_first_player']);
            if (isset($request['name_first_strategies']))
                $task->setNameFirstPlayer($request['name_first_strategies']);
            if (isset($request['name_second_strategies']))
                $task->setNameFirstPlayer($request['name_second_strategies']);

            $this->em->persist($task);

            $this->em->flush();
            return $this->respondWithSuccess("Задание успешно добавлено");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Получение конкретного объекта задания
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/TaskView")
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
    #[Route('/{taskId}',
        name: 'get_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['GET'])
    ]
    public function getTask(
        TaskPreviewer $taskPreviewer,
        int           $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        return $this->response($taskPreviewer->preview($task));
    }

    /**
     * Изменение атрибутов задания
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         example={
     *              "name": "Задание #1",
     *              "description": "Невероятно крутое задание",
     *              "matrix": {{0, 1, 1}, {1, 0, 1}, {1, 1, 0}},
     *              "flag_matrix": "платёжная матрица",
     *              "topic_id": 1
     *         },
     *         @OA\Property(property="name", ref="#/components/schemas/TaskView/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/TaskView/properties/description"),
     *         @OA\Property(property="matrix", ref="#/components/schemas/TaskView/properties/matrix"),
     *         @OA\Property(property="flag_matrix", ref="#/components/schemas/TaskView/properties/flag_matrix"),
     *         @OA\Property(property="name_first_player", ref="#/components/schemas/TaskView/properties/name_first_player"),
     *         @OA\Property(property="name_second_player", ref="#/components/schemas/TaskView/properties/name_second_player"),
     *         @OA\Property(property="name_first_strategies", ref="#/components/schemas/TaskView/properties/name_first_strategies"),
     *         @OA\Property(property="name_second_strategies", ref="#/components/schemas/TaskView/properties/name_second_strategies"),
     *         @OA\Property(property="new_topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Задание изменено"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Некорректные данные"
     * )
     */
    #[Route('/{taskId}',
        name: 'put_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['PUT']
    )]
    public function upTask(Request         $request,
                           int             $taskId,
                           TopicRepository $topicRepository): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task)
            return $this->respondNotFound("Задание не найдено");

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $task->setName($request['name']);
            }
            if (isset($request['description'])) {
                $task->setDescription($request['description']);
            }
            if (isset($request['matrix'])) {
                $task->setMatrix($request['matrix']);
            }
            if (isset($request['flag_matrix'])) {
                $task->setFlagMatrix($request['flag_matrix']);
            }

            if (isset($request['new_topic_id'])) {
                $topic = $topicRepository->find($request['new_topic_id']);

                if (!$topic)
                    return $this->respondNotFound("Тип задания не найден");
                else {
                    $task->setTopic($topic);
                }
            }

            if (isset($request['name_first_player']))
                $task->setNameFirstPlayer($request['name_first_player']);
            if (isset($request['name_second_player']))
                $task->setNameFirstPlayer($request['name_first_player']);
            if (isset($request['name_first_strategies']))
                $task->setNameFirstPlayer($request['name_first_strategies']);
            if (isset($request['name_second_strategies']))
                $task->setNameFirstPlayer($request['name_second_strategies']);

            $this->em->flush();

            return $this->respondWithSuccess("Задание изменено");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Удаление задания
     * @OA\Response(
     *     response=200,
     *     description="Задание успешно удалено"
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
    #[Route('/{taskId}',
        name: 'delete_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['DELETE']
    )]
    public function delTask(int $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        $this->em->remove($task);
        $this->em->flush();

        return $this->respondWithSuccess("Задание успешно удалено");
    }
}
