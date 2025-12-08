<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\DTOs\RegisterDto;
use App\Services\Auth\UserService;

class RegisterController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(RegisterRequest $request)
    {
        // Преобразуем реквест в DTO
        $dto = RegisterDto::fromRequest($request);

        // Передаём DTO в сервис
        $this->userService->register($dto);

        // Редиректим или возвращаем успешный ответ
        return redirect()->route('login')->with('success', 'Registration successful!');
    }
}

