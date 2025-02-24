<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function validateUserAndLogin(string $token): User
    {
        $user = User::where('unique_link', $token)->first();

        if (!$user || !$user->isLinkValid()) {
            abort(404, 'Link expired or not found.');
        }

        Auth::login($user);

        return $user;
    }
}
