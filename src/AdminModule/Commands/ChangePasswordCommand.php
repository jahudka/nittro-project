<?php

declare(strict_types=1);

namespace App\AdminModule\Commands;

use App\Console\Helpers;
use App\Console\InteractionHelper;
use App\Models\NoMatchException;
use App\Models\UserModel;
use Dibi\UniqueConstraintViolationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ChangePasswordCommand extends Command {

    /** @var UserModel */
    private $model;


    public function __construct(UserModel $model) {
        parent::__construct();
        $this->model = $model;
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
        try {
            $user = $this->model->get(['email' => $input->getArgument('email')]);

            $this->model->save([
                'id' => $user->id,
                'password_hash' => password_hash($input->getArgument('password'), PASSWORD_DEFAULT),
            ]);

            $output->writeln('<info>Password changed successfully.</info>');
            return 0;

        } catch (NoMatchException $e) {
            $output->writeln('<error>Sorry, no user with the specified e-mail was found!</error>');
            return 1;
        } catch (UniqueConstraintViolationException $e) {
            $output->writeln('<error>Sorry, the e-mail address you provided is already taken!</error>');
            return 1;
        }
    }


    private function validateEmail(?string $email, bool $need = true) : void {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($need) {
                throw new \RuntimeException('Please specify a valid e-mail address');
            }
        } else if (!$this->model->get(['email' => $email], false)) {
            throw new \RuntimeException('No user with the specified e-mail exists');
        }
    }

}
