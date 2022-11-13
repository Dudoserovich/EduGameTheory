<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use DateTimeInterface;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @UniqueEntity(fields={"name"}, message="A task with this name already exists")
 * @Gedmo\SoftDeleteable
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
     * @ORM\Column(type="string", length=32, unique=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private string $name;

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

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
