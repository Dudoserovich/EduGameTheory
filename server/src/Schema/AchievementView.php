<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;

class AchievementView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Achievement/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    #[Property(
        property: "name",
        description: "Название достижения",
        type: "string",
        example: "Хороший человек",
    )]
    /**
     * @Groups({"default", "name"})
     */
    public string $name;

    #[Property(
        property: "description",
        description: "Описание достижения",
        type: "string",
        example: "Достижение за то, что ты хороший человек"
    )]
    /**
     * @Groups({"default", "description"})
     */
    public ?string $description;

    #[Property(
        property: "imageFile",
        description: "Картинка достижения",
        type: "string",
        format: "binary",
    )]
    /**
     * @Groups({"default", "imageFile"})
     */
    public ?File $imageFile;
}