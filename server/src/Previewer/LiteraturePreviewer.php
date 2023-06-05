<?php

namespace App\Previewer;

use App\Entity\Literature;
use JetBrains\PhpStorm\ArrayShape;

class LiteraturePreviewer
{
    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "link" => "string"
    ])]
    public function preview(Literature $literature): array
    {
        return [
            "id" => $literature->getId(),
            "name" => $literature->getName(),
            "description" => $literature->getDescription(),
            "link" => $literature->getLink()
        ];
    }
}