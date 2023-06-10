<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AchievementRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use DateTimeInterface;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=AchievementRepository::class)
 * @Gedmo\SoftDeleteable
 */
#[Vich\Uploadable]
class Achievement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $description = null;

    #[Vich\UploadableField(mapping: "achievement", fileNameProperty: "imageName", size: "imageSize")]
    private ?File $imageFile = null;

    /**
     * @ORM\Column(nullable="true")
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $imageName = null;

    /**
     * @ORM\Column(nullable="true")
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?int $imageSize = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     **/
    private string $thumbnail;

    /**
     * @var ?string $subjectOfInteraction название объекта, взаимодействие над которым будет отслеживаться.
     *
     * @ORM\Column(type="string", length=255, options={"default" : "задание"})
     * @OA\Property(enum={"задание", "обучение" , "литература", "термины"})
     * @Groups({"default"})
     */
    private ?string $subjectOfInteraction = "задание";

    /**
     * @var ?string $typeOfInteraction тип взаимодействия с объектом.
     *
     * @ORM\Column(type="string", length=255, options={"default" : "прохождение"})
     * @OA\Property(enum={"создание", "прохождение", "переходы(клики)"})
     * @Groups({"default"})
     */
    private ?string $typeOfInteraction = "прохождение";

    /**
     * Необходимое количество повторений (Пример: Необходимо пройти 10 заданий).
     *
     * @ORM\Column(type="integer", options={"default": 1})
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?int $needScore = 1;

    /**
     * Необходимое количество попыток выполнения.
     *
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?int $needTries = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property(minimum=2, maximum=5)
     * @Groups({"default"})
     */
    private ?int $rating;

    // TODO: На будущее - нужно поле,
    //  которое будет говорить к каким ролям пользователей относятся достижения.
    //  Пока можно создавать достижения только для всех.

    /**
     * @ORM\Column(nullable="true")
     */
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): self
    {
        $this->imageSize = $imageSize;
        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    private ?DateTimeInterface $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function setSubjectOfInteraction(?string $subjectOfInteraction): self
    {
        $this->subjectOfInteraction = $subjectOfInteraction;
        return $this;
    }

    public function getSubjectOfInteraction(): ?string
    {
        return $this->subjectOfInteraction;
    }

    public function setNeedScore(?int $needScore): self
    {
        $this->needScore = $needScore;
        return $this;
    }

    public function getNeedScore(): ?int
    {
        return $this->needScore;
    }

    public function setTypeOfInteraction(?string $typeOfInteraction): self
    {
        $this->typeOfInteraction = $typeOfInteraction;
        return $this;
    }

    public function getTypeOfInteraction(): ?string
    {
        return $this->typeOfInteraction;
    }

    public function setNeedTries(?int $needTries): self
    {
        $this->needTries = $needTries;
        return $this;
    }

    public function getNeedTries(): ?int
    {
        return $this->needTries;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}
