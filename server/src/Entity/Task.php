<?php

namespace App\Entity;

use App\Repository\TaskRepository;

use DateTime;
use DateTimeInterface;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @UniqueEntity(fields={"name"}, message="Задание с таким названием уже существует")
 * @Gedmo\SoftDeleteable
 * @ORM\HasLifecycleCallbacks
 */
class Task
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
     * @ORM\Column(type="string", length=255, unique=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=32, options={"default" : "system"})
     * @OA\Property(enum={"system", "teacher"})
     * @Groups({"default"})
     */
    private ?string $type;

    // "matrix", "coop"
    /**
     * @ORM\ManyToOne(targetEntity=Topic::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=Topic::class))
     * @Groups({"default"})
     */
    private ?Topic $topic;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=User::class))
     * @Groups({"default"})
     */
    private ?User $owner;

    /**
     * @ORM\Column(type="json", nullable=false)
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="number")))
     * @Groups({"default"})
     */
    private array $matrix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $nameFirstPlayer = "Игрок 1";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $nameSecondPlayer = "Игрок 2";

    /**
     * @ORM\Column(type="json", nullable=true)
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="string")))
     * @Groups({"default"})
     */
    private ?array $nameFirstStrategies = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="string")))
     * @Groups({"default"})
     */
    private ?array $nameSecondStrategies = null;

    /**
     * @ORM\Column(type="string", length=255, options={"default" : "платёжная матрица"})
     * @OA\Property(enum={"платёжная матрица", "матрица последствий"})
     * @Groups({"default"})
     */
    private ?string $flagMatrix;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    private ?DateTimeInterface $deletedAt;

    /**
     * @ORM\Column(
     *     name="created_at",
     *     type="datetime",
     *     nullable=false,
     *     options={"default" : "1970-01-02 00:00:00"}
     * )
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    protected datetime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /** @param array|null $matrix The JSON to set.
     */
    public function setMatrix(?array $matrix): self
    {
        $this->matrix = $matrix;

        return $this;
    }

    public function getMatrix(): ?array
    {
        return $this->matrix;
    }

    public function getNameFirstPlayer(): ?string
    {
        return $this->nameFirstPlayer;
    }

    public function setNameFirstPlayer(?string $nameFirstPlayer): self
    {
        $this->nameFirstPlayer = $nameFirstPlayer;

        return $this;
    }

    public function getNameSecondPlayer(): ?string
    {
        return $this->nameSecondPlayer;
    }

    public function setNameSecondPlayer(?string $nameSecondPlayer): self
    {
        $this->nameSecondPlayer = $nameSecondPlayer;

        return $this;
    }

    /** @param array|null $nameFirstStrategies The JSON to set.
     */
    public function setNameFirstStrategies(?array $nameFirstStrategies): self
    {
        $this->nameFirstStrategies = $nameFirstStrategies;

        return $this;
    }

    public function getNameFirstStrategies(): ?array
    {
        return $this->nameFirstStrategies;
    }

    /** @param array|null $nameSecondStrategies The JSON to set.
     */
    public function setNameSecondStrategies(?array $nameSecondStrategies): self
    {
        $this->nameSecondStrategies = $nameSecondStrategies;

        return $this;
    }

    public function getNameSecondStrategies(): ?array
    {
        return $this->nameSecondStrategies;
    }

    public function getFlagMatrix(): ?string
    {
        return $this->flagMatrix;
    }

    public function setFlagMatrix(?string $flagMatrix): self
    {
        $this->flagMatrix = $flagMatrix;

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

    /**
     * Gets triggered only on insert
     *
     * @ORM\PrePersist
     */
    public function onPrePersist(): self
    {
        $this->createdAt = new \DateTime("now");
        return $this;
    }
}
