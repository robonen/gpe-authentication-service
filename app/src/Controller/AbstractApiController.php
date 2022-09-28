<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

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
}
