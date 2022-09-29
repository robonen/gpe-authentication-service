<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects/{id}', name: 'project_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
class DeleteProjectController extends AbstractApiController
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

        $this->manager->remove($project);
        $this->manager->flush();

        return $this->ok('Project successfully deleted');
    }
}
