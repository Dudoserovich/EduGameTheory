<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class TermView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Term/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Term/properties/name")
     * @Groups({"default", "name"})
     */
    public string $name;

    /**
     * @OA\Property(property="description", ref="#/components/schemas/Term/properties/description")
     * @Groups({"default", "description"})
     */
    public string $description;
}