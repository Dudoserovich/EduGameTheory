<?php

namespace App\Schema;

use App\Entity\Education;
use App\Entity\Task;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class EducationTasksView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/EducationTasks/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="edu", ref="#/components/schemas/EducationTasks/properties/edu")
     * @Groups({"default", "edu"})
     */
    public ?Education $edu;

    /**
     * @OA\Property(property="task", ref="#/components/schemas/EducationTasks/properties/task")
     * @Groups({"default", "task"})
     */
    public ?Task $task;

    /**
     * @OA\Property(property="theory_text", ref="#/components/schemas/EducationTasks/properties/theoryText")
     * @Groups({"default", "theory_text"})
     */
    public ?string $text;
}