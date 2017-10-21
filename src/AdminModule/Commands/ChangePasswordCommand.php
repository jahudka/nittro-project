<?php

declare(strict_types=1);

namespace App\AdminModule\Commands;

use App\Console\Helpers;
use App\Console\InteractionHelper;
use App\Entity\Identity;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ChangePasswordCommand extends Command {

    private $entityManager;

    private $userRepository;


    public function __construct(EntityManager $entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(Identity::class);
    }


    protected function configure() : void {
        $this
            ->setName('user:passwd')
            ->setDescription('Change a user\'s password')
            ->addArgument('email', InputArgument::REQUIRED, 'The e-mail of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password')
        ;
    }


    protected function interact(InputInterface $input, OutputInterface $output) : void {
        /** @var QuestionHelper $helper */
        $questionHelper = $this->getHelper('question');
        $interaction = new InteractionHelper($input, $output, $questionHelper);

        $interaction->ensureArgumentIsValid('email', \Closure::fromCallable([$this, 'validateEmail']), 'Please specify the e-mail of the user whose password you wnat to change:');
        $interaction->ensureArgumentIsValid('password', Helpers::getPasswordValidator(), 'Please specify the new password:', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $user = $this->userRepository->findOneBy(['email' => $input->getArgument('email')]);

        if (!$user) {
            $output->writeln('<error>Sorry, no user with the specified e-mail was found!</error>');
            return 1;
        } else {
            $user->setPassword($input->getArgument('password'));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $output->writeln('<info>Password changed successfully.</info>');
            return 0;
        }
    }


    private function validateEmail(?string $email, bool $need = true) : void {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($need) {
                throw new \RuntimeException('Please specify a valid e-mail address');
            }
        } else if (!$this->userRepository->findOneBy(['email' => $email], false)) {
            throw new \RuntimeException('No user with the specified e-mail exists');
        }
    }

}
