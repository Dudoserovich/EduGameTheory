<?php

namespace App\Previewer;

use App\Entity\Literature;
use App\Entity\Topic;
use JetBrains\PhpStorm\ArrayShape;

class TopicPreviewer
{
    #[ArrayShape([
        "id" => "int",
        "name" => "string"
    ])]
    public function preview(Topic $topic): array
    {
        return [
            "id" => $topic->getId(),
            "name" => $topic->getName()
        ];
    }
}