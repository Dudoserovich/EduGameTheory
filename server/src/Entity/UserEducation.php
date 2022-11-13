<?php

namespace App\Entity;

use App\Repository\UserEducationRepository;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserEducationRepository::class)
 * @Gedmo\SoftDeleteable
 */
class UserEducation
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
     * @ORM\ManyToOne(targetEntity=Education::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @OA\Property(ref=@Model(type=Education::class))
     * @Groups({"default"})
     */
    private ?Education $edu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property(minimum=2, maximum=5)
     * @Groups({"default"})
     */
    private ?int $rating;

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

    public function getEdu(): ?Education
    {
        return $this->edu;
    }

    public function setEdu(?Education $edu): self
    {
        $this->edu = $edu;
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
