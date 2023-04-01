<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $phonenumber = $request->request->get('phonenumber');
        $password = $request->request->get('password');

        // find the user by phone number
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['phonenumber' => $phonenumber]);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        // check if the password is correct
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            throw new BadCredentialsException();
        }

        // TODO: Generate a JWT token and return it in the response

        return new JsonResponse(['message' => 'Authentication successful']);
    }
}
