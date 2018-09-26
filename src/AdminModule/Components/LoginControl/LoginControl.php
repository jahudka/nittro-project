<?php

declare(strict_types=1);

namespace App\AdminModule\Components\LoginControl;

use App\UI\BaseControl;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;


class LoginControl extends BaseControl {

    /** @var callable[] */
    public $onLogin = [];

    /** @var User */
    private $user;


    public function __construct(User $user) {
        parent::__construct();
        $this->user = $user;
    }

    private function doLogin(Form $form, array $credentials) : void {
        try {
            $this->user->login($credentials['email'], $credentials['password']);
            $this->user->setExpiration('+1 month');
            $this->onLogin();

        } catch (AuthenticationException $e) {
            $form->addError('Neplatné přihlašovací údaje');
            $this->redrawControl('form');
        }
    }

    public function createComponentForm() : Form {
        $form = new Form();

        $form->addEmail('email', 'E-mail:')->setRequired();
        $form->addPassword('password', 'Heslo:')->setRequired();
        $form->addSubmit('login', 'Přihlásit');

        $form->addProtection();
        $form->onSuccess[] = \Closure::fromCallable([$this, 'doLogin']);

        return $form;
    }

}
