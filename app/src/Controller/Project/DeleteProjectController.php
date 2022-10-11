<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects/{id}', name: 'project_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
final class DeleteProjectController extends AbstractApiController
{
    public function __invoke(ProjectRepository $repository, Project $project = null): JsonResponse
    {
        if (!$project)
            return $this->error('Project not found', Response::HTTP_NOT_FOUND);

        $repository->remove($project);

        return $this->ok('Project successfully deleted');
    }
}
