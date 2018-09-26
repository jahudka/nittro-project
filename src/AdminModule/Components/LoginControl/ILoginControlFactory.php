<?php

declare(strict_types=1);

namespace App\AdminModule\Components\LoginControl;


interface ILoginControlFactory {

    public function create() : LoginControl;

}
