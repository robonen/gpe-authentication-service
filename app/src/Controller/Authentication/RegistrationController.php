<?php

namespace App\Controller\Authentication;

use App\Controller\AbstractApiController;
use App\Form\Type\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', name: 'auth_')]
final class RegistrationController extends AbstractApiController
{
    private ObjectManager $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    #[Route('/registration', name: 'registration', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $form = $this->buildForm(UserType::class, $request);

        if (!$form->isValid())
            return $this->json($form);

        $user = $form->getData();
        $user->setCreatedAt();

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->json($user);
    }
}
