<?php

namespace App\Previewer;

use App\Entity\EducationTasks;
use JetBrains\PhpStorm\ArrayShape;

class EducationTasksPreviewer
{
    private TaskPreviewer $taskPreviewer;
    private EducationPreviewer $educationPreviewer;

    public function __construct(
        TaskPreviewer      $taskPreviewer,
        EducationPreviewer $educationPreviewer,
    )
    {
        $this->taskPreviewer = $taskPreviewer;
        $this->educationPreviewer = $educationPreviewer;
    }

    #[ArrayShape([
        "id" => "int",
        "education" => [
            "id" => "int",
            "name" => "string",
            "description" => "string",
            "topic" => [
                "id" => "int",
                "name" => "string"
            ],
            "conclusion" => "string"
        ],
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
            "flag_matrix" => "string",
            "name_first_player" => "string",
            "name_second_player" => "string",
            "name_first_strategies" => "string[]",
            "name_second_strategies" => "string[]",
        ],
        "block_number" => "int",
        "theory_text" => "string"
    ])]
    public function preview(EducationTasks $eduTasks): array
    {
        return [
            "id" => $eduTasks->getId(),
            "education" => $this->educationPreviewer->preview($eduTasks->getEdu()),
            "task" => $this->taskPreviewer->preview($eduTasks->getTask()),
            "block_number" => $eduTasks->getBlockNumber(),
            "theory_text" => $eduTasks->getTheoryText()
        ];
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
            "flag_matrix" => "string",
            "name_first_player" => "string",
            "name_second_player" => "string",
            "name_first_strategies" => "string[]",
            "name_second_strategies" => "string[]",
        ],
        "block_number" => "int",
        "theory_text" => "string"
    ])]
    public function previewWithoutEdu(EducationTasks $eduTasks): array
    {
        return [
            "id" => $eduTasks->getId(),
            "task" => $this->taskPreviewer->preview($eduTasks->getTask()),
            "block_number" => $eduTasks->getBlockNumber(),
            "theory_text" => $eduTasks->getTheoryText()
        ];
    }

}