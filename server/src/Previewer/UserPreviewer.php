<?php

namespace App\Previewer;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserPreviewer
{
    private function getLink(string $pathImage): string
    {
        return 'http://localhost/api/uploads/avatar/' . $pathImage;
    }

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
                "avatar" => $this->getLink($user->getAvatar())
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