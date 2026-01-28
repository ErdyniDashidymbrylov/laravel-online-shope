<?php

namespace App\Http\Controllers\Auth;

use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\Auth\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $service
    ) {}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $dto = RegisterDto::fromRequest($request);

        $user = $this->service->register($dto);

        return redirect()
            ->route('login')
            ->with('success', 'Registration was successful!');
    }
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function showProfile(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $dto = UpdateProfileDto::fromRequest($request);
        $this->service->updateProfile($dto);

        return redirect()->route('profile.form');
    }
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $user = Auth::user();

        $this
            ->userService
            ->updatePassword(
                $user,
                $validatedData['current_password'],
                $validatedData['new_password']
            );

        return redirect()
            ->route('profile.form')
            ->with('status', 'Password successfully changed!');
    }

}
