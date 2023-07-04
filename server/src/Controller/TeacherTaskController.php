<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskMark;
use App\Previewer\TaskMarkPreviewer;
use App\Previewer\TaskPreviewer;
use App\Previewer\UserPreviewer;
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

use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="TeacherTask")
 * @Security(name="Bearer")
 */
#[Route('/teacher/tasks', name: 'teacher_tasks_')]
#[IsGranted(
    "ROLE_TEACHER",
    message: "The user must have a role ROLE_TEACHER"
)]
class TeacherTaskController extends ApiController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private TaskMarkRepository $taskMarkRepository;

    public function __construct(
        TaskRepository         $taskRepository,
        UserRepository         $userRepository,
        TaskMarkRepository     $taskMarkRepository,
        EntityManagerInterface $em
    )
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->taskMarkRepository = $taskMarkRepository;
        $this->em = $em;
    }

    /**
     * Получение всех заданий, держатель которых текущий пользователь (преподаватель)
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
     *     description="Доступ запрещён"
     * )
     */
    #[Route(
        "",
        name: 'get_tasks',
        methods: ['GET']
    )]
    public function getTasks(
        TaskPreviewer $taskPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $tasks = $this->taskRepository->findBy(["owner" => $user]);

        $taskPreviewers = array_map(
            fn(Task $task): array => $taskPreviewer->preview($task),
            $tasks
        );

        return $this->response($taskPreviewers);
    }

    /**
     * Получение результатов по заданиям, созданных текущим пользователем (преподавателем)
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     */
    #[Route(
        "/result",
        name: 'get_self_tasks',
        methods: ['GET']
    )]
    public function getSelfTasks(
        TaskMarkPreviewer $taskMarkPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $tasks = $this->taskRepository->findBy(["owner" => $user]);
        $taskMarksBySelfTasks = $this->taskRepository->findSelfTasks($user);

        $result = $taskMarkPreviewer->previewByArrayTasksAndTaskMarks(
            $tasks,
            $taskMarksBySelfTasks
        );

        return $this->response($result);
    }

    /**
     * Получение результата по конкретному своему заданию
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Задание не найдено"
     * )
     */
    #[Route(
        "/{taskId}/result",
        name: 'get_self_task',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function getSelfTask(
        int               $taskId,
        TaskMarkPreviewer $taskMarkPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $task = $this->taskRepository->findOneBy(["owner" => $user, "id" => $taskId]);
        if (!$task) {
            return $this->respondNotFound("Задание не найдено");
        }

        $taskMarksBySelfTask = $this->taskMarkRepository->findBy(["task" => $task]);

        $result = $taskMarkPreviewer->previewByTaskAndArrayTaskMarks(
            $task,
            $taskMarksBySelfTask
        );

        return $this->response($result);
    }

    /**
     * Получение информации о матрице по двумерному массиву и флагу матрицы
     * @OA\RequestBody (
     *     required=true,
     *     description="Получение информации о заданной матрице",
     *     @OA\JsonContent(
     *         example={
     *              "matrix": {{0, 1, 1}, {1, 0, 1}, {1, 1, 0}},
     *              "flag_matrix": "платёжная матрица",
     *         },
     *         @OA\Property(property="matrix", ref="#/components/schemas/TaskView/properties/matrix"),
     *         @OA\Property(property="flag_matrix", ref="#/components/schemas/TaskView/properties/flag_matrix"),
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
     *     description="Не найдено"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Некорректные данные"
     * )
     */
    #[Route('/matrixInfo',
        name: 'get_info_about_matrix',
        methods: ['PUT']
    )]
    public function solveTask(
        Request $request
    ): JsonResponse
    {
        $request = $request->request->all();
        $this->setSoftDeleteable($this->em, false);

        if (!$request['matrix']) {
            return $this->respondValidationError('Задана пустая матрица');
        }
        // Матрица последствий
        if ($request['flag_matrix'] != 'платёжная матрица') {
            try {
                $solve = TaskSolver::solveRiskMatrix($request['matrix']);
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException|Exception $e) {
                return $this->respondValidationError($e->getMessage());
            }
        } else {
            // Платёжная матрица
            try {
                $solve = TaskSolver::solvePayoffMatrix($request['matrix']);
            } catch (BadDataException|IncorrectTypeException
            |MatrixException|MathException|Exception $e) {
                return $this->respondValidationError($e->getMessage());
            }
        }

        $strategy = '';
        $method = '';
        if (!isset($solve['strategy'])) {
            $method = "Лапласа";

            $message = "Создаваемое задание можно будет решить методом **\"$method\"**. ";

        } else {
            if ($solve['strategy'] === 'смешанные стратегии') {
                $strategy = "смешанных стратегиях";
                $method = "Минимакс";
            } elseif ($solve['strategy'] === 'чистые стратегии') {
                $strategy = "чистых стратегиях";
                $method = "Симплекс-метод";
            }
            $message = "Задание задано в **$strategy**. 
                    Решить задание можно с помощью " . "**\"$method" . "а" . '"**. ';
        }

        return $this->response(array("message" => $message));
    }

}
