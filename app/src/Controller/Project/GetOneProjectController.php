<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Entity\Project;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects/{id}', name: 'project_info', requirements: ['id' => '\d+'], methods: ['GET'])]
final class GetOneProjectController extends AbstractApiController
{
    public function __invoke(Project $project = null): JsonResponse
    {
        if (!$project)
            return $this->error('Project not found', Response::HTTP_NOT_FOUND);

        return $this->ok($project);
    }
}
