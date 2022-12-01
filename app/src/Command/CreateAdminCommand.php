<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\AccessToken;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminCommand extends Command {
    protected static $defaultName = 'app:create-admin';
    protected static $defaultDescription = 'Используется для создания администратора. Не забудьте ввести адрес почты и пароль.';

    protected function configure(): void
    {
        $this
            ->setDescription('Команда для создания администратора. Не забудьте ввести адрес почты и пароль в качестве основных параметров')
            ->addArgument('email', InputArgument::REQUIRED, 'Каков email администратора?')
            ->addArgument('password', InputArgument::REQUIRED, 'Введите пароль пользователя.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Создаем администратора',
            '============',
            '',
        ]);
        $em = $this->getApplication()->getKernel()->getContainer()->get('doctrine')->getManager();
        if(count($em->getRepository('App:User')->findByRoleAdmin()) == 0) {
            $user = new User();
            $user->setEmail($input->getArgument('email'));
            $user->setPassword(password_hash($input->getArgument('password'), PASSWORD_DEFAULT));
            $user->setRoles(["ROLE_ADMIN"]);

            $token = new AccessToken();
            $token->setUser($user);
            $token->setActiveTill((new \DateTimeImmutable())->add(new \DateInterval('P1Y')));
          //  $token->setToken('b7d3685943328562416093197985915a1e3fef562ed44a000fc345eed7dc33ddc5cea7b75f767b7b02a661df0e109d3be5588b4ad683fe270b06f1787fe672c3');
            $token->setToken(bin2hex(openssl_random_pseudo_bytes(64))); 

            $em->persist($user);
            $em->persist($token);
            $em->flush();

            $output->writeln('Администратор успешно добавлен в систему');
        }
        else {
            $output->writeln('В системе уже есть администратор');
        }

        $output->writeln([
            '',
            '============',
        ]);

        return Command::SUCCESS;
    }
}