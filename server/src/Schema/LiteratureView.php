<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class LiteratureView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/LiteratureView/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/LiteratureView/properties/name")
     * @Groups({"default", "name"})
     */
    public string $name;

    /**
     * @OA\Property(property="link", ref="#/components/schemas/LiteratureView/properties/link")
     * @Groups({"default", "link"})
     */
    public string $link;
}