<?php

namespace App\Previewer;

use App\Entity\Task;
use JetBrains\PhpStorm\ArrayShape;

class TaskPreviewer
{
    private UserPreviewer $userPreviewer;

    public function __construct(UserPreviewer $userPreviewer)
    {
        $this->userPreviewer = $userPreviewer;
    }

    #[ArrayShape([
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
        "flag_matrix" => "string",
        "name_first_player" => "string",
        "name_second_player" => "string",
        "name_first_strategies" => "string[]",
        "name_second_strategies" => "string[]",
    ])]
    public function preview(Task $task): array
    {
        return [
            "id" => $task->getId(),
            "name" => $task->getName(),
            "description" => $task->getDescription(),
            "type" => $task->getType(),
            "topic" => [
                "id" => $task->getTopic()->getId(),
                "name" => $task->getTopic()->getName()
            ],
            "owner" => $task->getOwner() ? $this->userPreviewer->previewOnlyFio($task->getOwner()) : null,
            "matrix" => $task->getMatrix(),
            "flag_matrix" => $task->getFlagMatrix(),
            "name_first_player" => $task->getNameFirstPlayer(),
            "name_second_player" => $task->getNameSecondPlayer(),
            "name_first_strategies" => $task->getNameFirstStrategies(),
            "name_second_strategies" => $task->getNameSecondStrategies(),
        ];
    }
}