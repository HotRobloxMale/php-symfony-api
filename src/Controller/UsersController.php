<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class UsersController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/users/get", name="app_users", methods={"GET"})
     *
     * @Operation(
     *     tags={"Users"},
     *     summary="Get all users",
     *     security={@Security(name="Bearer")}
     * )
     */
    public function getUsersAction(SerializerInterface $serializer): JsonResponse
    {
        $users = $this->entityManager->getRepository(Users::class)->findAll();

        $jsonData = $serializer->serialize($users, 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * @Route("/api/users/add", name="add_users", methods={"POST"})
     *
     * @Operation(
     *     tags={"Users"},
     *     summary="Add a new user",
     *     security={@Security(name="Bearer")},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="login", type="string", example="JohnDoe"),
     *             @OA\Property(property="email", type="string", example="JohnDoe@gmail.com"),
     *             @OA\Property(property="name", type="string",example="John"),
     *             @OA\Property(property="surname", type="string",example="Doe"),
     *             @OA\Property(property="dateofbirth", type="string", format="date",example="1999-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User created successfully",
     *         @OA\Schema(ref="#/components/schemas/Basket")
     *     )
     * )
     */

    public function addUsersAction(Request $request, SerializerInterface $serializer): JsonResponse
    {
        // Deserialize the JSON data from the request body into an object
        $user = $serializer->deserialize($request->getContent(), Users::class, 'json');

        // Persist the user object
        $entityManager = $this->entityManager;
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, [], ['groups' => 'full']);
    }

}