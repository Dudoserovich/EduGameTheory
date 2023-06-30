<?php

namespace App\Previewer;

use App\Entity\User;
use App\Service\FileUploader;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\File\File;

class UserPreviewer
{
    private string $avatarDirectory;
    private FileUploader $fileUploader;

    public function __construct(
        string $avatarDirectory,
        FileUploader $fileUploader
    )
    {
        $this->avatarDirectory = $avatarDirectory;
        $this->fileUploader = $fileUploader;
    }

    #[ArrayShape([
        "id" => "int",
        "full_name" => "string",
        "login" => "string",
        "roles" => "string[]",
        "email" => "string",
        "avatar_name" => "string",
        "avatar_base64" => "string"
    ])]
    public function preview(User $user): array
    {
        $avatar = $this->fileUploader->getImageBase64(
            $this->avatarDirectory,
            $user->getAvatar()
        );

        return [
                "id" => $user->getId(),
                "full_name" => $user->getFio(),
                "login" => $user->getUsername(),
                "roles" => $user->getRoles(),
                "email" => $user->getEmail(),
                "avatar_name" => $user->getAvatar(),
                "avatar_base64" => $avatar
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

    #[ArrayShape([
        "id" => "int",
        "fio" => "string",
        "avatar_name" => "string",
        "avatar_base64" => "string",
    ])]
    public function previewFioAndAvatar(User $user): array
    {
        $avatar = $this->fileUploader->getImageBase64(
            $this->avatarDirectory,
            $user->getAvatar()
        );

        return [
            "id" => $user->getId(),
            "fio" => $user->getFio(),
            "avatar_name" => $user->getAvatar(),
            "avatar_base64" => $avatar
        ];
    }
}