<?php

namespace App\Controller\Authentication;

use App\Controller\AbstractApiController;
use App\Entity\AccessToken;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\AccessTokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/registration', name: 'auth_registration', methods: ['POST'])]
final class RegisterController extends AbstractApiController
{
    public function __invoke(
        Request                     $request,
        UserRepository              $repository,
        AccessTokenRepository       $tokenRepository,
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

        $token = new AccessToken();
        $token->setUser($user);
        $token->setActiveTill((new \DateTimeImmutable())->add(new \DateInterval('P1Y')));
        $token->setToken(bin2hex(openssl_random_pseudo_bytes(64)));

        $repository->save($user, false);
        $tokenRepository->save($token);

        return $this->ok([
                'user' => $user,
                'token' => $token->getToken()],
            Response::HTTP_CREATED);
    }
}
