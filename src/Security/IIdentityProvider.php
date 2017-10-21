<?php

declare(strict_types=1);

namespace App\Security;


interface IIdentityProvider {

    public function findByCredentials(array $credentials) : ?IIdentity;

}
