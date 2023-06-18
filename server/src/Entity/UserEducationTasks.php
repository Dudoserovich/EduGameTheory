<?php

namespace App\Entity;

use App\Repository\UserEducationTasksRepository;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserEducationTasksRepository::class)
 * @Gedmo\SoftDeleteable
 */
class UserEducationTasks
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
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=User::class))
     * @Groups({"default"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=EducationTasks::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=EducationTasks::class))
     * @Groups({"default"})
     */
    private ?EducationTasks $eduTasks;

    /**
     * @ORM\Column(type="boolean")
     * @OA\Property()
     * @Groups({"default"})
     */
    private bool $success = false;

    /**
     * @ORM\Column(type="boolean")
     * @OA\Property()
     * @Groups({"default"})
     */
    private bool $isCurrentBlock = false;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getEduTasks(): ?EducationTasks
    {
        return $this->eduTasks;
    }

    public function setEduTasks(?EducationTasks $eduTasks): self
    {
        $this->eduTasks = $eduTasks;
        return $this;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function getIsCurrentBlock(): bool
    {
        return $this->isCurrentBlock;
    }

    public function setCurrentBlock(bool $isCurrentBlock): self
    {
        $this->isCurrentBlock = $isCurrentBlock;
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
