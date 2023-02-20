<?php

namespace App\Schema;

use App\Entity\Topic;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class TaskView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Task/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Task/properties/name")
     * @Groups({"default", "name"})
     */
    public string $name;

    /**
     * @OA\Property(property="type", ref="#/components/schemas/Task/properties/type")
     * @Groups({"default", "type"})
     */
    public string $type;

    /**
     * @OA\Property(property="topic", ref="#/components/schemas/Task/properties/topic")
     * @Groups({"default", "topic"})
     */
    public Topic $topic;

    /**
     * @OA\Property(property="owner", ref="#/components/schemas/UserIdAndFioView")
     * @Groups({"default", "owner"})
     */
    public UserView $owner;
}