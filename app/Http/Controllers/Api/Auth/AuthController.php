<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\StoreRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(StoreRequest $request)
    {
        return User::create($request->all());
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Неправильная почта или пароль'
            ], 401);
        }
        /** @var \app\Http\Models\User $user */
        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'token' => $user->createToken("token of user: {$user->name}")->plainTextToken
        ]);
    }

    public function update(UpdateRequest $request)
    {
        return 'update';
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Вы вышли из личного кабинета'
        ]);
    }
}
