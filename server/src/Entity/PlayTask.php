<?php

namespace App\Entity;

use App\Repository\PlayTaskRepository;

use DateTime;
use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PlayTaskRepository::class)
 * @Gedmo\SoftDeleteable
 * @ORM\HasLifecycleCallbacks
 */
class PlayTask
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
     * @ORM\ManyToOne(targetEntity=Task::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=Task::class))
     * @Groups({"default"})
     */
    private ?Task $task;

    /**
     * @ORM\Column(type="json", nullable=false)
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="number")))
     * @Groups({"default"})
     */
    private array $moves = [];

    /**
     * Исход партии
     * @ORM\Column(type="float", nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?float $totalScore = null;

    /**
     * @ORM\Column(type="boolean")
     * @OA\Property()
     * @Groups({"default"})
     */
    private bool $success = false;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected datetime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected datetime $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    private ?DateTimeInterface $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @OA\Property(ref=@Model(type=User::class))
     * @Groups({"default"})
     */
    private ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    // User
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    // Task
    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;
        return $this;
    }

    // moves
    public function setMoves(array $moves): self
    {
        $this->moves = $moves;

        return $this;
    }

    public function addMove(int $move): self
    {
        $this->moves[] = $move;

        return $this;
    }

    public function getMoves(): ?array
    {
        return $this->moves;
    }

    // total score
    public function getTotalScores(): ?float
    {
        return $this->totalScore;
    }

    public function setTotalScore(?int $totalScore): self
    {
        $this->totalScore = $totalScore;
        return $this;
    }

    public function addTotalScore(?float $score): self
    {
        if (!$this->totalScore) {
            $this->totalScore = 0;
        } elseif (!$score) {
            $score = 0;
        }

        $this->totalScore += $score;
        return $this;
    }

    // deleted_at
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    // success
    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    // created_at
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

    // updated_at
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

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
