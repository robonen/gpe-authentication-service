<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\Service\NormalizeService;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="get_user_list", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function getAllUsers(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $count = $request->query->get('count', null);
        if(!is_null($count)) $count = intval($count);
        $offset = ($request->query->get('page', null) - 1) * $count;
        $users = $em->getRepository('App:User')->findBy(array(), array('id' => 'DESC'), $count, $offset > 0 ? $offset : null);

        $result = array();
        foreach($users as $user) {
            $json = (new NormalizeService())->normalizeByGroup($user);
            if(in_array('ROLE_MANAGER', $user->getRoles())) {
                $projects = $em->getRepository('App:Project')->findByManager($user);
                $json['projects'] = (new NormalizeService())->normalizeByGroup($projects);
            }
            else $json['projects'] = array();

            $result[] = $json;
        }
        return $this->json([
            'message' => 'Все пользователи в системе',
            'data' => $result
        ]);
    }

    /**
     * @Route("/me", name="get_user", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getCurrentUser()
    {
        return $this->json([
            'message' => 'Информация о пользователе',
            'data' => (new NormalizeService())->normalizeByGroup($this->getUser()),
        ]);
    }

    /**
     * @Route("/role", name="set_role", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function setUserRole(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $data = $request->toArray();

        if(is_null($user = $em->getRepository('App:User')->find($data['user'] ?? ''))) {
            return new JsonResponse(array('message' =>'Не существует такого пользователя'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $role = $data['role'] ?? '';
        if($role != 'ROLE_ADMIN' && $role != 'ROLE_MANAGER') {
            return new JsonResponse(array('message' =>'Нет такой роли'), JsonResponse::HTTP_BAD_REQUEST);
        }

        if(!in_array($role, $user->getRoles())) $user->setRoles(array_unique(array_merge($user->getRoles(), [$role]), SORT_REGULAR));
        else $user->setRoles(array_diff($user->getRoles(), [$role]));

        $em->flush();

        return $this->json([
            'message' => 'Роль пользователя успешно изменена',
            'data' => (new NormalizeService())->normalizeByGroup($user),
        ]);
    }
}
