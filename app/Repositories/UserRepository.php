<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\UserEntity;
use Doctrine\Persistence\ObjectRepository;

class UserRepository extends BaseRepository
{
    public function findUserEntityByUuid(string $uuid): ?UserEntity
    {
        /** @var ObjectRepository $repository */
        $repository = $this->entityManager->getRepository(UserEntity::class);

        return $repository->findOneBy([
                'uuid' => $uuid
            ]);
    }
}