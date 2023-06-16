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
     *     description="Permission deinied"
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
     *     description="Permission deinied"
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
     * Получение результата по конкретному заданию
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
    #[Route(
        "/{taskId}/result",
        name: 'get_self_task',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function getSelfTask(
        int $taskId,
        TaskMarkPreviewer $taskMarkPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $task = $this->taskRepository->findOneBy(["owner" => $user, "id" => $taskId]);
        if (!$task) {
            return $this->respondNotFound("Task not found");
        }

        $taskMarksBySelfTask = $this->taskMarkRepository->findBy(["task" => $task]);

        $result = $taskMarkPreviewer->previewByTaskAndArrayTaskMarks(
            $task,
            $taskMarksBySelfTask
        );

        return $this->response($result);
    }

}
