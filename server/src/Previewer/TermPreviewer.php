<?php

namespace App\Previewer;

use App\Entity\Term;
use App\Entity\TopicLiterature;
use JetBrains\PhpStorm\ArrayShape;

class TermPreviewer
{
    private TopicPreviewer $topicPreviewer;

    public function __construct(
        TopicPreviewer            $topicPreviewer
    )
    {
        $this->topicPreviewer = $topicPreviewer;
    }

    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "topic" => [
            "id" => "int",
            "name" => "string"
        ]
    ])]
    public function preview(Term $term): array
    {
        return [
            "id" => $term->getId(),
            "name" => $term->getName(),
            "description" => $term->getDescription(),
            "topic" => $this->topicPreviewer->preview($term->getTopic())
        ];
    }
}