<?php

declare(strict_types=1);

namespace App\UI;

use Nette\Application\UI\Control;


abstract class BaseControl extends Control {

    public function render() : void {
        if (!$this->getTemplate()->getFile()) {
            $reflection = new \ReflectionClass($this);
            $base = dirname($reflection->getFileName());
            $name = lcfirst($reflection->getShortName()) . '.latte';

            $candidates = [
                $base . '/templates/' . $name,
                $base . '/' . $name,
            ];

            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    $this->getTemplate()->setFile($candidate);
                }
            }
        }

        $this->getTemplate()->render();
    }

}
