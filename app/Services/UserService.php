<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Request\UserRequestEntity;
use App\Entities\UserEntity;
use App\Exceptions\ValidationException;
use App\Factories\Entity\Request\RequestEntityFactoryInterface;
use App\Factories\Entity\UserEntityFactory;
use App\Repositories\UserRepository;
use App\Validators\UserCreateValidator;
use Psr\Http\Message\ServerRequestInterface;

class UserService
{
    private UserRepository $userRepository;

    private RequestEntityFactoryInterface $requestEntityFactory;

    private UserCreateValidator $userCreateValidator;

    private UserEntityFactory $userEntityFactory;

    public function __construct(
        UserRepository $userRepository,
        RequestEntityFactoryInterface $requestEntityFactory,
        UserCreateValidator $userCreateValidator,
        UserEntityFactory $userEntityFactory
    ) {
        $this->userRepository = $userRepository;
        $this->requestEntityFactory = $requestEntityFactory;
        $this->userCreateValidator = $userCreateValidator;
        $this->userEntityFactory = $userEntityFactory;
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
}