<?php

namespace App\Previewer;

use App\Entity\Education;
use App\Entity\EducationTasks;
use App\Entity\Task;
use App\Entity\UserEducationTasks;
use JetBrains\PhpStorm\ArrayShape;

class UserEduTasksPreviewer
{
    private UserPreviewer $userPreviewer;
    private EducationTasksPreviewer $educationTasksPreviewer;

    public function __construct(
        UserPreviewer           $userPreviewer,
        EducationTasksPreviewer $educationTasksPreviewer
    )
    {
        $this->userPreviewer = $userPreviewer;
        $this->educationTasksPreviewer = $educationTasksPreviewer;
    }

    public function preview(UserEducationTasks $userEduTasks): array
    {
        return [
            "id" => $userEduTasks->getId(),
            "education_tasks" => $this->educationTasksPreviewer->preview($userEduTasks->getEduTasks()),
            "user" => $this->userPreviewer->previewOnlyFio($userEduTasks->getUser()),
            "current_block" => $userEduTasks->getIsCurrentBlock(),
            "success" => $userEduTasks->getSuccess()
        ];
    }

    public function previewWithoutUserAndEdu(UserEducationTasks $userEduTasks): array
    {
        return [
            "id" => $userEduTasks->getId(),
            "education_tasks" => $this->educationTasksPreviewer->previewWithoutEdu($userEduTasks->getEduTasks()),
            "current_block" => $userEduTasks->getIsCurrentBlock(),
            "success" => $userEduTasks->getSuccess()
        ];
    }

}