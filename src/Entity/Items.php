<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ItemsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    operations: [],
    normalizationContext: ["groups" => ["user_api"]],
    denormalizationContext: ["groups" => ["user_api"]],
)]
#[GetCollection()]
#[Get()]
#[post()]
#[ORM\Entity(repositoryClass: ItemsRepository::class)]
class Items
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true, example: 1)]
    #[Groups(["item_api", "user_api"])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["item_api", "user_api"])]
    #[ApiProperty(example: "itemName")]
    private ?string $itemName = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["item_api", "user_api"])]
    #[ApiProperty(example: "itemDescription")]
    private ?string $itemDescription = null;

    #[ORM\Column(type: 'float')]
    #[Groups(["item_api", "user_api"])]
    #[ApiProperty(example: 1.0)]
    private ?float $price = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(["item_api", "user_api"])]
    private ?int $quantity = null;

    #[ORM\Column(type: 'float')]
    #[Groups(["item_api", "user_api"])]
    private ?float $discount = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(["item_api", "user_api"])]
    private Users $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): void
    {
        $this->discount = $discount;
    }

    public function getItemDescription(): ?string
    {
        return $this->itemDescription;
    }

    public function setItemDescription(?string $itemDescription): void
    {
        $this->itemDescription = $itemDescription;
    }

    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    public function setItemName(?string $itemName): void
    {
        $this->itemName = $itemName;
    }

    public function getOwner(): Users
    {
        return $this->owner;
    }

    public function setOwner(Users $owner): void
    {
        $this->owner = $owner;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
