<?php

namespace App\Services\Auth;

use App\DTOs\RegisterDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function register(RegisterDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->first_name;
        $user->last_name = $dto->last_name;
        $user->email = $dto->email;
        $user->phone = $dto->phone; // если есть такое поле в БД
        $user->password = Hash::make($dto->password);
        $user->save();

        return $user;
    }
}


