<?php

namespace App\Entity;

use App\Repository\UserAchivementRepository;
use DateTime;
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
 * @ORM\HasLifecycleCallbacks
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
     * Текущее количество повторений (пример: необходимо пройти 10 заданий. Пройдено 3).
     *
     * @ORM\Column(type="integer")
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?int $totalScore = 0;

    /**
     * @var ?datetime $achievementDate Дата окончательного получения достижения
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    private ?datetime $achievementDate = null;

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
     *@ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected datetime $updatedAt;

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

    public function getTotalScore(): ?int
    {
        return $this->totalScore;
    }

    public function incTotalScore(): self
    {
        $this->totalScore = $this->totalScore++;
        return $this;
    }

    public function setTotalScore(?int $totalScore): self
    {
        $this->totalScore = $totalScore;
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

    // Когда набрано необходимое количество очков, записать дату получения достижения
    #[ORM\PostUpdate]
    public function setAchievementDate(): self
    {
        if ($this->getAchievement()->getNeedScore() === $this->getTotalScore()) {
            $this->achievementDate = new \DateTime("now");
        }
        return $this;
    }

    public function getAchievementDate(): ?datetime
    {
        return $this->achievementDate;
    }
}
