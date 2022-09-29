<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    protected function buildForm(string $type, Request $request): FormInterface
    {
        $options =[
            'csrf_protection' => false,
        ];

        $form = $this->container->get('form.factory')->createNamed('', $type, null, $options);

        $form->submit($request->toArray());

        return $form;
    }

    protected function formatFormError(FormInterface $form): array
    {
        $errors = [];

        foreach ($form as $name => $error) {
            foreach ($error->getErrors() as $e) {
                $errors[$name]['message'] = $e->getMessage();
            }
        }

        return $errors;
    }

    protected function respond(mixed $data, int $statusCode): JsonResponse
    {
        return $this->json($data, $statusCode);
    }

    protected function ok(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->respond([
            'status' => 'ok',
            'payload' => $data,
        ], $statusCode);
    }

    protected function error(mixed $data, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return $this->respond([
            'status' => 'error',
            'payload' => $data,
        ], $statusCode);
    }
}
