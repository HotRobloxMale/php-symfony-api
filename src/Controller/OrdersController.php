<?php

namespace App\Controller;

use App\Entity\Orders;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class OrdersController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    public function getOrder(string $id,SerializerInterface $serializer): JsonResponse
    {

        $orders = $this->entityManager->getRepository(Orders::class)->findBy(['orderNumber' => $id]);

        if (!$orders) {
            return new JsonResponse(['error' => 'No orders found'], 404);
        }

        $structuredOrders = $this->structureOrders($orders);
        $jsonData = $serializer->serialize(array_values($structuredOrders), 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }

    public function getOrders(int $id, SerializerInterface $serializer): JsonResponse
    {
        $orders = $this->entityManager->getRepository(Orders::class)->findBy(['user' => $id]);

        if (!$orders) {
            return new JsonResponse(['error' => 'No orders found'], 404);
        }

        $structuredOrders = $this->structureOrders($orders);
        $jsonData = $serializer->serialize(array_values($structuredOrders), 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }


    private function structureOrders(array $orders): array
    {
        $structuredOrders = [];

        foreach ($orders as $order) {
            $orderNumber = $order->getOrderNumber();

            if (!isset($structuredOrders[$orderNumber])) {
                $structuredOrders[$orderNumber] = [
                    'orderId' => $orderNumber,
                    'orderDate' => $order->getOrderDate(),
                    'user' => [
                        'id' => $order->getUser()->getId(),
                        'email' => $order->getUser()->getEmail(),
                        'login' => $order->getUser()->getLogin(),
                        'name' => $order->getUser()->getName(),
                        'surname' => $order->getUser()->getSurname(),
                        'group' => $order->getUser()->getGroup(),
                    ],
                    'items' => [],
                    'totalPrice' => 0.0,
                ];
            }

            $item = [
                'id' => $order->getItem()->getId(),
                'discount' => $order->getDiscount(),
                'itemDescription' => $order->getItem()->getItemDescription(),
                'itemName' => $order->getItem()->getItemName(),
                'price' => $order->getPrice(),
                'quantity' => $order->getQuantity(),
            ];
            $structuredOrders[$orderNumber]['items'][] = $item;
            $itemTotal = $item['price'] * $item['quantity'] * (1 - $item['discount']);
            $structuredOrders[$orderNumber]['totalPrice'] += $itemTotal;
        }

        return $structuredOrders;
    }
}