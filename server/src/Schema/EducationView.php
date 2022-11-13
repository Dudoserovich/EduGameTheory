<?php

namespace App\Schema;

use App\Entity\Topic;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class EducationView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Education/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Education/properties/name")
     * @Groups({"default", "name"})
     */
    public int $name;

    /**
     * @OA\Property(property="topic", ref="#/components/schemas/Education/properties/topic")
     * @Groups({"default", "topic"})
     */
    public ?Topic $topic;
}