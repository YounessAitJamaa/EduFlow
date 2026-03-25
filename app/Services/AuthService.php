<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data): array
    {
        $user = $this->authRepository->createUser($data);
        $token = Auth::guard('api')->login($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(array $credentials): array
    {
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            throw new Exception("Invalid credentials", 401);
        }

        return [
            'user' => Auth::guard('api')->user(),
            'token' => $token
        ];
    }

    public function me(): ?Authenticatable
    {
        return Auth::guard('api')->user();
    }

    public function logout(): void
    {
        Auth::guard('api')->logout();
    }

    public function forgotPassword(array $data): string
    {
        $status = Password::broker()->sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new Exception(__($status), 400);
        }

        return __($status);
    }

    public function resetPassword(array $data): string
    {
        $status = Password::broker()->reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new Exception(__($status), 400);
        }

        return __($status);
    }
}
