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
     * @OA\Property(property="description", ref="#/components/schemas/Task/properties/description")
     * @Groups({"default", "description"})
     */
    public string $description;

    /**
     * @OA\Property(property="type", ref="#/components/schemas/Task/properties/type")
     * @Groups({"default", "type"})
     */
    public string $type;

    /**
     * @OA\Property(property="topic", ref="#/components/schemas/TaskView")
     * @Groups({"default", "topic"})
     */
    public Topic $topic;

    /**
     * @OA\Property(property="owner", ref="#/components/schemas/UserIdAndFioView")
     * @Groups({"default", "owner"})
     */
    public UserView $owner;

    /**
     * @OA\Property(property="matrix", ref="#/components/schemas/Task/properties/matrix")
     * @Groups({"default", "matrix"})
     */
    public array $matrix;

    /**
     * @OA\Property(property="flag_matrix", ref="#/components/schemas/Task/properties/flagMatrix")
     * @Groups({"default", "flagMatrix"})
     */
    public ?string $flagMatrix;

    /**
     * @OA\Property(property="name_first_player", ref="#/components/schemas/Task/properties/nameFirstPlayer")
     * @Groups({"default", "name_first_player"})
     */
    public ?string $nameFirstPlayer;

    /**
     * @OA\Property(property="name_second_player", ref="#/components/schemas/Task/properties/nameSecondPlayer")
     * @Groups({"default", "name_second_player"})
     */
    public ?string $nameSecondPlayer;

    /**
     * @OA\Property(property="name_first_strategies", ref="#/components/schemas/Task/properties/nameFirstStrategies")
     * @Groups({"default", "name_first_strategies"})
     */
    public ?array $nameFirstStrategies;

    /**
     * @OA\Property(property="name_second_strategies", ref="#/components/schemas/Task/properties/nameSecondStrategies")
     * @Groups({"default", "name_second_strategies"})
     */
    public ?array $nameSecondStrategies;

}