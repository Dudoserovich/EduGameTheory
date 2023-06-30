<?php

namespace App\Previewer;

use App\Entity\Level;
use JetBrains\PhpStorm\ArrayShape;

class LevelPreviewer
{
    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "need_scores" => "int"
    ])]
    public function preview(Level $level): array
    {
        return [
            "id" => $level->getId(),
            "name" => $level->getName(),
            "need_scores" => $level->getNeedScores()
        ];
    }

    #[ArrayShape([
        "name" => "string",
        "need_scores" => "int"
    ])]
    public function previewWithoutId(Level $level): array
    {
        return [
            "name" => $level->getName(),
            "need_scores" => $level->getNeedScores()
        ];
    }
}