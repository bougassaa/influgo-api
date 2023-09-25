<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private UserRepository $repository,
        private UserPasswordHasherInterface $passwordHasher
    ){}

    #[Route(name: 'user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = new User();

        $errors = Utils::dto($request->toArray(), $user, $this->validator);

        if (count($errors) > 0) {
            return $this->json(['error' => implode("\n", $errors)], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user, $user->getPassword()
        );
        $user->setPassword($hashedPassword);

        if ($this->repository->findOneBy(['email' => $user->getEmail()])) {
            return $this->json(['error' => 'email already exist'], Response::HTTP_CONFLICT);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['email' => $user->getEmail()]);
    }

}
