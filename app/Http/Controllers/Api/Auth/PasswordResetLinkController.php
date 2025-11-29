<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ApiResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        
        // Находим пользователя
        $user = User::where('email', $request->email)->firstOrFail();

        // Отправляем уведомление с ссылкой на сброс пароля
        $user->notify(new ApiResetPassword());

        return response()->json([
            'message' => 'Ссылка для сброса пароля отправлена на ваш email.',
        ]);
    }
}
