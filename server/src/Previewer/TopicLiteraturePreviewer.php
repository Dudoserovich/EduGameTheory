<?php

namespace App\Previewer;

use App\Entity\Literature;
use App\Entity\TopicLiterature;
use App\Repository\TopicLiteratureRepository;
use App\Service\FileUploader;
use JetBrains\PhpStorm\ArrayShape;

class TopicLiteraturePreviewer
{
    private TopicLiteratureRepository $topicLiteratureRepository;
    private TopicPreviewer $topicPreviewer;
    private FileUploader $fileUploader;
//    private string $imageDirectory;

    public function __construct(
//        string                    $imageDirectory,
        TopicLiteratureRepository $topicLiteratureRepository,
        TopicPreviewer            $topicPreviewer,
        FileUploader              $fileUploader
    )
    {
//        $this->imageDirectory = $imageDirectory;
        $this->topicLiteratureRepository = $topicLiteratureRepository;
        $this->topicPreviewer = $topicPreviewer;
        $this->fileUploader = $fileUploader;
    }

    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "link" => "string",
        "topic" => [[
            "id" => "int",
            "name" => "string"
        ]],
        "image_base64" => "string"
    ])]
    public function preview(Literature $literature): array
    {
        $topicLiteratures = $this->topicLiteratureRepository->findBy(["literature" => $literature]);

        $topics = array_map(
            fn(TopicLiterature $topicLiterature): array => $this->topicPreviewer->preview($topicLiterature->getTopic()),
            $topicLiteratures
        );

        $image = $this->fileUploader->getImageBase64(
            $literature->getImageFile()->getPath(),
            $literature->getImageName()
        );

        return [
            "id" => $literature->getId(),
            "name" => $literature->getName(),
            "description" => $literature->getDescription(),
            "link" => $literature->getLink(),
            "topic" => $topics,
            "image_base64" => $image
        ];
    }

}