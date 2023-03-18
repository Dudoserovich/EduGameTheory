<?php

namespace App\Previewer;

use App\Entity\Achievement;
use JetBrains\PhpStorm\ArrayShape;

class AchievementPreviewer
{
    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "imageName" => "string",
        "imageSize" => "string"
    ])]
    public function preview(Achievement $achievement): array
    {
        return [
            "id" => $achievement->getId(),
            "name" => $achievement->getName(),
            "description" => $achievement->getDescription(),
            "imageName" => $achievement->getImageName(),
            "imageSize" => $achievement->getImageSize()
        ];
    }
}