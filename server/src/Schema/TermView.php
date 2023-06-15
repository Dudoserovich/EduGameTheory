<?php

namespace App\Schema;

use App\Entity\Topic;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Property;
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
    #[Property(example: "Игрок")]
    public string $name;

    /**
     * @OA\Property(property="description", ref="#/components/schemas/Term/properties/description")
     * @Groups({"default", "description"})
     */
    #[Property(example: "Одна из сторон в игровой ситуации")]
    public string $description;

    /**
     * @OA\Property(property="topic", ref="#/components/schemas/TopicView")
     * @Groups({"default", "link"})
     */
//    #[Property()]
    public Topic $topic;
}