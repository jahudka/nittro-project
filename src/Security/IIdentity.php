<?php

declare(strict_types=1);

namespace App\Security;
use Nette;


interface IIdentity extends Nette\Security\IIdentity {

    public function areCredentialsValid(array $credentials) : bool;

}
