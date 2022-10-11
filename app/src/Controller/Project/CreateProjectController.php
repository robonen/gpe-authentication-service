<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Form\Type\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'project_create', methods: ['POST'])]
final class CreateProjectController extends AbstractApiController
{
    public function __invoke(Request $request, ProjectRepository $repository): JsonResponse
    {
        $form = $this->formFromRequest(ProjectType::class, $request);

        if (!$form->isValid())
            return $this->error($this->formatFormError($form), Response::HTTP_UNPROCESSABLE_ENTITY);

        $project = $form->getData();

        $repository->save($project);

        return $this->ok($project, Response::HTTP_CREATED);
    }
}
