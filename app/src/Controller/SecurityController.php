<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\AccessToken;
use App\Service\NormalizeService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/api/security")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $data = $request->toArray();

        if(empty($email = $data['email'] ?? '') || empty($password = $data['password'] ?? '')
            || empty($confirm_password = $data['password'] ?? '')) {
            return new JsonResponse(array(
                'message' =>'Заполнены не все данные. Пожалуйста, проверьте корректность заполнения всех обязательных полей.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        if($password != $confirm_password) {
            return new JsonResponse(array(
                'message' =>'Пароли не совпадают.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        if(!is_null($em->getRepository(User::class)->findOneByEmail($email))) {
            return new JsonResponse(array(
                'message' =>'Пользователь с таким адресом электронной почты уже существует.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($email);
 //       $user->setConfirmationCode($codeGenerator->getConfirmationCode());
        $user->setName($email);
        $user->setCreatedAt();
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $token = new AccessToken();
        $token->setUser($user);
        $token->setActiveTill((new \DateTimeImmutable())->add(new \DateInterval('P1Y')));
        $token->setToken(bin2hex(openssl_random_pseudo_bytes(64)));

        $em->persist($user);
        $em->persist($token);
        $em->flush();

        return $this->json([
            'message' => 'Регистрация прошла успешно',
            'data' => array(
                'token' => $token->getToken(),
                'user' => (new NormalizeService())->normalizeByGroup($user)
            ),
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $data = $request->toArray();

        if(empty($email = $data['email'] ?? '') || empty($password = $data['password'] ?? '')) {
            return new JsonResponse(array(
                'message' =>'Вы не ввели пароль и/или адрес электронной почты.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        if(is_null($user = $em->getRepository(User::class)->findOneByEmail($email))) {
            return new JsonResponse(array(
                'message' =>'Такого пользователя не существует'), JsonResponse::HTTP_BAD_REQUEST);
        }
        if(!password_verify($password, $user->getPassword())) {
            return new JsonResponse(array(
                'message' =>'Неверный логин и/или пароль'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = new AccessToken();
        $token->setUser($user);
        $token->setActiveTill((new \DateTimeImmutable())->add(new \DateInterval('P1Y')));
        $token->setToken(bin2hex(openssl_random_pseudo_bytes(64)));

        $em->persist($token);
        $em->flush();

        return $this->json([
            'message' => 'Вход в систему выполнен успешно',
            'data' => array(
                'token' => $token->getToken(),
                'user' => (new NormalizeService())->normalizeByGroup($user)
            ),
        ]);
    }

    /**
     * @Route("/send/password", name="send_password", methods={"POST"})
     */
    public function sendPassword(Request $request, MailerInterface $mailer, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $data = $request->toArray();

        if(empty($email = $data['email'])) {
            return new JsonResponse(array(
                'message' =>'Вы не ввели адрес электронной почты.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        if(is_null($user = $em->getRepository(User::class)->findOneByEmail($email))) {
            return new JsonResponse(array(
                'message' =>'Такого пользователя не существует'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $password = '';
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 20; $i++) {
            $n = rand(0, $alphaLength);
            $password .= $alphabet[$n];
        }
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $type = $data['type'] ?? 'reset';
        $tokens = $em->getRepository(AccessToken::class)->findByUser($user);

        if($type == 'update') {
            $token = $em->getRepository(AccessToken::class)->findOneByToken(explode(' ', $request->headers->get('YT-AUTH-TOKEN'))[1]);
            if($token instanceof AccessToken) {
                if(($key = array_search($token, $tokens, TRUE)) !== FALSE) {
                    unset($tokens[$key]);
                }
            }
        }
        foreach($tokens as $token) {
            $em->remove($token);
        }

        $em->flush();

        $message = (new Email())
            ->from('forinfo@yourtar.ru') //mail
            ->to($user->getEmail())
            ->subject('Новый пароль')
            ->html($this->renderView(
                'email/newPassword.html.twig', //front
                array(
                    'password' => $password
                )
            ),
                'text/html');

        $mailer->send($message);

        return $this->json([
            'message' => 'Пароль успешно изменен',
            'data' =>  (new NormalizeService)->normalizeByGroup($user)
        ]);
    }

    /**
     * @Route("/change/password", name="change_password", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function changePassword(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $user = $this->getUser();
        $data = $request->toArray();

        if(!password_verify($data['oldPassword'] ?? '', $user->getPassword())) {
            return new JsonResponse(array(
                'message' =>'Неверный логин и/или пароль'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $new = $data['newPassword'] ?? '';
        if($new != $data['verify'] ?? '') {
            return new JsonResponse(array(
                'message' =>'Пароли не совпадают'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->setPassword(password_hash($new, PASSWORD_DEFAULT));
        $em->flush();

        return $this->json([
            'message' => 'Пароль успешно изменен',
            'data' =>  (new NormalizeService)->normalizeByGroup($user)
        ]);
    }

    /**
     * @Route("/change/email", name="change_email", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function changeEmail(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $user = $this->getUser();
        $data = $request->toArray();

        if(empty($email = $email = $data['email'] ?? null)) {
            return new JsonResponse(array(
                'message' =>'Проверьте корректность введенного адреса почты'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->setEmail($email);
        $em->flush();

        return $this->json([
            'message' => 'Адрес электронной почты успешно изменен',
            'data' =>  (new NormalizeService)->normalizeByGroup($user)
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function logout(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $token = $em->getRepository('App:AccessToken')->findOneBy(array(
            'token' => explode(' ', $request->headers->get('YT-AUTH-TOKEN'))[1],
        ));

        if (!$token instanceof AccessToken) {
            return $this->json([
                'message' => 'Токен уже удален!',
            ], 404);
        }

        $em->remove($token);
        $em->flush();

        return $this->json([
            'message' => 'Выход из системы выполнен успешно.',
        ]);
    }
}
