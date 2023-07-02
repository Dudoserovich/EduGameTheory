<?php

namespace App\Schema;

use App\Entity\Topic;
use OpenApi\Attributes\Property;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class LiteratureView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/Achievement/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/Achievement/properties/name")
     * @Groups({"default", "name"})
     */
    #[Property(example: "Основные понятия теории игр")]
    public string $name;

    /**
     * @OA\Property(property="description", ref="#/components/schemas/Achievement/properties/description")
     * @Groups({"default", "description"})
     */
    #[Property(example: "В ресурсе изложены базовые понятия и положения теории игр")]
    public string $description;

    /**
     * @OA\Property(property="link", ref="#/components/schemas/Achievement/properties/link")
     * @Groups({"default", "link"})
     */
    #[Property(example: "https://elar.urfu.ru/bitstream/10995/43897/1/978-5-7996-1940-4_2016.pdf")]
    public string $link;

    /**
     * @OA\Property(property="topic", ref="#/components/schemas/TopicView")
     * @Groups({"default", "link"})
     */
//    #[Property()]
    public Topic $topic;

    /**
     * @OA\Property(property="image_base64")
     * @Groups({"default", "image_base64"})
     */
    #[Property(example: "data:image/png;base64...")]
    public string $imageBase64;
}