<?php

namespace App\Schema;

use App\Entity\Education;
use App\Entity\User;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class UserEducationView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/UserEducation/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="user", ref="#/components/schemas/UserEducation/properties/user")
     * @Groups({"default", "user"})
     */
    public ?User $user;

    /**
     * @OA\Property(property="edu", ref="#/components/schemas/UserEducation/properties/edu")
     * @Groups({"default", "edu"})
     */
    public ?Education $edu;

    /**
     * @OA\Property(property="rating", ref="#/components/schemas/UserEducation/properties/rating")
     * @Groups({"default", "rating"})
     */
    public ?int $rating;
}