<?php

declare(strict_types=1);

namespace App\AdminModule\Factories;

use App\AdminModule\Forms\LoginForm;


interface ILoginFormFactory {

    public function create() : LoginForm;

}
