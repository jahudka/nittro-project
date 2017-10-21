<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Identity;
use Kdyby\Doctrine\EntityManager;


class DoctrineIdentityProvider implements IIdentityProvider {

    private $repository;

    public function __construct(EntityManager $entityManager) {
        $this->repository = $entityManager->getRepository(Identity::class);
    }


    public function findByCredentials(array $credentials) : ?IIdentity {
        if (empty($credentials)) {
            return null;
        }

        [$email] = $credentials;
        return $this->repository->findOneBy(['email' => $email]);
    }

}
