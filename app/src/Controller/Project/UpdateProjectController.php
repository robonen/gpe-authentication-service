<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Entity\Project;
use App\Form\Type\ProjectType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects/{id}', name: 'project_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
final class UpdateProjectController extends AbstractApiController
{
    protected ObjectManager $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function __invoke(int $id, Request $request): JsonResponse
    {
        $project = $this->manager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->error('Project not found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->formFromRequest(ProjectType::class, $request, $project);

        if (!$form->isValid())
            return $this->error($this->formatFormError($form), Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->manager->flush();

        return $this->ok($form->getData());
    }
}
