<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Factories\ILoginFormFactory;
use App\AdminModule\Forms\LoginForm;


class UserPresenter extends BasePresenter {

    /** @var ILoginFormFactory */
    private $loginFormFactory;


    protected function isPublic() : bool {
        return true;
    }

    public function injectLoginFormFactory(ILoginFormFactory $loginFormFactory) : void {
        $this->loginFormFactory = $loginFormFactory;
    }

    public function actionLogout() : void {
        $this->getUser()->logout(true);

        if ($this->isAjax()) {
            $this->forward('Dashboard:');
        } else {
            $this->redirect('Dashboard:');
        }
    }

    public function renderLogin(?string $backlink = null) : void {
        if ($this->getUser()->isLoggedIn()) {
            if ($backlink) {
                $this->restoreRequest($backlink);
            } else if ($this->isAjax()) {
                $this->forward('Dashboard');
            } else {
                $this->redirect('Dashboard:');
            }
        }
    }

    public function createComponentLogin() : LoginForm {
        return $this->loginFormFactory->create();
    }

}
