<?php

namespace App\Services\Auth;

use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\Models\User;
use Cassandra\Exception\ValidationException;
use Couchbase\AuthenticationException;
use Illuminate\Support\Facades\Auth;
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
    /**
     * @throws AuthenticationException
     */
    public function updateProfile(UpdateProfileDto $dto): void
    {
        $user = Auth::user();
        $user->fill($dto->toArray());
        $user->save();
    }
    /**
     * @throws ValidationException
     */
    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Invalid current password']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }
}


