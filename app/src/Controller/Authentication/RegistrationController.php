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

#[Route('/auth/registration', name: 'auth_registration', methods: ['POST'])]
final class RegistrationController extends AbstractApiController
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

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword(),
        );
        $user->setPassword($hashedPassword);
        $user->setCreatedAt();

        $repository->save($user);

        return $this->ok($user, Response::HTTP_CREATED);
    }
}
