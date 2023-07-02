<?php

namespace App\Previewer;

use App\Entity\Education;
use App\Entity\User;
use App\Repository\EducationTasksRepository;
use App\Repository\UserEducationTasksRepository;
use JetBrains\PhpStorm\ArrayShape;

class EducationPreviewer
{
    private TopicPreviewer $topicPreviewer;
    private EducationTasksRepository $educationTasksRepository;
    private UserEducationTasksRepository $userEducationTasksRepository;

    public function __construct(
        TopicPreviewer               $topicPreviewer,
        EducationTasksRepository     $educationTasksRepository,
        UserEducationTasksRepository $userEducationTasksRepository,
    )
    {
        $this->topicPreviewer = $topicPreviewer;
        $this->educationTasksRepository = $educationTasksRepository;
        $this->userEducationTasksRepository = $userEducationTasksRepository;
    }

    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "topic" => [
            "id" => "int",
            "name" => "string"
        ],
        "conclusion" => "string"
    ])]
    public function preview(Education $education): array
    {
        return [
            "id" => $education->getId(),
            "name" => $education->getName(),
            "description" => $education->getDescription(),
            "topic" => $this->topicPreviewer->preview($education->getTopic()),
            "conclusion" => $education->getConclusion()
        ];
    }

    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "topic" => [
            "id" => "int",
            "name" => "string"
        ],
        "conclusion" => "string",
        "progress" => [
            "passed" => "int",
            "total" => "int"
        ]
    ])]
    public function previewByEduAndUser(Education $education, User $user): array
    {
        $ETByEdu = $this->educationTasksRepository->findBy([
            "edu" => $education
        ]);

        $passed = 0;
        foreach ($ETByEdu as $eduTasks) {
            $UET = $this->userEducationTasksRepository->findBy([
                "eduTasks" => $eduTasks,
                "user" => $user,
                "success" => true
            ]);

            if ($UET) {
                $passed++;
            }
        }

        return [
            "id" => $education->getId(),
            "name" => $education->getName(),
            "description" => $education->getDescription(),
            "topic" => $this->topicPreviewer->preview($education->getTopic()),
            "conclusion" => $education->getConclusion(),
            "progress" => [
                "passed" => $passed,
                "total" => count($ETByEdu)
            ]
        ];
    }

    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "topic" => [
            "id" => "int",
            "name" => "string"
        ]
    ])]
    public function previewWithoutConclusion(Education $education): array
    {
        return [
            "id" => $education->getId(),
            "name" => $education->getName(),
            "description" => $education->getDescription(),
            "topic" => $this->topicPreviewer->preview($education->getTopic())
        ];
    }
}