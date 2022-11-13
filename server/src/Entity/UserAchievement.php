<?php

namespace App\Entity;

use App\Repository\UserAchivementRepository;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserAchivementRepository::class)
 * @Gedmo\SoftDeleteable
 */
class UserAchievement
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
     * @ORM\ManyToOne(targetEntity=Achievement::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @OA\Property(ref=@Model(type=Achievement::class))
     * @Groups({"default"})
     */
    private ?Achievement $achievement;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @OA\Property(ref=@Model(type=User::class))
     * @Groups({"default"})
     */
    private ?User $user;

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

    public function getAchievement(): ?Achievement
    {
        return $this->achievement;
    }

    public function setAchievement(?Achievement $achievement): self
    {
        $this->achievement = $achievement;
        return $this;
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
