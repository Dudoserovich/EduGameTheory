<?php

namespace App\Previewer;

use App\Entity\Task;
use App\Entity\TaskMark;
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
            "avatar" => "string",
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
    public function previewResult(TaskMark $taskMark): array
    {
        return [
            "id" => $taskMark->getId(),
            "rating" => $taskMark->getRating(),
            "count_tries" => $taskMark->getCountTries(),
            "updated_at" => $taskMark->getUpdatedAt()
        ];
    }
}