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
        "image_href" => "string"
    ])]
    public function preview(Achievement $achievement): array
    {
        return [
            "id" => $achievement->getId(),
            "name" => $achievement->getName(),
            "description" => $achievement->getDescription(),
            "image_href" => $achievement->getThumbnail()
        ];
    }
}