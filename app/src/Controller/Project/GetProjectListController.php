<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'project_list', methods: ['GET'])]
final class GetProjectListController extends AbstractApiController
{
    public function __invoke(ProjectRepository $repository): JsonResponse
    {
        $projects = $repository->findAll();

        return $this->ok($projects);
    }
}
