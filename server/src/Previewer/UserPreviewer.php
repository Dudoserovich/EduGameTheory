<?php

namespace App\Previewer;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class UserPreviewer
{

//    public function __construct()
//
//    }

    #[ArrayShape([
        "id" => "int",
        "full_name" => "string",
        "login" => "string",
        "roles" => "string[]",
        "email" => "string",
        "avatar" => "string",
    ])]
    public function preview(User $user): array
    {
        return [
                "id" => $user->getId(),
                "full_name" => $user->getFio(),
                "login" => $user->getUserIdentifier(),
                "roles" => $user->getRoles(),
                "email" => $user->getEmail(),
                "avatar" => $user->getAvatar()
            ];
    }

    #[ArrayShape([
        "id" => "int",
        "fio" => "string"
    ])]
    public function previewOnlyFio(User $user): array
    {
        return [
            "id" => $user->getId(),
            "fio" => $user->getFio(),
        ];
    }
}