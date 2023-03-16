<?php

namespace App\Schema;

use OpenApi\Annotations as OA;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

class LiteratureView
{
    /**
     * @OA\Property(property="id", ref="#/components/schemas/LiteratureView/properties/id")
     * @Groups({"default", "id"})
     */
    public int $id;

    /**
     * @OA\Property(property="name", ref="#/components/schemas/LiteratureView/properties/name")
     * @Groups({"default", "name"})
     */
    #[Property(example: "Основные понятия теории игр")]
    public string $name;

    /**
     * @OA\Property(property="link", ref="#/components/schemas/LiteratureView/properties/link")
     * @Groups({"default", "link"})
     */
    #[Property(example: "https://elar.urfu.ru/bitstream/10995/43897/1/978-5-7996-1940-4_2016.pdf")]
    public string $link;
}