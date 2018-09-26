<?php

declare(strict_types=1);

namespace App\UI;

use Nette\Application\UI\Control;
use Nittro\Bridges\NittroUI\ComponentUtils;


abstract class BaseControl extends Control {
    use ComponentUtils;

    private $view = 'default';


    protected function setView(string $view) : void {
        $this->view = $view;
    }

    protected function getView() : string {
        return $this->view;
    }


    public function render() : void {
        if (!$this->getTemplate()->getFile()) {
            $rc = new \ReflectionClass($this);
            $basepath = dirname($rc->getFileName());
            $this->getTemplate()->setFile($basepath . '/templates/' . $this->getView() . '.latte');
        }

        $this->getTemplate()->render();
    }

}
