<?php

declare(strict_types=1);

namespace App\Validators;

use App\Entities\Request\RequestEntityInterface;
use App\Entities\UserEntity;
use App\Validators\CustomRules\UniqueRule;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class UserCreateValidator implements ValidatorInterface
{
    private Validator $validator;

    private UniqueRule $uniqueRule;

    private ?Validation $validation = null;

    public function __construct(Validator $validator, UniqueRule $uniqueRule)
    {
        $this->validator = $validator;
        $this->uniqueRule = $uniqueRule;
    }

    public function validate(RequestEntityInterface $userRequestEntity): bool
    {
        $this->validator->addValidator('unique', $this->uniqueRule);

        $uniqueClassname = UserEntity::class;

        $this->validation = $this->validator->make([
            'email' => $userRequestEntity->email,
            'password' => $userRequestEntity->password,
            'confirmPassword' => $userRequestEntity->confirmPassword,
        ], [
            'email' => "required|email|unique:$uniqueClassname,email",
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
        ]);

        $this->validation->validate();

        if ($this->validation->fails()) {
           return false;
        }

        return true;
    }

    public function errors(): array
    {
        $errors = $this->validation->errors();

        return $errors->firstOfAll();
    }
}