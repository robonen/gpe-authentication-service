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

#[Route('/auth/logout', name: 'auth_logout', methods: ['DELETE'])]
class LogoutController extends AbstractApiController
{
    public function __invoke(
        Request                     $request,
        AccessTokenRepository       $tokenRepository,
    ): JsonResponse
    {
        $token = $request->headers->get('X-AUTH-TOKEN');
        $removetoken = $tokenRepository->findOneByToken($token);

        $tokenRepository->remove($removetoken);
        return $this->ok('Exit', Response::HTTP_OK);
    }
}
