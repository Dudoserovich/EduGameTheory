<?php

namespace App\Entity;

use App\Repository\EducationTasksRepository;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=EducationTasksRepository::class)
 * @Gedmo\SoftDeleteable
 */
class EducationTasks
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
     * @ORM\Column(type="integer", nullable=false)
     * @OA\Property()
     * @Groups({"default"})
     */
    private int $blockNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Education::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=Education::class))
     * @Groups({"default"})
     */
    private ?Education $edu;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=Task::class))
     * @Groups({"default"})
     */
    private ?Task $task;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $theoryText;

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

    public function getBlockNumber(): int
    {
        return $this->blockNumber;
    }

    public function setBlockNumber(int $blockNumber): self
    {
        $this->blockNumber = $blockNumber;
        return $this;
    }

    public function getEdu(): ?Education
    {
        return $this->edu;
    }

    public function setEdu(?Education $edu): self
    {
        $this->edu = $edu;
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

    public function getTheoryText(): ?string
    {
        return $this->theoryText;
    }

    public function setTheoryText(?string $theoryText): self
    {
        $this->theoryText = $theoryText;
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
