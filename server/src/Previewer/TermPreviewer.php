<?php

namespace App\Previewer;

use App\Entity\Term;
use JetBrains\PhpStorm\ArrayShape;

class TermPreviewer
{
    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string"
    ])]
    public function preview(Term $term): array
    {
        return [
            "id" => $term->getId(),
            "name" => $term->getName(),
            "description" => $term->getDescription()
        ];
    }
}