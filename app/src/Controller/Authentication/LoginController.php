<?php

namespace App\Controller\Authentication;

use App\Controller\AbstractApiController;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
final class LoginController extends AbstractApiController
{
    public function __invoke(
        Request $request,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $form = $this->formFromRequest(UserType::class, $request);

        if (!$form->isValid())
            return $this->error($this->formatFormError($form), Response::HTTP_UNPROCESSABLE_ENTITY);

        /** @var User $user */
        $user = $form->getData();

        /** @var User | null $registeredUser */
        $registeredUser = $repository->findOneByEmail($user->getEmail());

        if (!$registeredUser)
            return $this->error('User not found', Response::HTTP_UNAUTHORIZED);

        if (!$passwordHasher->isPasswordValid($registeredUser, $user->getPassword()))
            return $this->error('Invalid password', Response::HTTP_UNAUTHORIZED);

        return $this->ok($registeredUser, Response::HTTP_CREATED);
    }
}
