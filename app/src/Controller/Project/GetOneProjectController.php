<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects/{id}', name: 'project_info', requirements: ['id' => '\d+'], methods: ['GET'])]
final class GetOneProjectController extends AbstractApiController
{
    protected ObjectManager $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function __invoke(int $id): JsonResponse
    {
        $project = $this->manager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->error('Project not found', Response::HTTP_NOT_FOUND);
        }

        return $this->ok($project);
    }
}
