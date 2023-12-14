<?php


namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\UsersController;
use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;

#[ApiResource(

    operations:[],
    normalizationContext: ["groups" => ["user_api"]],
    denormalizationContext: ["groups" => ["user_api"]],
)]
#[GetCollection()]
#[Get()]
#[post()]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user_api"])]
    #[ApiProperty(identifier: true, example: 1)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["user_api"])]
    #[ApiProperty(example: "login")]
    private ?string $login = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["user_api"])]
    #[ApiProperty(example: "name")]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["user_api"])]
    #[ApiProperty(example: "email")]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["user_api"])]
    #[ApiProperty(example: "surname")]
    private ?string $surname = null;

    #[ORM\ManyToOne(targetEntity: UserGroups::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(["user_api"])]
    #[MaxDepth(1)]
    private ?UserGroups $group = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    public function getGroup(): ?UserGroups
    {
        return $this->group;
    }

    public function setGroup(?UserGroups $group): self
    {
        $this->group = $group;
        return $this;
    }
}
