<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserGroupsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [],
    normalizationContext: ["groups" => ["user_api"]], // Include user_api group
    denormalizationContext: ["groups" => ["user_api"]], // Include user_api group
)]
#[GetCollection()]
#[Get()]
#[post()]
#[ORM\Entity(repositoryClass: UserGroupsRepository::class)]
class UserGroups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user_group_api", "user_api"])] // Include user_api group
    #[ApiProperty(identifier: true, example: 1)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[ApiProperty(example: "group name")]
    #[Groups(["user_group_api", "user_api"])] // Include user_api group
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
