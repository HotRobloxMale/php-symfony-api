<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Items;
use App\Entity\Orders;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Mailer\MailerInterface;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twig\Environment;

class BasketController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $mailer;
    private $twig;

    public function __construct(EntityManagerInterface $entityManager,Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->mailer = new PHPMailer(true);
        $this->twig = $twig;
    }


    public function getUserBasket(SerializerInterface $serializer, int $id): JsonResponse
    {
        // Fetch the user entity based on the provided user ID
        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        // Fetch the basket items associated with the user
        $basketItems = $this->entityManager->getRepository(Basket::class)->findBy(['user' => $user]);

        $itemsWithDiscountedPrice = [];

        // Calculate the discounted price for each item and build the response data
        foreach ($basketItems as $basketItem) {
            /** @var Basket $basketItem */
            $item = $basketItem->getItem();

            $discountedPrice = sprintf("%.2f",($item->getPrice()-$item->getPrice()*$item->getDiscount()));

            $itemData = [
                'item' => $item,
                'quantity' => $basketItem->getQuantity(),
                'price' => $item->getPrice(),
                'discounted_price' => sprintf("%.2f", $discountedPrice),
                'total_price' => sprintf("%.2f",$discountedPrice * $basketItem->getQuantity())
            ];

            $itemsWithDiscountedPrice[] = $itemData;
        }

        // Calculate the total summary of the basket
        $totalSummary = array_reduce(
            $itemsWithDiscountedPrice,
            fn($total, $itemData) => $total + ($itemData['discounted_price'] * $itemData['quantity']),
            0
        );

        // Build the final response data
        $responseData = [
            'user' => $user,
            'items' => $itemsWithDiscountedPrice,
            'total_summary' => sprintf("%.2f", $totalSummary), // Convert to string with 2 decimal places
        ];

        // Serialize the data and return as JSON response

        $jsonData = $serializer->serialize($responseData, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }


    public function addUserBasket(Request $request, SerializerInterface $serializer): JsonResponse
    {
        // Decode the request content
        $data = json_decode($request->getContent(), true);

        if (!isset($data['itemId'], $data['userId'], $data['quantity'])) {
            return new JsonResponse(['message' => 'Missing parameters.'], Response::HTTP_BAD_REQUEST);
        }

        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->find($data['userId']);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        // Fetch the item entity based on the provided item ID
        $itemRepository = $this->entityManager->getRepository(Items::class);
        $item = $itemRepository->find($data['itemId']);

        if (!$item) {
            return new JsonResponse(['message' => 'Item not found.'], Response::HTTP_NOT_FOUND);
        }

        $basketRepository = $this->entityManager->getRepository(Basket::class);
        $existingBasket = $basketRepository->findOneBy(['user' => $user, 'item' => $item]);

        if ($existingBasket) {
            $existingBasket->setQuantity($existingBasket->getQuantity() + $data['quantity']);
            $this->entityManager->persist($existingBasket);
            $this->entityManager->flush();

            $jsonData = $serializer->serialize($existingBasket, 'json');

            return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
        } else {
            $basket = new Basket();
            $basket->setUser($user);
            $basket->setItem($item);
            $basket->setQuantity($data['quantity']);

            $this->entityManager->persist($basket);
            $this->entityManager->flush();

            $jsonData = $serializer->serialize($basket, 'json');

            return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
        }

    }
    public function buyUserBasket(SerializerInterface $serializer, MailerInterface $mailer,int $id): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        // Fetch the basket items associated with the user
        $basketItems = $this->entityManager->getRepository(Basket::class)->findBy(['user' => $user]);
        $itemsRepo = $this->entityManager->getRepository(Items::class);
        if(!$basketItems){
            return new JsonResponse(['message' => 'Basket is empty.'], Response::HTTP_NOT_FOUND);
        }
        $itemsWithDiscountedPrice = [];
        $insufficientStockItems = [];

        // Calculate the discounted price for each item and build the response data
        foreach ($basketItems as $basketItem) {
            /** @var Basket $basketItem */
            $item = $basketItem->getItem();

            // Fetch the corresponding item from Items repository
            $itemDataInRepo = $itemsRepo->find($item->getId());

            if (!$itemDataInRepo || $basketItem->getQuantity() > $itemDataInRepo->getQuantity()) {
                $insufficientStockItems[] = $item->getItemName();
                continue;
            }

            $discountedPrice = sprintf("%.2f",($item->getPrice()-$item->getPrice()*$item->getDiscount()));

            $itemData = [
                'item' => $item,
                'quantity' => $basketItem->getQuantity(),
                'price' => $item->getPrice(),
                'discounted_price' => sprintf("%.2f", $discountedPrice),
                'total_price' => sprintf("%.2f",$discountedPrice * $basketItem->getQuantity())
            ];

            $itemsWithDiscountedPrice[] = $itemData;
        }

        // Calculate the total summary of the basket
        $totalSummary = array_reduce(
            $itemsWithDiscountedPrice,
            fn($total, $itemData) => $total + ($itemData['discounted_price'] * $itemData['quantity']),
            0
        );


        $responseData = [
            'user' => $user,
            'items' => $itemsWithDiscountedPrice,
            'total_summary' => sprintf("%.2f", $totalSummary), // Convert to string with 2 decimal places
        ];

        if (!empty($insufficientStockItems)) {
            return new JsonResponse(['message' => 'Not enough stock for items: ' . implode(', ', $insufficientStockItems)], Response::HTTP_BAD_REQUEST);
        }
        else {
            $timestamp = round(microtime(true) * 1000); // Current time in milliseconds
            $timestamp = substr($timestamp, -5, 5); // Last 5 digits of the timestamp
            $randomValue = mt_rand(0, 9);

            $orderNumber = "O" . $id . $timestamp . $randomValue; // 12 characters long

            foreach ($basketItems as $basketItem) {
                /** @var Basket $basketItem */
                $item = $basketItem->getItem();

                $orderItem = new Orders();
                $orderItem->setUser($user);
                $orderItem->setItem($item);
                $orderItem->setQuantity($basketItem->getQuantity());
                $orderItem->setPrice($item->getPrice());
                $orderItem->setDiscount($item->getDiscount());
                $orderItem->setOrderNumber($orderNumber);
                $orderItem->setOrderDate(new \DateTime());
                $this->entityManager->persist($orderItem);
                // Fetch the corresponding item from Items repository
                $itemDataInRepo = $itemsRepo->find($item->getId());

                $itemDataInRepo->setQuantity($itemDataInRepo->getQuantity() - $basketItem->getQuantity());
                $this->entityManager->persist($itemDataInRepo);
                $this->entityManager->remove($basketItem);
                $this->entityManager->flush();
            }
            try {
                // Render your Twig template and store the result in a variable.
                $body = $this->twig->render('email/receipt.html.twig', [
                    'items' => $itemsWithDiscountedPrice,
                    'total_summary' => $totalSummary
                ]);

                //Server settings
                $this->mailer->isSMTP();
                $this->mailer->Host       = 'smtp.gmail.com';
                $this->mailer->SMTPAuth   = true;
                $this->mailer->Username   = 'yourmail@mail.com';
                $this->mailer->Password   = 'yoursmtppassowrd';
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $this->mailer->Port       = 587;

                //Recipients
                $this->mailer->setFrom('yourmail@email.com');
                $this->mailer->addAddress($user->getEmail());

                //Content
                $this->mailer->isHTML(true);
                $this->mailer->Subject = 'Your Purchase Receipt for Order ' . $orderNumber;
                $this->mailer->Body    = $body;

                $this->mailer->send();
            } catch (Exception $e) {
                return new JsonResponse(['message' => 'There was an issue sending the email.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $jsonData = $serializer->serialize($responseData, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);


        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }


}