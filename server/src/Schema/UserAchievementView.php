<?php

namespace App\Schema;

use App\Entity\Achievement;
use App\Entity\User;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class UserAchievementView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/UserAchievement/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="achievement", ref="#/components/schemas/UserAchievement/properties/achievement")
     * @Groups({"default", "achievement"})
     */
    public ?Achievement $achievement;

    /**
     * @OA\Property(property="user", ref="#/components/schemas/UserAchievement/properties/user")
     * @Groups({"default", "user"})
     */
    public ?User $user;
}