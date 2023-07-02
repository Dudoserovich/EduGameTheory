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
    public string $name;

    /**
     * @OA\Property(property="description", ref="#/components/schemas/Education/properties/description")
     * @Groups({"default", "description"})
     */
    public ?string $description;

    /**
     * @OA\Property(property="topic", ref="#/components/schemas/Education/properties/topic")
     * @Groups({"default", "topic"})
     */
    public ?Topic $topic;

    /**
     * @OA\Property(property="conclusion", ref="#/components/schemas/Education/properties/conclusion")
     * @Groups({"default", "conclusion"})
     */
    public ?string $conclusion;
}