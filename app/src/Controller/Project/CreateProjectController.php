<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Form\Type\ProjectType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'project_create', methods: ['POST'])]
final class CreateProjectController extends AbstractApiController
{
    private ObjectManager $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function __invoke(Request $request): JsonResponse
    {
        $form = $this->buildForm(ProjectType::class, $request);

        if (!$form->isValid())
            return $this->error($this->formatFormError($form), Response::HTTP_UNPROCESSABLE_ENTITY);

        $project = $form->getData();

        $this->manager->persist($project);
        $this->manager->flush();

        return $this->ok($project, Response::HTTP_CREATED);
    }
}
