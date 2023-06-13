<?php

namespace App\Entity;

use App\Repository\TaskMarkRepository;

use DateTime;
use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=TaskMarkRepository::class)
 * @Gedmo\SoftDeleteable
 * @ORM\HasLifecycleCallbacks
 */
class TaskMark
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
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @OA\Property(ref=@Model(type=User::class))
     * @Groups({"default"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=Task::class))
     * @Groups({"default"})
     */
    private ?Task $task;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property(minimum=2, maximum=5)
     * @Groups({"default"})
     */
    private ?int $rating = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?int $countTries;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    private ?DateTimeInterface $deletedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected datetime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected datetime $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;
        return $this;
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

    public function getCountTries(): ?int
    {
        return $this->countTries;
    }

    public function setCountTries(?int $countTries): self
    {
        $this->countTries = $countTries;
        return $this;
    }

    public function incCountTries(): self
    {
        $this->countTries++;
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

    /**
     * Gets triggered every time on update
     *
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): self
    {
        $this->updatedAt = new \DateTime("now");
        return $this;
    }
}
