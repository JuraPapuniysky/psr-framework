<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Request\AuthRequestEntity;
use App\Entities\Request\UserRequestEntity;
use App\Entities\SessionEntity;
use App\Entities\UserEntity;
use App\Exceptions\ValidationException;
use App\Factories\Entity\Request\RequestEntityFactoryInterface;
use App\Factories\Entity\SessionEntityFactory;
use App\Factories\Entity\UserEntityFactory;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Validators\AuthValidator;
use App\Validators\UserCreateValidator;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

final class UserService
{
    const MAX_USER_SESSIONS = 5;

    private UserRepository $userRepository;

    private RequestEntityFactoryInterface $requestEntityFactory;

    private UserCreateValidator $userCreateValidator;

    private UserEntityFactory $userEntityFactory;

    private SessionEntityFactory $sessionEntityFactory;

    private AuthValidator $authValidator;

    private SessionRepository $sessionRepository;

    public function __construct(
        UserRepository $userRepository,
        RequestEntityFactoryInterface $requestEntityFactory,
        UserCreateValidator $userCreateValidator,
        UserEntityFactory $userEntityFactory,
        SessionEntityFactory $sessionEntityFactory,
        AuthValidator $authValidator,
        SessionRepository $sessionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->requestEntityFactory = $requestEntityFactory;
        $this->userCreateValidator = $userCreateValidator;
        $this->userEntityFactory = $userEntityFactory;
        $this->sessionEntityFactory = $sessionEntityFactory;
        $this->authValidator = $authValidator;
        $this->sessionRepository = $sessionRepository;
    }

    public function create(ServerRequestInterface $request): UserEntity
    {
        $requestEntity = $this->requestEntityFactory->create($request, UserRequestEntity::class);

        if ($this->userCreateValidator->validate($requestEntity) === false) {
            throw new ValidationException('Validation error', 400);
        }

        $userEntity = $this->userEntityFactory->create($requestEntity);

        $this->userRepository->save($userEntity);

        return $userEntity;
    }

    public function getValidationErrors(): array
    {
        return $this->userCreateValidator->errors();
    }

    public function confirmUser(string $registrationConfirmToken): UserEntity
    {
        $userEntity = $this->userRepository->findUserEntityByRegistrationConfirmToken($registrationConfirmToken);

        if ($userEntity === null) {
            throw new EntityNotFoundException('User not found', 404);
        }

        $userEntity->setIsConfimd(true);
        $this->userRepository->save($userEntity);

        return $userEntity;
    }

    public function authUser(ServerRequestInterface $request): SessionEntity
    {
        $authEntity = $this->requestEntityFactory->create($request, AuthRequestEntity::class);

        if ($this->authValidator->validate($authEntity) === false) {
            throw new ValidationException('Validation error', 400);
        }

        $userEntity = $this->userRepository->findUserEntityByEmail($authEntity->email);

        if ($userEntity === null) {
            throw new EntityNotFoundException('User not found', 404);
        }

        $sessionEntity = $this->sessionRepository->findSessionByUserFingerPrint($userEntity, $authEntity->fingerPrint);

        if ($sessionEntity === null) {
            $userSessions = $this->sessionRepository->findSessionEntitiesByUser($userEntity);

            if (count($userSessions) >= self::MAX_USER_SESSIONS) {
                $this->sessionRepository->delete($userSessions[0]);
            }

            $sessionEntity = $this->sessionEntityFactory->create($authEntity, $userEntity);
            $this->sessionRepository->save($sessionEntity);
        }

        return $sessionEntity;
    }

    public function refreshSession(ServerRequestInterface $request): SessionEntity
    {

    }
}