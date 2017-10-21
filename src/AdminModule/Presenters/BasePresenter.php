<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette\Application\Request;
use Nittro\Bridges\NittroUI\Presenter;


abstract class BasePresenter extends Presenter {

    protected $tab;

    protected function startup() : void {
        parent::startup();

        $this->setDefaultSnippets(['title', 'content']);

        if (!$this->getUser()->isLoggedIn() && !$this->isPublic()) {
            if (!$this->getRequest()->hasFlag(Request::RESTORED)) {
                $key = $this->storeRequest();
            }

            $this->forward('User:login', ['backlink' => $key ?? null]);
        }
    }


    protected function afterRender() : void {
        parent::afterRender();

        if (!isset($this->tab)) {
            $this->tab = lcfirst(preg_replace('/^Admin:/', '', $this->getName()));
        }

        $this->template->tab = $this->payload->tab = $this->tab;

        if ($this->isAjax() && (($restored = $this->getRequest()->hasFlag(Request::RESTORED)) || $this->getRequest()->isMethod(Request::FORWARD))) {
            if ($restored) {
                $this->postGet('this');
            }

            $this->redrawControl('header');
            $this->redrawControl('menu');
        }
    }


    protected function isPublic() : bool {
        return false;
    }

}
