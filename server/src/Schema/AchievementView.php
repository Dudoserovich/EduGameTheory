<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class AchievementView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Achievement/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Achievement/properties/name")
     * @Groups({"default", "name"})
     */
    public string $name;

    /**
     * @OA\Property(property="description", ref="#/components/schemas/Achievement/properties/description")
     * @Groups({"default", "description"})
     */
    public ?string $description;
}