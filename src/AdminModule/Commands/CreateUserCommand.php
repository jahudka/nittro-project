<?php

declare(strict_types=1);

namespace App\AdminModule\Commands;

use App\Console\Helpers;
use App\Console\InteractionHelper;
use App\Entity\Identity;
use Dibi\UniqueConstraintViolationException;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateUserCommand extends Command {

    private $entityManager;

    private $userRepository;


    public function __construct(EntityManager $entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(Identity::class);
    }


    protected function configure() : void {
        $this
            ->setName('user:create')
            ->setDescription('Create a new user account')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the user')
            ->addArgument('email', InputArgument::REQUIRED, 'The e-mail of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
        ;
    }


    protected function interact(InputInterface $input, OutputInterface $output) : void {
        /** @var QuestionHelper $helper */
        $questionHelper = $this->getHelper('question');
        $interaction = new InteractionHelper($input, $output, $questionHelper);

        $interaction->ensureArgumentIsValid('name', \Closure::fromCallable([$this, 'validateUserName']), 'Please specify the name of the new user:');
        $interaction->ensureArgumentIsValid('email', \Closure::fromCallable([$this, 'validateEmail']), 'Please specify the e-mail of the new user:');
        $interaction->ensureArgumentIsValid('password', Helpers::getPasswordValidator(), 'Please specify a password for the new user:', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        try {
            $user = new Identity();
            $user->setEmail($input->getArgument('email'));
            $user->setName($input->getArgument('name'));
            $user->setPassword($input->getArgument('password'));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln('<info>User created.</info>');
            return 0;

        } catch (UniqueConstraintViolationException $e) {
            $output->writeln('<error>Sorry, the e-mail address you provided is already taken!</error>');
            return 1;
        }
    }

    private function validateUserName(?string $name, bool $need = true) : void {
        if (empty($name)) {
            if ($need) {
                throw new \RuntimeException('Name cannot be empty');
            }
        } else if ($this->userRepository->findOneBy(['name' => $name], false)) {
            throw new \RuntimeException('That user already exists');
        }
    }

    private function validateEmail(?string $email, bool $need = true) : void {
        if (empty($email)) {
            if ($need) {
                throw new \RuntimeException('E-mail cannot be empty');
            }
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Please specify a valid e-mail address');
        } else if ($this->userRepository->findOneBy(['email' => $email], false)) {
            throw new \RuntimeException('That e-mail is already taken');
        }
    }

}
