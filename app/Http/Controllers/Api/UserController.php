<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\UserResource;
use App\Models\User;

class UserController
{
    public function getAll()
    {
        return UserResource::collection(User::all());
    }

    public function getUser($id)
    {
        return new UserResource(User::findOrFail($id));
    }
}
