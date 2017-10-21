<?php

declare(strict_types=1);

namespace App\Security;

use App\Models\NoMatchException;
use App\Models\UserModel;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;


class SimpleAuthenticator implements IAuthenticator {

    /** @var UserModel */
    private $model;

    public function __construct(UserModel $model) {
        $this->model = $model;
    }

    /**
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials) : IIdentity {
        [$email, $password] = $credentials;

        try {
            $user = $this->model->get(['email' => $email]);

            if (!password_verify($password, $user->password_hash)) {
                throw new AuthenticationException();
            }

            return new Identity($user->id, ['admin'], ['name' => $user->name]);
        } catch (NoMatchException $e) {
            throw new AuthenticationException();
        }
    }

}
