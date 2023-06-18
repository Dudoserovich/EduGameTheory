<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"login"}, message="There is already an account with this login")
 * @Gedmo\SoftDeleteable
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     */
    private string $login;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(format="password")
     * @Groups({"default"})
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property()
     * @Groups({"default"})
     */
    private ?string $fio;

    /**
     * @var string[]
     * @ORM\Column(type="json")
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string", minItems=1, maxItems=3, enum={"ROLE_USER", "ROLE_TEACHER", "ROLE_ADMIN"})
     * )
     * @Groups({"default"})
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property(format="email")
     * @Groups({"default"})
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, options={"default" : "serious_cat.jpg"})
     * @OA\Property()
     * @Groups({"default"})
     */
    #[Assert\Regex(
        pattern: "/[A-z]+\.(png|jpg)$/",
        message: 'Invalid avatar',
        match: true,
    )]
    private ?string $avatar = 'serious_cat.jpg';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @OA\Property(format="date-time")
     * @Groups({"default"})
     */
    private ?DateTimeInterface $deletedAt;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('login', new NotBlank());
        $metadata->addPropertyConstraint('password', new NotBlank());
        $metadata->addPropertyConstraint('email', new Assert\Regex([
            'pattern' => '/(.+@.+\..+)/',
        ]));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
//        return $this->login;
        return (string) $this->id;
    }

    /**
     * @OA\Property(property="login")
     * @Groups({"default"})
     */
    public function getUsername(): string
    {
        return $this->login;
    }

    public function setUsername(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getFio(): ?string
    {
        return $this->fio;
    }

    public function setFio(string $fio): self
    {
        $this->fio = $fio;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function addRole(string $role)
    {
        $this->roles = array_unique([...$this->roles, $role]);
    }
}