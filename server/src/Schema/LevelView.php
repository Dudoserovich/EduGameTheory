<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

class LevelView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Level/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Level/properties/name")
     * @Groups({"default", "name"})
     */
    public string $name;

    /**
     * @OA\Property(property="need_scores", ref="#/components/schemas/Level/properties/needScores")
     * @Groups({"default", "need_scores"})
     */
    public int $needScores;

}