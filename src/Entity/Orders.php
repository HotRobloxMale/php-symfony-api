<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\OrdersController;
use App\Repository\OrdersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
operations: [
        new GetCollection(uriTemplate: '/orders/byid/{id}', controller: OrdersController::class, name: 'getOrder'),
        new GetCollection(uriTemplate: '/orders/byuser/{id}', controller: OrdersController::class , name: 'getOrders'),
    ],
    normalizationContext: ["groups" => ["user_api"]],
    denormalizationContext: ["groups" => ["user_api"]],
)]
#[GetCollection]

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true, example: 1)]
    #[Groups(["order", "user_api"])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[ApiProperty(example: '4f6a6d58149a9')]
    #[Groups(["order", "user_api"])]
    private ?string $orderNumber = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[Groups(["order", "user_api"])]
    private ?Users $user = null;

    #[ORM\Column(type: 'datetime')]
    #[ApiProperty(example: '2021-03-01T00:00:00+00:00')]
    #[Groups(["order", "user_api"])]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\ManyToOne(targetEntity: Items::class)]
    #[Groups(["order", "user_api"])]
    private ?Items $item = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(["order", "user_api"])]
    private ?int $quantity = null;

    #[ORM\Column(type: 'float')]
    #[ApiProperty(example: 1.0)]
    #[Groups(["order", "user_api"])]
    private ?float $price = null;

    #[ORM\Column(type: 'float')]
    #[ApiProperty(example: 0.5)]
    #[Groups(["order", "user_api"])]
    private ?float $discount = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): void
    {
        $this->user = $user;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(?\DateTimeInterface $orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    public function getItem(): ?Items
    {
        return $this->item;
    }

    public function setItem(?Items $item): void
    {
        $this->item = $item;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): void
    {
        $this->discount = $discount;
    }
}
