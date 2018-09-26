<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\LoginControl;


class UserPresenter extends BasePresenter {

    /** @var LoginControl\ILoginControlFactory */
    private $loginFormFactory;


    protected function isPublic() : bool {
        return true;
    }

    public function injectLoginFormFactory(LoginControl\ILoginControlFactory $loginFormFactory) : void {
        $this->loginFormFactory = $loginFormFactory;
    }

    public function actionLogout() : void {
        $this->getUser()->logout(true);

        if ($this->isAjax()) {
            $this->forward('login');
        } else {
            $this->redirect('login');
        }
    }

    public function renderLogin(?string $backlink = null) : void {
        $this->module = 'login';

        if ($this->getUser()->isLoggedIn()) {
            if ($backlink) {
                $this->restoreRequest($backlink);
            } else if ($this->isAjax()) {
                $this->forward('Dashboard:');
            } else {
                $this->redirect('Dashboard:');
            }
        }
    }

    public function createComponentLogin() : LoginControl\LoginControl {
        return $this->loginFormFactory->create();
    }

}
