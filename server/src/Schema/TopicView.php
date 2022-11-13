<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class TopicView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Topic/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Topic/properties/name")
     * @Groups({"default", "name"})
     */
    public string $name;
}