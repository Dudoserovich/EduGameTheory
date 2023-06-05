<?php

namespace App\Previewer;

use App\Entity\Literature;
use App\Entity\TopicLiterature;
use App\Repository\TopicLiteratureRepository;
use JetBrains\PhpStorm\ArrayShape;

class TopicLiteraturePreviewer
{
    private TopicLiteratureRepository $topicLiteratureRepository;
    private TopicPreviewer $topicPreviewer;

    public function __construct(
        TopicLiteratureRepository $topicLiteratureRepository,
        TopicPreviewer $topicPreviewer
    )
    {
        $this->topicLiteratureRepository = $topicLiteratureRepository;
        $this->topicPreviewer = $topicPreviewer;
    }

    #[ArrayShape([
        "literature" => [
            "id" => "int",
            "name" => "string",
            "link" => "string",
            "topic" => [[
                "id" => "int",
                "name" => "string"
            ]]
        ]
    ])]
    public function preview(Literature $literature): array
    {
        $topicLiteratures = $this->topicLiteratureRepository->findBy(["literature" => $literature]);

        $topics = array_map(
            fn(TopicLiterature $topicLiterature): array => $this->topicPreviewer->preview($topicLiterature->getTopic()),
            $topicLiteratures
        );

        return [
            "literature" =>
            [
                "id" => $literature->getId(),
                "name" => $literature->getName(),
                "link" => $literature->getLink(),
                "topic" => $topics
            ]
        ];
    }

}