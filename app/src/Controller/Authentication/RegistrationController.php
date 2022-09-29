<?php

namespace App\Controller\Authentication;

use App\Controller\AbstractApiController;
use App\Form\Type\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/registration', name: 'auth_registration', methods: ['POST'])]
final class RegistrationController extends AbstractApiController
{
    private ObjectManager $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function __invoke(Request $request): JsonResponse
    {
        $form = $this->formFromRequest(UserType::class, $request);

        if (!$form->isValid())
            return $this->error($this->formatFormError($form), Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = $form->getData();
        $user->setCreatedAt();

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->ok($user, Response::HTTP_CREATED);
    }
}
