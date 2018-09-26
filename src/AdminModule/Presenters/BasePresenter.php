<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette\Application\Request;
use Nittro\Bridges\NittroUI\Presenter;


abstract class BasePresenter extends Presenter {

    protected $module;


    protected function startup() : void {
        parent::startup();
        $this->setDefaultSnippets(['header', 'content']);

        if (!$this->getUser()->isLoggedIn() && !$this->isPublic()) {
            if (!$this->getRequest()->hasFlag(Request::RESTORED)) {
                $key = $this->storeRequest();
            }

            $this->forward('User:login', ['backlink' => $key ?? null]);
        }
    }


    protected function afterRender() : void {
        parent::afterRender();

        if (!isset($this->module)) {
            $this->module = lcfirst(preg_replace('/^Admin:/', '', $this->getName()));
        }

        $this->template->module = $this->payload->module = $this->module;

        if ($this->isAjax() && ($this->getRequest()->hasFlag(Request::RESTORED) || $this->getRequest()->isMethod(Request::FORWARD))) {
            $this->postGet('this');
        }
    }


    protected function isPublic() : bool {
        return false;
    }

}
