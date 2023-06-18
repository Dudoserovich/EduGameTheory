<?php

namespace App\Previewer;

use App\Entity\Task;
use App\Entity\TaskMark;
use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class TaskMarkPreviewer
{
    private UserPreviewer $userPreviewer;
    private TaskPreviewer $taskPreviewer;

    public function __construct(
        UserPreviewer $userPreviewer,
        TaskPreviewer $taskPreviewer
    )
    {
        $this->userPreviewer = $userPreviewer;
        $this->taskPreviewer = $taskPreviewer;
    }

    const RESULT_TEACHER_PREVIEW = [
        "id" => "int",
        "task" => [
            "id" => "int",
            "name" => "string",
            "description" => "string",
            "type" => "string",
            "topic" => [
                "id" => "int",
                "name" => "string"
            ],
            "owner" => [
                "id" => "int",
                "fio" => "string"
            ],
            "matrix" => "float[]",
            "flag_matrix" => "string"
        ],
        "users" => [[
            "id" => "int",
            "full_name" => "string",
            "avatar_name" => "string",
            "avatar_base64" => "string",
            "rating" => "int",
            "count_tries" => "int",
            "updated_at" => "datetime"
        ]]
    ];

    #[ArrayShape([
        "id" => "int",
        "task" => [
            "id" => "int",
            "name" => "string",
            "description" => "string",
            "type" => "string",
            "topic" => [
                "id" => "int",
                "name" => "string"
            ],
            "owner" => [
                "id" => "int",
                "fio" => "string"
            ],
            "matrix" => "float[]",
            "flag_matrix" => "string"
        ],
        "user" => [
            "id" => "int",
            "full_name" => "string",
            "login" => "string",
            "roles" => "string[]",
            "email" => "string",
            "avatar_name" => "string",
            "avatar_base64" => "string"
        ],
        "rating" => "int",
        "count_tries" => "int",
        "updated_at" => "datetime"
    ])]
    public function previewAll(TaskMark $taskMark): array
    {
        return [
            "id" => $taskMark->getId(),
            "task" => $this->taskPreviewer->preview($taskMark->getTask()),
            "user" => $this->userPreviewer->preview($taskMark->getUser()),
            "rating" => $taskMark->getRating(),
            "count_tries" => $taskMark->getCountTries(),
            "updated_at" => $taskMark->getUpdatedAt()
        ];
    }

    #[ArrayShape([
        "id" => "int",
        "rating" => "int",
        "count_tries" => "int",
        "updated_at" => "datetime"
    ])]
    public function previewOnlyResult(TaskMark $taskMark): array
    {
        return [
            "id" => $taskMark->getId(),
            "rating" => $taskMark->getRating(),
            "count_tries" => $taskMark->getCountTries(),
            "updated_at" => $taskMark->getUpdatedAt()
        ];
    }

    #[ArrayShape(self::RESULT_TEACHER_PREVIEW)]
    /**
     * @param Task[] $tasks
     * @param TaskMark[] $taskMarksBySelfTasks
     * @return array
     */
    public function previewByArrayTasksAndTaskMarks(
        array $tasks,
        array $taskMarksBySelfTasks
    ): array
    {
        $result = [];

        foreach ($tasks as $task) {
            // Ищем задание пользователя в пройденных
            //  заданиях пользователями
            $i = 0;
            $taskMarks = [];
            while ($i < count($taskMarksBySelfTasks)) {
                if ($taskMarksBySelfTasks[$i] && $task->getId() === $taskMarksBySelfTasks[$i]->getTask()->getId())
                    $taskMarks[] = $taskMarksBySelfTasks[$i];
                $i++;
            }

            // Формируем результирующий массив
            $users = [];
            $others = [];
            /**
             * @var TaskMark[] $taskMarks
             */
            foreach ($taskMarks as $taskMark) {
                $users[] = $taskMark->getUser();
                $others[] = [
                    "rating" => $taskMark->getRating(),
                    "count_tries" => $taskMark->getCountTries(),
                    "updated_at" => $taskMark->getUpdatedAt()
                ];
            }

            $arrayUsers = [];
            foreach ($users as $key => $user) {
                $userPreview = $this->userPreviewer->previewFioAndAvatar($user);
                $userPreview['task_result'] = [
                    'rating' => $others[$key]['rating'],
                    'count_tries' => $others[$key]['count_tries'],
                    'updated_at' => $others[$key]['updated_at'] ?? null
                ];
                $arrayUsers[] = $userPreview;
            }

            $result[] = [
                "task" => $this->taskPreviewer->preview($task),
                "users" => $arrayUsers
            ];
        }

        return $result;
    }

    #[ArrayShape(self::RESULT_TEACHER_PREVIEW)]
    /**
     * @param Task $task
     * @param TaskMark[] $taskMarksBySelfTask
     * @return array
     */
    public function previewByTaskAndArrayTaskMarks(
        Task $task,
        array $taskMarksBySelfTask
    ): array
    {
        $users = [];
        foreach ($taskMarksBySelfTask as $key => $taskMarkBySelfTask) {
            $users[] = $this->userPreviewer->previewFioAndAvatar($taskMarkBySelfTask->getUser());
            $users[$key]['task_result'] = [
                'rating' => $taskMarkBySelfTask->getRating(),
                'count_tries' => $taskMarkBySelfTask->getCountTries(),
                'updated_at' => $taskMarkBySelfTask->getUpdatedAt()
            ];
        }

        return [
            'task' => $this->taskPreviewer->preview($task),
            'users' => $users
        ];
    }
}