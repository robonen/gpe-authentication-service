<?php

namespace App\Controller\Project;

use App\Controller\AbstractApiController;
use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'project_list', methods: ['GET'])]
final class GetProjectListController extends AbstractApiController
{
    protected ObjectManager $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function __invoke(): JsonResponse
    {
        $projects = $this->manager->getRepository(Project::class)->findAll();

        return $this->ok($projects);
    }
}
