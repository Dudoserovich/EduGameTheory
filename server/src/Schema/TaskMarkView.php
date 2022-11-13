<?php

namespace App\Schema;

use App\Entity\User;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class TaskMarkView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/TaskMark/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="user", ref="#/components/schemas/TaskMark/properties/user")
     * @Groups({"default", "user"})
     */
    public ?User $user;

    /**
     * @OA\Property(property="rating", ref="#/components/schemas/TaskMark/properties/rating")
     * @Groups({"default", "rating"})
     */
    public ?int $rating;
}