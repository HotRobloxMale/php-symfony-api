<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\BasketController;
use App\DTO\BasketInput;
use App\Repository\BasketRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    operations: [
        new Get(uriTemplate: '/basket/get/{id}',controller: BasketController::class,name: 'getUserBasket'),
        new post(uriTemplate: '/basket/add', controller: BasketController::class, openapiContext: [
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'userId' => [
                                    'type' => 'integer',
                                    'example' => 1,
                                ],
                                'itemId' => [
                                    'type' => 'integer',
                                    'example' => 1,
                                ],
                                'quantity' => [
                                    'type' => 'integer',
                                    'example' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    new Post(uriTemplate: '/basket/buy/{id}',controller: BasketController::class,name: 'buyUserBasket')
    ],
    normalizationContext: ["groups" => ["user_api"]],
    denormalizationContext: ["groups" => ["user_api"]],
)]
#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["basket_api", "user_api"])]
    #[ApiProperty(identifier: true, example: 1)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["basket_api", "user_api"])]
    private ?Users $user = null;

    #[ORM\ManyToOne(targetEntity: Items::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["basket_api", "user_api"])]
    private ?Items $item = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(["basket_api", "user_api"])]
    private ?int $quantity = null;

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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): void
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
