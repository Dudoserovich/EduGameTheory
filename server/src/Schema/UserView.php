<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class UserView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/User/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="login", ref="#/components/schemas/User/properties/login")
     * @Groups({"default", "login"})
     */
    public string $login;

    /**
     * @OA\Property(property="email", ref="#/components/schemas/User/properties/email")
     * @Groups({"default", "email"})
     */
    public string $email;

    /**
     * @OA\Property(property="fio", ref="#/components/schemas/User/properties/fio")
     * @Groups({"default", "fio"})
     */
    public string $fio;

    /**
     * @var string[]
     * @OA\Property(property="roles", ref="#/components/schemas/User/properties/roles")
     * @Groups({"default", "roles"})
     */
    public array $roles;
}